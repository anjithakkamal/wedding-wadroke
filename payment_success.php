<?php
session_start();
include('db_connection.php');

if(!isset($_SESSION['u_id'])){
    die("❌ You must be logged in to place an order!");
}

$user_id = $_SESSION['u_id'];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $product_ids = $_POST['product_id'] ?? [];
    $quantities  = $_POST['quantity'] ?? [];
    $total_price = $_POST['total_price'] ?? 0;
    $address     = $_POST['address'] ?? '';

    if(count($product_ids) == 0){
        die("❌ No items in the order.");
    }

    // Prepare items as JSON to store in orders table
    $items = [];
    for($i=0; $i<count($product_ids); $i++){
        $items[] = [
            'product_id' => $product_ids[$i],
            'quantity' => $quantities[$i]
        ];
    }
    $items_json = json_encode($items);

    // Insert into orders table
    $stmt = $con->prepare("INSERT INTO orders (user_id, items, address, total_price, payment_status, order_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $payment_status = "Paid"; // assuming payment succeeded
    $stmt->bind_param("issds", $user_id, $items_json, $address, $total_price, $payment_status);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert into order_history table
    $stmt2 = $con->prepare("INSERT INTO order_history (user_id, order_id, address, total_price, payment_status, order_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt2->bind_param("iisss", $user_id, $order_id, $address, $total_price, $payment_status);
    $stmt2->execute();
    $stmt2->close();

    // Clear cart
    $con->query("DELETE FROM cart WHERE user_id = $user_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Success - Wedding Wardrobe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Payment Successful!</h5>
      </div>
      <div class="modal-body text-center">
        <p>✅ Your order has been placed successfully.</p>
        <p><strong>Total: ₹<?php echo $total_price; ?></strong></p>
      </div>
      <div class="modal-footer">
        <a href="order_history.php" class="btn btn-primary">View Order History</a>
        <a href="user_home.php" class="btn btn-secondary">Go to Home</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show modal on page load
var successModal = new bootstrap.Modal(document.getElementById('successModal'));
successModal.show();
</script>

</body>
</html>
