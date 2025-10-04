<?php
include("db_connection.php");

// Upload directory
$uploadDir = "uploads/";

// -------------------- ADD PRODUCT --------------------
if(isset($_POST['add_product'])){
    $name = mysqli_real_escape_string($con, $_POST['p_name']);
    $category = mysqli_real_escape_string($con, $_POST['p_category']);
    $quantity = mysqli_real_escape_string($con, $_POST['p_quantity']);
    $price = mysqli_real_escape_string($con, $_POST['p_price']);

    // Handle file upload
    $image = "";
    if(isset($_FILES['p_image']) && $_FILES['p_image']['error'] == 0){
        $image = time() . "_" . basename($_FILES['p_image']['name']);
        move_uploaded_file($_FILES['p_image']['tmp_name'], $uploadDir . $image);
    }

    $sql = "INSERT INTO product (p_name, p_category, p_quantity, p_price, p_image) 
            VALUES ('$name', '$category', '$quantity', '$price', '$image')";
    $msg = mysqli_query($con, $sql) ? "✅ Product added successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- UPDATE PRODUCT --------------------
if(isset($_POST['update_product'])){
    $id = (int)$_POST['p_id'];
    $name = mysqli_real_escape_string($con, $_POST['p_name']);
    $category = mysqli_real_escape_string($con, $_POST['p_category']);
    $quantity = mysqli_real_escape_string($con, $_POST['p_quantity']);
    $price = mysqli_real_escape_string($con, $_POST['p_price']);

    // Handle file upload
    $image_sql = "";
    if(isset($_FILES['p_image']) && $_FILES['p_image']['error'] == 0){
        $image = time() . "_" . basename($_FILES['p_image']['name']);
        move_uploaded_file($_FILES['p_image']['tmp_name'], $uploadDir . $image);
        $image_sql = ", p_image='$image'";
    }

    $sql = "UPDATE product SET p_name='$name', p_category='$category', p_quantity='$quantity', p_price='$price' $image_sql 
            WHERE p_id=$id";
    $msg = mysqli_query($con, $sql) ? "✅ Product updated successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- DELETE PRODUCT --------------------
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM product WHERE p_id=$id";
    $msg = mysqli_query($con, $sql) ? "✅ Product deleted successfully!" : "❌ Error: " . mysqli_error($con);
}

// -------------------- FETCH PRODUCTS --------------------
$products = mysqli_query($con, "SELECT * FROM product ORDER BY p_id DESC");

// -------------------- FETCH CATEGORIES --------------------
$categories = mysqli_query($con, "SELECT * FROM category ORDER BY name ASC");

// -------------------- EDIT PRODUCT --------------------
$editData = null;
if(isset($_GET['edit'])){
    $id = (int)$_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM product WHERE p_id=$id"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wedding Wardrobe - Product Management</title>
<link href="https://fonts.googleapis.com/css2?family=Sacramento&family=Work+Sans:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
body { font-family: 'Work Sans', sans-serif; background: #f8f9fa; margin:0; display:flex; }
.sidebar { width:250px; background:#b76e79; height:100vh; position:fixed; padding:30px 20px; overflow-y:auto; }
.sidebar h2 { font-family:'Sacramento', cursive; color:#fff; font-size:36px; margin-bottom:30px; }
.sidebar a, .sidebar .btn.dropdown-toggle { display:block; width:100%; background:#ffe6ea; color:#b76e79; margin:8px 0; padding:12px 18px; border-radius:10px; text-align:left; font-weight:500; border:none; cursor:pointer; transition:all .2s ease; }
.sidebar a:hover, .sidebar .btn.dropdown-toggle:hover { background:#ffccd5; transform:translateX(5px); }
.dropdown-menu { background:#fff6f6; border:none; width:100%; }
.dropdown-item { color:#b76e79; font-weight:500; }
.dropdown-item:hover { background:#ffe6ea; }
.main-content { margin-left:250px; padding:40px; width:100%; }
h2.text-center { font-family:'Sacramento', cursive; color:#b76e79; font-size:40px; margin-bottom:30px; }
.card { background:#fff; border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-top:20px; padding:20px; }
.card h5 { color:#b76e79; margin-bottom:15px; }
.table th { background:#ffc9d2; color:#b76e79; font-weight:600; }
.table td img { width:50px; height:50px; object-fit:cover; border-radius:5px; }
.modal-header { background:#b76e79; color:#fff; border-bottom:none; }
.modal-footer { border-top:none; }
.modal-content { border-radius:15px; }
.btn-danger { background:#b76e79; border-color:#b76e79; }
.btn-danger:hover { background:#a35d6c; border-color:#a35d6c; }
.btn-warning { background:#ffc107; border-color:#ffc107; }
.btn-warning:hover { background:#e0a800; border-color:#d39e00; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Wedding Wardrobe</h2>
    <a href="admin_dashboard.php">🏠 Home</a>
    <a href="admin_adduser.php">👥 User Management</a>
    <div class="dropdown w-100">
        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">👗 Product</button>
        <div class="dropdown-menu">
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

<div class="main-content container">
    <h2 class="text-center">🛍️ Product Management</h2>

    <?php if(isset($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <button class="btn btn-danger mb-3" data-toggle="modal" data-target="#productModal">
        <?= $editData ? "✏️ Edit Product" : "➕ Add Product" ?>
    </button>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editData ? "Edit Product" : "Add Product" ?></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="p_id" value="<?= $editData['p_id'] ?? '' ?>">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="p_name" class="form-control" value="<?= $editData['p_name'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="p_category" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $cat['name'] ?>" <?= ($editData && $editData['p_category']==$cat['name'])?'selected':'' ?>>
                                        <?= $cat['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="text" name="p_quantity" class="form-control" value="<?= $editData['p_quantity'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="text" name="p_price" class="form-control" value="<?= $editData['p_price'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="p_image" class="form-control">
                            <?php if($editData && $editData['p_image']): ?>
                                <img src="uploads/<?= $editData['p_image'] ?>" class="mt-2" style="width:80px; height:80px; object-fit:cover;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" name="<?= $editData ? 'update_product' : 'add_product' ?>">
                            <?= $editData ? "Update Product" : "Add Product" ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card p-4">
        <h5>📋 Products List</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Category</th><th>Quantity</th><th>Price</th><th>Image</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?= $row['p_id'] ?></td>
                    <td><?= $row['p_name'] ?></td>
                    <td><?= $row['p_category'] ?></td>
                    <td><?= $row['p_quantity'] ?></td>
                    <td><?= $row['p_price'] ?></td>
                    <td>
                        <?php if($row['p_image']): ?>
                            <img src="uploads/<?= $row['p_image'] ?>" style="width:50px; height:50px; object-fit:cover;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?= $row['p_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $row['p_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if($editData): ?>
<script>
$(document).ready(function(){
    $('#productModal').modal('show');
});
</script>
<?php endif; ?>
</body>
</html>
