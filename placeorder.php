<?php
session_start();
include('db_connection.php');
$username = isset($_SESSION['name']) ? $_SESSION['name'] : '';

if(!isset($_SESSION['u_id'])){
    die("You must be logged in to place an order!");
}
$user_id = $_SESSION['u_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Wedding Wardrobe - Place Order</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.container.mt-5 {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.table { background: #fff; border-radius: 10px; overflow: hidden; }
.table th, .table td { vertical-align: middle !important; text-align: center; }
.table th { background-color:#FF6F61; color:#fff; }
.table tbody tr:hover { background-color: #e6f2ff; }
.btn-success { border-radius: 8px; padding: 10px 20px; font-size: 16px; font-weight: 600; }
h2 { margin-bottom: 25px; color: #0077b6; font-weight: 700; }
</style>
</head>
<body style="background-image:url(images/wegging.png);">

<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">
  <div class="container">
    <a class="navbar-brand" href="user_home.php">
      <img src="images/logo.png" width="50" height="50" alt="Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="user_home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="productlist.php">Product List</a></li>
        <?php if($username != ''): ?>
          <li class="nav-item"><a class="nav-link" href="#">Hello, <?php echo htmlspecialchars($username); ?></a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
<?php
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $product_ids = $_POST['product_id'] ?? [];
    $quantities  = $_POST['quantity'] ?? [];
    $total_price = $_POST['total_price'] ?? 0;

    echo "<h2>Order Summary</h2>";

    if(count($product_ids) > 0){
        echo "<form action='confirm_order.php' method='POST'>";
        echo "<table class='table table-bordered text-center'>";
        echo "<thead><tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
              </tr></thead><tbody>";

        for($i=0; $i<count($product_ids); $i++){
            $pid = (int)$product_ids[$i];
            $qty = (int)$quantities[$i];

            // Fetch product info
            $p_sql = mysqli_query($con, "SELECT p_name, p_price, p_image FROM product WHERE p_id = $pid");
            $prod = mysqli_fetch_assoc($p_sql);

            $subtotal = $prod['p_price'] * $qty;

            // hidden fields to pass to confirm_order.php
            echo "<input type='hidden' name='product_id[]' value='$pid'>";
            echo "<input type='hidden' name='quantity[]' value='$qty'>";

            echo "<tr>
                    <td>{$prod['p_name']}</td>
                    <td><img src='uploads/{$prod['p_image']}' width='70'></td>
                    <td>$qty</td>
                    <td>₹{$prod['p_price']}</td>
                    <td>₹$subtotal</td>
                  </tr>";
        }

        echo "<tr>
                <td colspan='4' class='text-end'><strong>Total</strong></td>
                <td><strong>₹$total_price</strong></td>
              </tr>";
        echo "</tbody></table>";

        echo "<input type='hidden' name='total_price' value='$total_price'>";
        echo "</form>"; // close the order summary table form
        
        // ✅ Remove old confirm button and keep only modal trigger
        
    } else {
        echo "<p>No items found in order!</p>";
    }
}
?>
<!-- Confirm button opens modal instead of submitting -->
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addressModal">
  Confirm Order
</button>

<!-- 🔹 Bootstrap Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="payment.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Enter Delivery Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label class="form-label">Full Address</label>
            <textarea name="address" class="form-control" required></textarea>
          </div>
          
          <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" class="form-control" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <!-- Pass order items also -->
          <?php
          for($i=0; $i<count($product_ids); $i++){
              echo "<input type='hidden' name='product_id[]' value='{$product_ids[$i]}'>";
              echo "<input type='hidden' name='quantity[]' value='{$quantities[$i]}'>";
          }
          ?>
          <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
        </div>
        
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
