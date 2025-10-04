<?php
session_start();
include('db_connection.php');

if(isset($_GET['id'])){
    $cart_id = intval($_GET['id']);

    // Get cart item info
    $cart_query = mysqli_query($con, "SELECT * FROM cart WHERE cart_id=$cart_id");
    $cart_item = mysqli_fetch_assoc($cart_query);

    if($cart_item){
        $product_name = mysqli_real_escape_string($con, $cart_item['product']);
        $quantity_in_cart = $cart_item['quantity'];

        // Return quantity back to product stock
        $product_query = mysqli_query($con, "SELECT * FROM product WHERE p_name='$product_name'");
        $product = mysqli_fetch_assoc($product_query);

        if($product){
            $new_stock = $product['p_quantity'] + $quantity_in_cart;
            mysqli_query($con, "UPDATE product SET p_quantity=$new_stock WHERE p_name='$product_name'");
        }

        // Remove item from cart
        mysqli_query($con, "DELETE FROM cart WHERE cart_id=$cart_id");
    }
}

header("Location: cart.php");
exit();
?>
