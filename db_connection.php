<?php
$con = mysqli_connect("localhost", "root", "Password@123", "wedding");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
