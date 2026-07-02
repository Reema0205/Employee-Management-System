<?php
session_start();
require 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Check user in database
$stmt = $connection->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user && password_verify($password, $user['password'])){
    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    
    // Store in session
    $_SESSION['otp'] = $otp;
    $_SESSION['username'] = $username;
    $_SESSION['otp_verified'] = false;
    
    // Show OTP in alert and redirect to otp.php
    echo "
    <script>
        alert('Your OTP is: " . $otp . "');
        window.location.href = 'otp.php';
    </script>";
} else {
    echo "
    <script>
        alert('Invalid username or password!');
        window.location.href = 'login.php';
    </script>";
}
?>