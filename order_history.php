<?php
session_start();
$username = isset($_SESSION['name']) ? $_SESSION['name'] : '';
include('db_connection.php');

if(!isset($_SESSION['u_id'])){
    die("❌ You must be logged in to view your orders!");
}

$user_id = $_SESSION['u_id'];

// Fetch order history for this user
$result = $con->query("SELECT * FROM order_history WHERE user_id = $user_id ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Wedding Wardrobe - My Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Work+Sans:400,600,700" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f4f9; font-family: 'Work Sans', sans-serif; padding-top: 80px; }
h2 { color: #0077b6; margin-bottom: 30px; font-weight: 700; text-align: center; }

/* Navbar adjustments */
.navbar-brand img { border-radius: 50%; }
.navbar-nav .nav-link.active { color: #ffcc00 !important; }

/* Order card */
.card {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Table styling */
.table {
    background: #f9f9f9;
    border-radius: 10px;
    overflow: hidden;
}
.table th {
    background-color: #FF6F61;
    color: #fff;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
}
.table tbody tr:hover { background-color: #e6f2ff; }

/* Product image */
.product-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

/* Payment status badge */
.payment-status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: 600;
    color: #fff;
}
.payment-status.Paid { background-color: #28a745; }
.payment-status.Pending { background-color: #ffc107; color: #212529; }
.payment-status.Failed { background-color: #dc3545; }

/* Order status badge */
.order-status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 12px;
    font-weight: 600;
    color: #fff;
}
.order-status.Pending { background-color: #ffc107; color: #212529; }
.order-status.Cancelled { background-color: #dc3545; }
.order-status.Delivered { background-color: #28a745; }
</style>
</head>
<body style="background-image:url(images/wegging.png);">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">
  <div class="container">
    <a class="navbar-brand" href="user_home.php">
      <img src="images/logo.png" width="50" height="50" alt="Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="user_home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="productlist.php">Product List</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">My Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
        
        <?php if($username != ''): ?>
        <li class="nav-item">
          <a class="nav-link" href="#">Hello, <?php echo htmlspecialchars($username); ?></a>
        </li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Page content -->
<div class="container">
    <h2>My Order History</h2>

    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()):
            $order_id = $row['order_id'];
            $address = htmlspecialchars($row['address']);
            $total_price = number_format($row['total_price'], 2);
            $payment_status = htmlspecialchars($row['payment_status']);
            $order_status = htmlspecialchars($row['order_status']); // get order status
            $order_date = date('d-m-Y H:i', strtotime($row['order_date']));

            // Get items from orders table
            $order_res = $con->query("SELECT items FROM orders WHERE order_id = $order_id");
            $order_data = $order_res->fetch_assoc();
            $items = json_decode($order_data['items'], true);
        ?>
        <div class="card">
            <h5>Order #<?php echo $order_id; ?> | <?php echo $order_date; ?></h5>
            <p><strong>Address:</strong> <?php echo $address; ?></p>
            <p>
                <strong>Payment Status:</strong> 
                <span class="payment-status <?php echo $payment_status; ?>"><?php echo $payment_status; ?></span> 
                | <strong>Order Status:</strong> 
                <span class="order-status <?php echo $order_status; ?>"><?php echo $order_status; ?></span>
                | <strong>Total:</strong> ₹<?php echo $total_price; ?>
            </p>

            <table class="table table-bordered text-center mt-3">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Price (₹)</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item):
                        $pid = $item['product_id'];
                        $qty = $item['quantity'];

                        $p_res = $con->query("SELECT p_name, p_price, p_image FROM product WHERE p_id = $pid");
                        $prod = $p_res->fetch_assoc();
                        $subtotal = $prod['p_price'] * $qty;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prod['p_name']); ?></td>
                        <td><img src="uploads/<?php echo $prod['p_image']; ?>" class="product-img" alt="<?php echo htmlspecialchars($prod['p_name']); ?>"></td>
                        <td><?php echo $qty; ?></td>
                        <td><?php echo number_format($prod['p_price'],2); ?></td>
                        <td><?php echo number_format($subtotal,2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center">No orders found.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
