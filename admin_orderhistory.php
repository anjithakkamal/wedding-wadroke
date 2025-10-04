<?php
session_start();
include('db_connection.php');

// Handle form submission to update order status
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['order_id'], $_POST['status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $stmt = $con->prepare("UPDATE order_history SET order_status=? WHERE order_id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_orderhistory.php");
    exit;
}

// Fetch all orders grouped by status
$pending_res = $con->query("SELECT oh.*, u.name AS user_name 
                             FROM order_history oh 
                             JOIN userreg u ON oh.user_id = u.u_id
                             WHERE oh.order_status IN ('Pending','Cancelled')
                             ORDER BY oh.order_date DESC");

$delivered_res = $con->query("SELECT oh.*, u.name AS user_name 
                              FROM order_history oh 
                              JOIN userreg u ON oh.user_id = u.u_id
                              WHERE oh.order_status='Delivered'
                              ORDER BY oh.order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Order History</title>

<!-- Bootstrap 4 -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
body {
  margin: 0;
  font-family: 'Work Sans', sans-serif;
  background: #fdf0f0;
  display: flex;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background-color: #b76e79;
  color: white;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  overflow-y: auto;
}
.sidebar h2 { font-family: 'Sacramento', cursive; font-size: 36px; margin-bottom: 30px; color: #fff; }
.sidebar a, .sidebar .btn.dropdown-toggle {
  display: block;
  width: 100%;
  background-color: #ffe6ea;
  color: #b76e79;
  text-decoration: none;
  margin: 8px 0;
  padding: 10px 15px;
  font-weight: 500;
  border-radius: 8px;
  transition: background 0.3s ease, transform 0.2s ease;
  text-align: left;
}
.sidebar a:hover, .sidebar .btn.dropdown-toggle:hover {
  background-color: #fddde2;
  transform: translateX(5px);
}
.dropdown-menu { background-color: #fff6f6; border: none; width: 100%; }
.dropdown-item { color: #b76e79; font-weight: 500; }
.dropdown-item:hover { background-color: #ffe6ea; }

/* Main content */
.main-content { margin-left: 250px; padding: 40px; width: 100%; }
h2.page-title { color: #b76e79; margin-bottom: 30px; font-family: 'Sacramento', cursive; }

/* Cards */
.card { background: rgba(255,255,255,0.9); border-radius: 15px; margin-bottom: 25px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
.product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 5px;}
.table th { background-color: #b76e79; color: #fff; text-align: center; }
.table td { text-align: center; }
.payment-status { padding: 5px 12px; border-radius: 12px; font-weight: 600; color: #fff; }
.payment-status.Paid { background-color: #28a745; }
.payment-status.Pending { background-color: #ffc107; color: #212529; }
.payment-status.Failed { background-color: #dc3545; }


body { font-family:'Work Sans', sans-serif; background:#fdf0f0; }
.main-content { margin-left:250px; padding:40px; width:100%; }
.card { background:#fff; border-radius:15px; margin-bottom:25px; padding:20px; box-shadow:0 5px 15px rgba(0,0,0,0.1);}
.product-img { width:80px; height:80px; object-fit:cover; border-radius:5px;}
.table th { background-color:#b76e79; color:#fff; text-align:center; }
.table td { text-align:center; }
.order-status { font-weight:600; padding:5px 10px; border-radius:12px; display:inline-block; }
.order-status.Pending { background:#ffc107; color:#212529; }
.order-status.Cancelled { background:#dc3545; color:#fff; }
.order-status.Delivered { background:#28a745; color:#fff; }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
<h2>Wedding Wardrobe</h2>
<a href="admin_dashboard.php">🏠 Home</a>
<a href="admin_adduser.php">👥 Users</a>
<div class="dropdown w-100">
  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">👗 Products</button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="admin_category.php">🧺 Category</a>
    <a class="dropdown-item" href="admin_outfit.php">🧥 Outfits</a>
  </div>
</div>
<a href="admin_orderhistory.php">🛍️ Orders</a>
<a href="logout.php">🔒 Logout</a>
</div>

<!-- Main content -->
<div class="main-content">
<h2 class="page-title">Order History</h2>

<h4 class="text-warning">Pending / Cancelled Orders</h4>
<?php if($pending_res->num_rows > 0): ?>
    <?php while($order = $pending_res->fetch_assoc()):
        $order_id = $order['order_id'];
        $user_name = htmlspecialchars($order['user_name']);
        $address = htmlspecialchars($order['address']);
        $total_price = number_format($order['total_price'],2);
        $order_status = $order['order_status'];
        $order_date = date('d-m-Y H:i', strtotime($order['order_date']));

        $items_data = $con->query("SELECT items FROM orders WHERE order_id = $order_id");
        $items = json_decode($items_data->fetch_assoc()['items'], true);
    ?>
    <div class="card">
        <h5>Order #<?php echo $order_id; ?> | <?php echo $order_date; ?> | User: <?php echo $user_name; ?></h5>
        <p><strong>Address:</strong> <?php echo $address; ?></p>
        <p>
            <strong>Total:</strong> ₹<?php echo $total_price; ?> |
            <span class="order-status <?php echo $order_status; ?>"><?php echo $order_status; ?></span>
        </p>

        <form method="POST" style="margin-bottom:15px;">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <select name="status" class="form-control" style="width:200px; display:inline-block; margin-right:10px;">
                <option value="Pending" <?php if($order_status=='Pending') echo 'selected'; ?>>Pending</option>
                <option value="Cancelled" <?php if($order_status=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                <option value="Delivered" <?php if($order_status=='Delivered') echo 'selected'; ?>>Delivered</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
        </form>

        <table class="table table-bordered mt-2">
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
                    $prod_res = $con->query("SELECT p_name, p_price, p_image FROM product WHERE p_id = $pid");
                    $prod = $prod_res->fetch_assoc();
                    $subtotal = $prod['p_price'] * $qty;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['p_name']); ?></td>
                    <td><img src="uploads/<?php echo $prod['p_image']; ?>" class="product-img"></td>
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
    <p>No pending or cancelled orders.</p>
<?php endif; ?>

<!-- Delivered Orders -->
<h4 class="text-success mt-5">Delivered Orders</h4>
<?php if($delivered_res->num_rows > 0): ?>
    <?php while($order = $delivered_res->fetch_assoc()):
        $order_id = $order['order_id'];
        $user_name = htmlspecialchars($order['user_name']);
        $address = htmlspecialchars($order['address']);
        $total_price = number_format($order['total_price'],2);
        $order_status = $order['order_status'];
        $order_date = date('d-m-Y H:i', strtotime($order['order_date']));

        $items_data = $con->query("SELECT items FROM orders WHERE order_id = $order_id");
        $items = json_decode($items_data->fetch_assoc()['items'], true);
    ?>
    <div class="card">
        <h5>Order #<?php echo $order_id; ?> | <?php echo $order_date; ?> | User: <?php echo $user_name; ?></h5>
        <p><strong>Address:</strong> <?php echo $address; ?></p>
        <p>
            <strong>Total:</strong> ₹<?php echo $total_price; ?> |
            <span class="order-status <?php echo $order_status; ?>"><?php echo $order_status; ?></span>
        </p>

        <table class="table table-bordered mt-2">
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
                    $prod_res = $con->query("SELECT p_name, p_price, p_image FROM product WHERE p_id = $pid");
                    $prod = $prod_res->fetch_assoc();
                    $subtotal = $prod['p_price'] * $qty;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['p_name']); ?></td>
                    <td><img src="uploads/<?php echo $prod['p_image']; ?>" class="product-img"></td>
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
    <p>No delivered orders.</p>
<?php endif; ?>

</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// AJAX to update order status
$(document).ready(function(){
    $('.status-select').change(function(){
        var order_id = $(this).data('order');
        var status = $(this).val();
        $.post('admin_orderhistory.php', {update_status:1, order_id:order_id, status:status}, function(response){
            if(response.trim()=='success'){
                alert('Order status updated to '+status);
            } else {
                alert('Failed to update order status.');
            }
        });
    });
});
</script>

</body>
</html>
