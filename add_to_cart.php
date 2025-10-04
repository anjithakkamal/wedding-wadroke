<?php
session_start();
include('db_connection.php');

$user_id = $_SESSION['u_id'] ?? 0;
if (!$user_id) die("❌ You must be logged in to add items to cart.");

if (!isset($_POST['p_id'])) die("❌ Invalid request.");

$product_id = intval($_POST['p_id']);

// Fetch product
$product_query = mysqli_query($con, "SELECT * FROM product WHERE p_id=$product_id");
$product = mysqli_fetch_assoc($product_query);

if (!$product) die("❌ Product not found!");
if ($product['p_quantity'] < 1) die("❌ Product out of stock!");

// Product info
$product_name = mysqli_real_escape_string($con, $product['p_name']);
$price = floatval($product['p_price']);

// Check if already in cart
$check = mysqli_query($con, "SELECT * FROM cart WHERE product='$product_name' AND user_id=$user_id");

if (mysqli_num_rows($check) > 0) {
    // Increment quantity
    $row = mysqli_fetch_assoc($check);
    $new_quantity = intval($row['quantity']) + 1;
    $new_total = $price * $new_quantity;

    $update_query = mysqli_query($con, "UPDATE cart SET quantity=$new_quantity, item_total=$new_total WHERE cart_id=".$row['cart_id']);
    if(!$update_query) die("❌ Update failed: " . mysqli_error($con));
} else {
    // Insert new product into cart
    $item_total = $price;
    $insert_query = mysqli_query($con, "INSERT INTO cart(product, quantity, user_id, item_total, product_id)
VALUES('$product_name', 1, $user_id, $item_total, $product_id)");

    if(!$insert_query) die("❌ Insert failed: " . mysqli_error($con));
}

// Reduce product stock
$new_stock = $product['p_quantity'] - 1;
mysqli_query($con, "UPDATE product SET p_quantity=$new_stock WHERE p_id=$product_id");

header("Location: cart.php?added=1");
exit();
?>
