<?php
session_start();
if(!isset($_SESSION['u_id'])){
    die("You must be logged in to confirm order!");
}
$user_id = $_SESSION['u_id'];

// Get posted data
$product_ids = $_POST['product_id'] ?? [];
$quantities  = $_POST['quantity'] ?? [];
$total_price = $_POST['total_price'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Order</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 text-center">
  <h2>Confirm Your Order</h2>
  <p>Please provide your delivery address before proceeding to payment.</p>

  <!-- Address Form -->
  <form action="payment.php" method="POST" class="mt-4" style="max-width:600px; margin:auto;">
      <div class="mb-3">
          <label class="form-label">Full Address</label>
          <textarea name="address" class="form-control" rows="3" required></textarea>
      </div>
      <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" required>
      </div>

      <!-- Hidden order details -->
      <?php
      foreach($product_ids as $i=>$pid){
          echo "<input type='hidden' name='product_id[]' value='".htmlspecialchars($pid)."'>";
          echo "<input type='hidden' name='quantity[]' value='".htmlspecialchars($quantities[$i])."'>";
      }
      ?>
      <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

      <button type="submit" class="btn btn-primary">Proceed to Payment</button>
  </form>
</div>

</body>
</html>
