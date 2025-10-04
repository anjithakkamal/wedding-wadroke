<?php
session_start(); // Start session at the top

// Example: assuming user's name is stored in session as 'username'
$username = isset($_SESSION['name']) ? $_SESSION['name'] : '';

include('db_connection.php'); // Your DB connection

$query = "SELECT * FROM product";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Wedding Wardrobe</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,600,400italic,700' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Sacramento" rel="stylesheet">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- CSS -->
<!-- <link rel="stylesheet" href="css/bootstrap.css"> -->
<link rel="stylesheet" href="css/animate.css">
<link rel="stylesheet" href="css/icomoon.css">
<link rel="stylesheet" href="css/magnific-popup.css">
<link rel="stylesheet" href="css/owl.carousel.min.css">
<link rel="stylesheet" href="css/owl.theme.default.min.css">
<link rel="stylesheet" href="css/style.css">

<style>
.product-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
    transition: 0.3s;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 12px rgba(0,0,0,0.2);
}
.product-card img {
    max-height: 200px;
    margin-bottom: 10px;
}
.product-card h4 {
    font-weight: bold;
}
.product-card p {
    margin: 0;
}
.product-container {
    padding: 50px 15px;
}

.fh5co-nav {
    background: transparent !important;
    box-shadow: none !important;
    position: absolute;
    width: 100%;
    top: 0;
    left: 0;
    z-index: 1000;
}

.fh5co-nav ul li a {
    color: #fff !important; /* White links on transparent bg */
    font-weight: 600;
}

.fh5co-nav ul li a:hover {
    color: #ffcc00 !important; /* Highlight on hover */
}
/* Force Transparent Navbar */
.fh5co-nav {
    background: rgba(255, 255, 255, 0) !important; /* fully transparent */
    box-shadow: none !important;
    border: none !important;
    position: absolute !important;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.fh5co-nav ul li a,
.fh5co-nav ul li a:visited {
    color: #fff !important; /* white text */
}

.fh5co-nav ul li a:hover {
    color: #ffcc00 !important; /* highlight color */
}


</style>
</head>
<body>

<!-- Loader -->
<div class="fh5co-loader"></div>

<div id="page">
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">

  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="user_home.html">
      <img src="images/logo.png" width="75" height="75" alt="Logo">
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
    <li class="nav-item"><a class="nav-link" href="cart.php">My Cart</a></li>
    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>

    <!-- Dropdown -->
    <!-- <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
         data-bs-toggle="dropdown" aria-expanded="false">
        Gallery
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="#">HTML5</a></li>
        <li><a class="dropdown-item" href="#">CSS3</a></li>
        <li><a class="dropdown-item" href="#">Sass</a></li>
        <li><a class="dropdown-item" href="#">jQuery</a></li>
      </ul>
    </li> -->

    <!-- Show logged-in user name -->
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


<header id="fh5co-header" class="fh5co-cover" role="banner" style="background-image:url(images/wegging.png);" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center">
                <div class="display-t">
                    <div class="display-tc animate-box" data-animate-effect="fadeIn">
                        <h1>Wedding Wardrobe</h1>
                        <h2>Our Gallery</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Products Section -->
<div class="container product-container">
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="uploads/<?php echo $row['p_image']; ?>" alt="<?php echo $row['p_name']; ?>" class="img-fluid">
                    <h4><?php echo $row['p_name']; ?></h4>
                    <p>Category: <?php echo $row['p_category']; ?></p>
                    <p>Price: ₹<?php echo $row['p_price']; ?></p>
                    <!-- Button trigger modal -->
                  <!-- Button -->
                  <button type="button" class="btn btn-primary btn-sm mt-2" 
        data-bs-toggle="modal" 
        data-bs-target="#productModal<?php echo $row['p_id']; ?>">
    View Details
</button>



                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="productModal<?php echo $row['p_id']; ?>" tabindex="-1" aria-labelledby="productModalLabel<?php echo $row['p_id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="uploads/<?php echo $row['p_image']; ?>" alt="<?php echo $row['p_name']; ?>" class="img-fluid mb-3">
        <p>Category: <?php echo $row['p_category']; ?></p>
        <!-- <p>Quantity: <?php echo $row['p_quantity']; ?></p> -->
        <p>Price: ₹<?php echo $row['p_price']; ?></p>
      </div>
      <div class="modal-footer">
      <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="p_id" value="<?php echo $row['p_id']; ?>">
                    <button type="submit" class="btn btn-success" <?php echo ($row['p_quantity'] < 1) ? 'disabled' : ''; ?>>
                        Add to Cart
                    </button>
                </form>

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

        <?php } ?>
    </div>
</div>

<!-- JS -->
<!-- <script src="js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.easing.1.3.js"></script>
<script src="js/jquery.waypoints.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.countTo.js"></script>
<script src="js/jquery.stellar.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/magnific-popup-options.js"></script>
<script src="js/simplyCountdown.js"></script>
<script src="js/main.js"></script>

</body>
</html>
