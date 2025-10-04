<?php
include("db_connection.php"); // make sure this file connects to your DB
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form values safely
    $fullname   = mysqli_real_escape_string($con, $_POST['fullname']);
    $email      = mysqli_real_escape_string($con, $_POST['email']);
    $phone      = mysqli_real_escape_string($con, $_POST['phone']);
    $gender     = mysqli_real_escape_string($con, $_POST['gender']);
    $address    = mysqli_real_escape_string($con, $_POST['address']);
    $password   = mysqli_real_escape_string($con, $_POST['password']);

    // Hash password (for security)
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_query($con, "SELECT * FROM userreg WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Email already registered!'); window.location='registration.html';</script>";
        exit();
    }

    // Insert into database
    $sql = "INSERT INTO userreg (name, email, phone, gender, address, password) 
            VALUES ('$fullname', '$email', '$phone', '$gender', '$address', '$password')";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('✅ Registration Successful!'); window.location='login.html';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
