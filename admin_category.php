<?php
include("db_connection.php");

// -------------------- ADD CATEGORY --------------------
if(isset($_POST['add_category'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $sql = "INSERT INTO category (name) VALUES ('$name')";
    $msg = mysqli_query($con, $sql) ? "✅ Category added successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- UPDATE CATEGORY --------------------
if(isset($_POST['update_category'])){
    $id = $_POST['c_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $sql = "UPDATE category SET name='$name' WHERE c_id=$id";
    $msg = mysqli_query($con, $sql) ? "✅ Category updated successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- DELETE CATEGORY --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "DELETE FROM category WHERE c_id=$id";
    $msg = mysqli_query($con, $sql) ? "✅ Category deleted successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- FETCH CATEGORIES --------------------
$categories = mysqli_query($con, "SELECT * FROM category ORDER BY c_id DESC");

// -------------------- EDIT CATEGORY --------------------
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM category WHERE c_id=$id"));
}
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
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      overflow-y: auto; /* scroll if content exceeds */
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
      padding: 10px 15px;
      font-weight: 500;
      border-radius: 8px;
      transition: background 0.3s ease, transform 0.2s ease;
      text-align: left;
      border: none;
      cursor: pointer;
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
      background: rgba(255, 255, 255, 0.9);
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      margin-top: 20px;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .table th {
      background-color: #ffc9d2;
      color: #b76e79;
    }

    .modal-header {
      background-color: #b76e79;
      color: #fff;
      border-bottom: none;
    }

    .modal-footer {
      border-top: none;
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

  <div class="dropdown w-100">
    <button class="btn dropdown-toggle" type="button" id="productDropdown" data-toggle="dropdown">
      👗 Product
    </button>
    <div class="dropdown-menu" aria-labelledby="productDropdown">
      <a class="dropdown-item" href="admin_category.php">🧺 Category</a>
      <a class="dropdown-item" href="admin_outfit.php">🧥 Outfits</a>
    </div>
  </div>
  <a href="admin_orderhistory.php">🛍️ Orders</a>
  <!-- <a href="#">📅 Calendar</a>
  <a href="#">📸 Gallery</a>
  <a href="#">🛍️ Vendors</a>
  <a href="#">💬 Feedback</a> -->
  <a href="logout.php">🔒 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content container">
  <h2 class="dashboard-heading text-center">📂 Category Management</h2>

  <?php if(isset($msg)): ?>
  <div class="alert alert-info"><?= $msg ?></div>
  <?php endif; ?>

  <!-- Add/Edit Button -->
  <button class="btn btn-danger mb-3" data-toggle="modal" data-target="#categoryModal">
      <?= $editData ? "✏️ Edit Category" : "➕ Add Category" ?>
  </button>

  <!-- Category Modal -->
  <div class="modal fade" id="categoryModal" tabindex="-1">
      <div class="modal-dialog">
          <div class="modal-content">
              <form method="POST">
                  <div class="modal-header">
                      <h5 class="modal-title"><?= $editData ? "Edit Category" : "Add Category" ?></h5>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" name="c_id" value="<?= $editData['c_id'] ?? '' ?>">
                      <div class="form-group">
                          <label>Category Name</label>
                          <input type="text" name="name" class="form-control" value="<?= $editData['name'] ?? '' ?>" required>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="submit" class="btn btn-danger" name="<?= $editData ? 'update_category' : 'add_category' ?>">
                          <?= $editData ? "Update Category" : "Add Category" ?>
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- Category List Table -->
  <div class="card p-4">
      <h5>📋 Categories List</h5>
      <table class="table table-bordered table-striped">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Category Name</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
          <?php while($row = mysqli_fetch_assoc($categories)): ?>
              <tr>
                  <td><?= $row['c_id'] ?></td>
                  <td><?= $row['name'] ?></td>
                  <td>
                      <a href="?edit=<?= $row['c_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                      <a href="?delete=<?= $row['c_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                  </td>
              </tr>
          <?php endwhile; ?>
          </tbody>
      </table>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if($editData): ?>
<script>
$(document).ready(function(){
    $('#categoryModal').modal('show');
});
</script>
<?php endif; ?>

</body>
</html>
