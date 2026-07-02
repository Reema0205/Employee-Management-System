<?php
session_start();

if(!isset($_SESSION['otp'])){
    header('Location: login.php');
    exit;
}

$enteredOtp = $_POST['otp'];
$sessionOtp = $_SESSION['otp'];

if($enteredOtp == $sessionOtp){
    $_SESSION['otp_verified'] = true;
    header('Location: index.html');
    exit;
} else {
    $_SESSION['error'] = 'Wrong OTP! Please try again.';
    header('Location: otp.php');
    exit;
}
?>