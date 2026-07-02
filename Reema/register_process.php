<?php
session_start();
require 'config.php';

$fullname = $_POST['fullname'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
$role = $_POST['role'];

// Check passwords match
if($password !== $confirmPassword){
    echo "<script>alert('Passwords do not match!'); window.location.href='Register.html';</script>";
    exit;
}

// Check if username already exists
$check = $connection->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    echo "<script>alert('Username or Email already exists!'); window.location.href='Register.html';</script>";
    exit;
}

// Hash password and insert
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $connection->prepare("INSERT INTO admins (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullname, $username, $email, $hashed, $role);

if($stmt->execute()){
    echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Registration Failed! Try again.'); window.location.href='Register.html';</script>";
}
?>