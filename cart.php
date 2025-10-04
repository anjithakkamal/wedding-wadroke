<?php
session_start();
$username = isset($_SESSION['name']) ? $_SESSION['name'] : '';
include('db_connection.php');

$user_id = $_SESSION['u_id'] ?? 0;
if(!$user_id) die("You must be logged in to view your cart.");

// Fetch cart items with product info
$query = mysqli_query($con, "
    SELECT c.*, p.p_name, p.p_image, p.p_price 
    FROM cart c
    JOIN product p ON p.p_id = c.product_id
    WHERE c.user_id = $user_id
");

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Wedding Wardrobe - My Cart</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,600,400italic,700' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css?family=Sacramento" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<style>
/* Container */
.container.mt-5 {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Table styling */
.table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}

.table th, .table td {
    vertical-align: middle !important;
    text-align: center;
}

.table th {
    background-color:#FF6F61; /* Coral / orange-pink shade */
    color: #fff; /* Keep text white for contrast */
}


.table tbody tr:hover {
    background-color: #e6f2ff;
}

/* Remove button */
.btn-danger {
    border-radius: 5px;
    padding: 5px 10px;
}

/* Checkout button */
.btn-success {
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 600;
}

/* Heading */
h2 {
    margin-bottom: 25px;
    color: #0077b6;
    font-weight: 700;
}

/* Total row */
.table tfoot tr td {
    font-weight: bold;
    font-size: 18px;
}

/* Navbar adjustments */
.fh5co-nav {
    background: rgba(0,0,0,0.5);
    padding: 10px 0;
}
.fh5co-nav ul li a {
    color: #fff !important;
}
.fh5co-nav ul li a:hover {
    color: #ffcc00 !important;
}

/* Product images in table */
.cart-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}
</style>
</head>
<body style="background-image:url(images/wegging.png);">

<div class="fh5co-loader"></div>

<div id="page">
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="user_home.php">
      <img src="images/logo.png" width="50" height="50" alt="Logo">
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="user_home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="productlist.php">Product List</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php"> My Cart</a></li>
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

<!-- Add spacing below navbar so content is not hidden -->
<div class="container mt-5 pt-5 my-4">
<h2 class="mb-4" style="color: #FF7F50;">My Cart</h2>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead style="background-color: #0077b6; color: white;">
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($query)) {
                $total_price += $row['item_total'];
            ?>
                <tr style="background-color: #e6f2ff;">
                    <td><?php echo htmlspecialchars($row['p_name']); ?></td>
                    <td><img src="uploads/<?php echo $row['p_image']; ?>" class="cart-img"></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>₹<?php echo $row['p_price']; ?></td>
                    <td>₹<?php echo $row['item_total']; ?></td>
                    <td>
                        <a href="remove_from_cart.php?id=<?php echo $row['cart_id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    </td>
                </tr>
            <?php } ?>
                <tr style="background-color: #cce5ff;">
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td colspan="2"><strong>₹<?php echo $total_price; ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <form action="placeorder.php" method="POST">
    <?php 
    // Loop cheyyumbo cart_id or product_id array pass cheyyam
    mysqli_data_seek($query, 0); // reset query pointer
    while($row = mysqli_fetch_assoc($query)) {
        echo '<input type="hidden" name="product_id[]" value="'.$row['product_id'].'">';
        echo '<input type="hidden" name="quantity[]" value="'.$row['quantity'].'">';
    }
    ?>
    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
    <button type="submit" class="btn btn-success btn-lg">Place Order</button>
</form>

</div>

<style>
.cart-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

/* Add spacing under navbar */
body {
    padding-top: 90px; /* adjust to navbar height */
}

/* Hover effect for table rows */
.table tbody tr:hover {
    background-color: #d0e7ff;
    transition: 0.3s;
}

.table th, .table td {
    vertical-align: middle !important;
}
</style>



<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/jquery.stellar.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</body>
</html> 