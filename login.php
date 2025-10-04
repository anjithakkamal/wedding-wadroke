<?php
session_start();
require_once 'db_connection.php'; // $con

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if ($role === "admin") {
        // Hardcoded admin credentials
        $admin_email = "admin@gmail.com";
        $admin_pass = "admin123";

        if ($email === $admin_email && $password === $admin_pass) {
            $_SESSION['role'] = "admin";
            $_SESSION['admin_name'] = "Super Admin";
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "❌ Invalid Admin credentials.";
        }

    } elseif ($role === "user") {
        // Check patient login from DB
        $query = "SELECT * FROM userreg WHERE email='$email' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if ($user['password'] === $password) {
                $_SESSION['role'] = "user";
                $_SESSION['u_id'] = $user['u_id']; 
                $_SESSION['name'] = $user['name'];

                header("Location: user_home.php");
                exit;
            } else {
                echo "❌ Invalid user password.";
            }
        } else {
            echo "❌ No patient found with that email.";
        }
    }
}
?>