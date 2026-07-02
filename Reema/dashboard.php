<?php
session_start();

if(!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true){
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EMS - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
        }
        .navbar {
            background: mediumblue;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h2 { font-size: 20px; }
        .logout-btn {
            background: white;
            color: mediumblue;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .welcome {
            text-align: center;
            margin-top: 80px;
        }
        .welcome i {
            font-size: 80px;
            color: mediumblue;
            margin-bottom: 20px;
        }
        .welcome h1 {
            color: darkblue;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .welcome p {
            color: #555;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-users"></i> Employee Management System</h2>
        <button class="logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>

    <div class="welcome">
        <i class="fas fa-check-circle"></i>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>You have successfully logged in to the Employee Management System.</p>
    </div>
</body>
</html>