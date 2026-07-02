<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EMS - Enter OTP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 50%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container { width: 100%; max-width: 450px; text-align: center; }
        .logo { font-size: 60px; margin-bottom: 15px; }
        .logo .fa-users {
            background: linear-gradient(90deg, #3b82f6 0%, #f97316 50%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h1 { color: darkblue; font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .subtitle { color: #000; font-size: 18px; margin-bottom: 30px; }
        .card {
            background: white;
            border-radius: 12px;
            padding: 35px 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: left;
        }
        .form-icon { text-align: center; margin-bottom: 20px; }
        .form-icon i {
            font-size: 35px; color: mediumblue;
            background: #eef4ff; padding: 18px; border-radius: 50%;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-wrapper .icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); font-size: 14px;
        }
        .input-wrapper input {
            width: 100%; padding: 12px 12px 12px 40px;
            border: 1px solid #e2e8f0; border-radius: 6px;
            font-size: 18px; letter-spacing: 6px;
            background: #f8fafc; text-align: center;
        }
        .input-wrapper input:focus {
            outline: none; border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .btn {
            width: 100%; padding: 12px; border: none;
            border-radius: 6px; font-size: 15px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
        }
        .btn-verify { background: mediumblue; color: white; margin-bottom: 12px; }
        .btn-verify:hover { background: #2563eb; }
        .btn-back { background: #e5e7eb; color: #333; }
        .btn-back:hover { background: #d1d5db; }
        .error { color: red; text-align: center; margin-bottom: 15px; font-weight: 500; }
        footer { margin-top: 25px; color: #000; font-size: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo"><i class="fas fa-users"></i></div>
    <h1><b>EMPLOYEE MANAGEMENT SYSTEM</b></h1>
    <p class="subtitle">OTP Verification</p>

    <div class="card">
        <div class="form-icon"><i class="fas fa-shield-alt"></i></div>

        <?php if(isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="verify_otp.php" method="POST">
            <div class="form-group">
                <label><b>Enter the 6-digit OTP shown in the alert</b></label>
                <div class="input-wrapper">
                    <i class="fas fa-key icon"></i>
                    <input type="text" name="otp" maxlength="6" placeholder="______" required autofocus>
                </div>
            </div>
            <button type="submit" class="btn btn-verify">VERIFY OTP</button>
            <button type="button" class="btn btn-back" onclick="window.location.href='login.php'">BACK TO LOGIN</button>
        </form>
    </div>
    <footer><p>© 2026 EMS. All rights reserved.</p></footer>
</div>
</body>
</html>