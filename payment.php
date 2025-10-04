<?php
session_start();
include('db_connection.php');

if(!isset($_SESSION['u_id'])){
    die("❌ You must be logged in to continue payment!");
}

$user_id = $_SESSION['u_id'];

// Get order data from POST
$product_ids = $_POST['product_id'] ?? [];
$quantities  = $_POST['quantity'] ?? [];
$total_price = $_POST['total_price'] ?? 0;

$address = $_POST['address'] ?? '';
$city    = $_POST['city'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$phone   = $_POST['phone'] ?? '';

// Save order temporarily in session
$_SESSION['pending_order'] = [
    'product_ids' => $product_ids,
    'quantities'  => $quantities,
    'total_price' => $total_price,
    'address'     => $address,
    'city'        => $city,
    'pincode'     => $pincode,
    'phone'       => $phone
];

$order = $_SESSION['pending_order'];
$total_price = $order['total_price'];
$address_str = htmlspecialchars($order['address'] . ', ' . $order['city'] . ' - ' . $order['pincode']);
$phone = htmlspecialchars($order['phone']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment Demo - Wedding Wardrobe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card { max-width:700px; margin:auto; }
.small-note { font-size:0.9rem; color:#6c757d; }
</style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="mb-3 text-center">Payment (Demo)</h3>
    <p class="text-center">Amount: <strong>₹<?php echo $total_price; ?></strong></p>
    <p class="small-note text-center">This is a demo — do not enter real card details.</p>

    <form action="payment_success.php" method="POST" class="text-center">
      <!-- Delivery summary -->
      <div class="mb-3">
        <label class="form-label"><strong>Delivery</strong></label>
        <div class="form-control"><?php echo $address_str; ?> — Phone: <?php echo $phone; ?></div>
      </div>

      <!-- Card fields -->
      <div class="mb-3">
        <label class="form-label">Name on Card</label>
        <input type="text" name="card_name" class="form-control" placeholder="Full name on card">
      </div>

      <div class="mb-3">
        <label class="form-label">Card Number</label>
        <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Expiry (MM/YY)</label>
          <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">CVV</label>
          <input type="text" name="card_cvc" class="form-control" placeholder="CVV">
        </div>
      </div>

      <!-- Hidden fields for order info -->
      <?php
      foreach($product_ids as $i => $pid){
          echo '<input type="hidden" name="product_id[]" value="'.$pid.'">';
          echo '<input type="hidden" name="quantity[]" value="'.$quantities[$i].'">';
      }
      ?>
      <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
      <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
      <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
      <input type="hidden" name="pincode" value="<?php echo htmlspecialchars($pincode); ?>">
      <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

      <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg me-2">💳 Pay Now</button>
        <a href="payment_failed.php" class="btn btn-outline-danger btn-lg">Cancel</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>
