<?php
include("db_connection.php");

// Fetch counts
$totalProducts = $con->query("SELECT COUNT(*) as count FROM product")->fetch_assoc()['count'];
$totalOrders   = $con->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalUsers    = $con->query("SELECT COUNT(*) as count FROM userreg")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Wedding Wardrobe Dashboard</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Sacramento&family=Work+Sans:wght@300;400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    body {
      margin: 0;
      font-family: 'Work Sans', sans-serif;
      background: linear-gradient(to right, #fbecec, #fff6f6);
      display: flex;
    }

    /* Sidebar */
    .sidebar {
  width: 250px;
  background-color: #b76e79;
  color: white;
  height: 100vh;       /* full viewport height */
  position: fixed;
  top: 0;
  left: 0;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;

  overflow-y: auto;    /* enable vertical scroll */
  scrollbar-width: thin; /* optional, for Firefox */
}


    .sidebar h2 {
      font-family: 'Sacramento', cursive;
      font-size: 36px;
      margin-bottom: 30px;
      color: #fff;
    }

    .sidebar a,
.sidebar .btn.dropdown-toggle {
  display: block;
  width: 100%;
  background-color: #ffe6ea;
  color: #b76e79;
  text-decoration: none;
  margin: 8px 0;
  padding: 12px 18px;
  font-weight: 600;
  border-radius: 10px;
  transition: background 0.3s ease, transform 0.2s ease;
  text-align: center;   /* Center text like button */
  border: none;         /* Remove border */
  cursor: pointer;      /* Pointer on hover */
}

.sidebar a:hover,
.sidebar .btn.dropdown-toggle:hover {
  background-color: #ffccd5;
  color: #8a3d4b;
  transform: scale(1.03);  /* small pop effect */
}
.sidebar a,
    .sidebar .btn.dropdown-toggle {
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

    .sidebar a:hover,
    .sidebar .btn.dropdown-toggle:hover {
      background-color: #fddde2;
      transform: translateX(5px);
    }

    .dropdown-menu {
      background-color: #fff6f6;
      border: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 100%;
    }

    .dropdown-item {
      color: #b76e79;
      font-weight: 500;
    }

    .dropdown-item:hover {
      background-color: #ffe6ea;
    }

    /* Main Content */
    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: 100%;
    }

    .dashboard-heading {
      font-family: 'Sacramento', cursive;
      font-size: 48px;
      color: #b76e79;
      margin-bottom: 30px;
    }

    .card {
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-title {
      font-weight: 600;
      color: #b76e79;
    }

    .card-text {
      font-size: 14px;
      color: #555;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        justify-content: space-around;
        padding: 10px;
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      .dashboard-heading {
        font-size: 36px;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
  <h2>Wedding Wardrobe</h2>
  <a href="admin_dashboard.php">🏠 Home</a>
  <a href="admin_adduser.php">👥 User Management</a>

  <!-- Product Management Dropdown -->
  <div class="dropdown w-100">
    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      👗 Product
    </button>
    <div class="dropdown-menu" aria-labelledby="userDropdown">
      <a class="dropdown-item" href="admin_category.php">🧺 Category</a>
      <a class="dropdown-item" href="admin_outfit.php">🧥 Outfits</a>
    </div>
  </div>

  <a href="admin_orderhistory.php">🛍️ Order History</a>
  <!-- <a href="#">📸 Gallery</a> -->
  <!-- <a href="#">🛍️ Vendors</a> -->
  <!-- <a href="#">💬 Feedback</a> -->
  <a href="logout.php">🔒 Logout</a>
</div>


  <!-- Main Content -->
  <div class="main-content">
  <h1 class="dashboard-heading">Welcome, Admin 💫</h1>

  <div class="row">
    <!-- Total Products -->
    <div class="col-md-4 mb-4">
      <div class="card p-4 shadow-sm text-center">
        <h5 class="card-title">👗 Total Products</h5>
        <h2><?php echo $totalProducts; ?></h2>
        <p class="card-text">Add, edit, and organize your wedding outfits.</p>
        <a href="admin_outfit.php" class="btn btn-outline-danger btn-sm">Manage</a>
      </div>
    </div>

    <!-- Total Orders -->
    <div class="col-md-4 mb-4">
      <div class="card p-4 shadow-sm text-center">
        <h5 class="card-title">📦 Total Orders</h5>
        <h2><?php echo $totalOrders; ?></h2>
        <p class="card-text">Track all bookings with reminders.</p>
        <a href="admin_orderhistory.php" class="btn btn-outline-danger btn-sm">View Orders</a>
      </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-4 mb-4">
      <div class="card p-4 shadow-sm text-center">
        <h5 class="card-title">👤 Total Users</h5>
        <h2><?php echo $totalUsers; ?></h2>
        <p class="card-text">Manage registered users easily.</p>
        <a href="admin_adduser.php" class="btn btn-outline-danger btn-sm">View Users</a>
      </div>
    </div>
  </div>
</div>


  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>