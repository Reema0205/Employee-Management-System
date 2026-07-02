<?php
require 'config.php';

$username = $_POST['username'];
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

if($newPassword !== $confirmPassword){
    echo "<script>alert('Passwords do not match!'); window.location.href='Reset.html';</script>";
    exit;
}

// Check if user exists
$check = $connection->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $username);
$check->execute();
$check->store_result();

if($check->num_rows === 0){
    echo "<script>alert('Username or Email not found!'); window.location.href='Reset.html';</script>";
    exit;
}

// Update password
$hashed = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $connection->prepare("UPDATE admins SET password = ? WHERE username = ? OR email = ?");
$stmt->bind_param("sss", $hashed, $username, $username);

if($stmt->execute()){
    echo "<script>alert('Password reset successful!'); window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Failed to reset password!'); window.location.href='Reset.html';</script>";
}

$connection->close();
?>