<?php
include("db_connection.php");

// -------------------- INSERT USER --------------------
if(isset($_POST['add_user'])){
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $phone    = mysqli_real_escape_string($con, $_POST['phone']);
    $gender   = $_POST['gender'];
    $address    = mysqli_real_escape_string($con, $_POST['address']);
// Insert user
$password = mysqli_real_escape_string($con, $_POST['password']); // plain text
$sql = "INSERT INTO userreg (name,email,phone,gender,address,password)
        VALUES ('$fullname','$email','$phone','$gender','$address','$password')";
    $msg = mysqli_query($con, $sql) ? "✅ User added successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- UPDATE USER --------------------
if(isset($_POST['update_user'])){
    $id = $_POST['u_id'];
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $phone    = mysqli_real_escape_string($con, $_POST['phone']);
    
    $gender   = $_POST['gender'];
    $address    = mysqli_real_escape_string($con, $_POST['address']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if(!empty($_POST['password'])){
        $password = mysqli_real_escape_string($con, $_POST['password']); // plain text
        $sql = "UPDATE userreg SET name='$fullname', email='$email', phone='$phone', gender='$gender', address='$address', password='$password' WHERE u_id=$id";
    } else {
        $sql = "UPDATE userreg SET name='$fullname', email='$email', phone='$phone', gender='$gender', address='$address' WHERE u_id=$id";
    }

    $msg = mysqli_query($con, $sql) ? "✅ User updated successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- FETCH USERS --------------------
$users = mysqli_query($con, "SELECT * FROM userreg ORDER BY u_id DESC");

// -------------------- EDIT DATA --------------------
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM userreg WHERE u_id=$id"));
}

// -------------------- DELETE USER --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM userreg WHERE u_id=$id";
    $msg = mysqli_query($con, $delete_sql) ? "✅ User deleted successfully!" : "❌ Error: " . mysqli_error($con);
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

  <!-- <a href="#">📅 Calendar</a> -->
  <!-- <a href="#">📸 Gallery</a> -->
  <a href="admin_orderhistory.php">🛍️ Orders</a>
  <!-- <a href="#">💬 Feedback</a> -->
  <a href="logout.php">🔒 Logout</a>
</div>

  <!-- Main Content -->
  <div class="main-content">
    <h2 class="dashboard-heading">👥 User Management</h2>

    <?php if(isset($msg)): ?>
    <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <button type="button" class="btn btn-danger mb-3" data-toggle="modal" data-target="#userModal">
      <?= $editData ? "✏️ Edit User" : "➕ Add User" ?>
    </button>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title"><?= $editData ? "Edit User" : "Add New User" ?></h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="u_id" value="<?= $editData['u_id'] ?? '' ?>">
              <div class="form-group"><label>Full Name</label><input type="text" name="fullname" class="form-control" value="<?= $editData['name'] ?? '' ?>" required></div>
              <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $editData['email'] ?? '' ?>" required></div>
            <div class="form-group"><label>Phone</label><input type="tel" name="phone" class="form-control" value="<?= $editData['phone'] ?? '' ?>" required></div>
              <div class="form-group">
                <label>Gender</label><br>
                <label><input type="radio" name="gender" value="Male" <?= ($editData && $editData['gender']=='Male')?'checked':'' ?>> Male</label>
                <label><input type="radio" name="gender" value="Female" <?= ($editData && $editData['gender']=='Female')?'checked':'' ?>> Female</label>
                <label><input type="radio" name="gender" value="Other" <?= ($editData && $editData['gender']=='Other')?'checked':'' ?>> Other</label>
              </div>
              <div class="form-group"><label>Address</label><input type="text" name="address" class="form-control" value="<?= $editData['address'] ?? '' ?>" required></div>

              <div class="form-group">
                <label>Password <?= $editData ? "(Leave blank to keep current)" : "" ?></label>
                <input type="password" name="password" class="form-control">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" name="<?= $editData ? 'update_user' : 'add_user' ?>">
                <?= $editData ? 'Update User' : 'Add User' ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- User List Table -->
    <div class="card p-4 shadow">
      <h5>📋 Users List</h5>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Gender</th><th>Address</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= $row['u_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['gender'] ?></td>
            <td><?= $row['address'] ?></td>
          
            <!-- <td>
              <a href="?edit=<?= $row['u_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            </td> -->
            <td>
  <a href="?edit=<?= $row['u_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
  <a href="?delete=<?= $row['u_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
      $('#userModal').modal('show');
    });
  </script>
  <?php endif; ?>

</body>
</html>