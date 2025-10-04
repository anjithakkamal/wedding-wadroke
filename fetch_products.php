<?php
include 'db_connection.php';
$category = $_GET['category'];

$sql = ($category === "All") 
    ? "SELECT * FROM product" 
    : "SELECT * FROM product WHERE p_category = '$category'";

$result = $conn->query($sql);
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>