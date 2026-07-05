<?php
// login.php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Already logged in → go to dashboard
if (!empty($_SESSION['company_id'])) {
    header('Location: dashboard.php'); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter both username/email and password.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id, name, email, password FROM companies WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['company_id'] = $row['id'];
                $_SESSION['company']    = ['name' => $row['name'], 'email' => $row['email']];
                session_regenerate_id(true);
                $stmt->close(); $db->close();
                header('Location: dashboard.php'); exit;
            }
        }
        $stmt->close(); $db->close();
        $error = 'Invalid username/email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IPS  System – Company Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",Arial,sans-serif;}
    body{background:linear-gradient(135deg,#071522 0%,#0b3b32 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
    .login-wrapper{display:flex;width:900px;max-width:100%;border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.4);}
    .login-left{background:linear-gradient(180deg,#0d2a1e 0%,#18b65a22 100%);width:380px;padding:50px 40px;display:flex;flex-direction:column;justify-content:center;border-right:1px solid rgba(255,255,255,0.08);flex-shrink:0;}
    .brand{display:flex;align-items:center;gap:14px;margin-bottom:50px;}
    .brand-icon{width:50px;height:50px;background:#18b65a;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;}
    .brand h1{font-size:26px;font-weight:800;color:#fff;}.brand p{font-size:12px;color:#86efac;}
    .left-features h3{font-size:20px;color:#fff;font-weight:700;margin-bottom:24px;}
    .feature-item{display:flex;align-items:flex-start;gap:14px;margin-bottom:20px;}
    .feature-item .fi-icon{width:38px;height:38px;background:rgba(24,182,90,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#4ade80;font-size:15px;flex-shrink:0;}
    .feature-item h4{font-size:14px;font-weight:600;color:#e2e8f0;margin-bottom:3px;}
    .feature-item p{font-size:12px;color:#94a3b8;line-height:1.5;}
    .login-right{flex:1;background:#fff;padding:50px 44px;display:flex;flex-direction:column;justify-content:center;}
    .login-right h2{font-size:26px;font-weight:700;color:#111827;margin-bottom:6px;}
    .login-right .subtitle{font-size:14px;color:#6b7280;margin-bottom:36px;}
    .form-group{margin-bottom:20px;}
    .form-group label{display:block;font-size:13.5px;font-weight:600;color:#374151;margin-bottom:8px;}
    .input-wrap{position:relative;}
    .input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:15px;}
    .form-group input{width:100%;padding:13px 14px 13px 42px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:15px;outline:none;transition:border-color .2s;background:#f9fafb;}
    .form-group input:focus{border-color:#18b65a;background:#fff;}
    .form-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;font-size:13px;}
    .form-row label{display:flex;align-items:center;gap:7px;color:#374151;cursor:pointer;}
    .form-row a{color:#18b65a;text-decoration:none;font-weight:600;}
    .login-btn{width:100%;background:#18b65a;color:#fff;border:none;padding:14px;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:background .2s;}
    .login-btn:hover{background:#14964a;}
    .divider{display:flex;align-items:center;gap:12px;margin:22px 0;color:#9ca3af;font-size:13px;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:#e5e7eb;}
    .demo-note{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;font-size:13px;color:#166534;line-height:1.6;}
    .demo-note strong{font-weight:700;}
    .error-msg{color:#dc2626;font-size:13px;margin-top:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;}
    @media(max-width:700px){.login-left{display:none;}.login-right{padding:36px 28px;}}
  </style>
</head>
<body>
<div class="login-wrapper">
  <!-- Left -->
  <div class="login-left">
    <div class="brand">
      <div class="brand-icon"><i class="fa-solid fa-graduation-cap"></i></div>
      <div><h1>IPS</h1><p>Internship Placement System</p></div>
    </div>
    <div class="left-features">
      <h3>Manage your hiring<br>all in one place.</h3>
      <div class="feature-item">
        <div class="fi-icon"><i class="fa-solid fa-briefcase"></i></div>
        <div><h4>Post Jobs Easily</h4><p>Publish and manage job postings in seconds.</p></div>
      </div>
      <div class="feature-item">
        <div class="fi-icon"><i class="fa-solid fa-users"></i></div>
        <div><h4>Track Applicants</h4><p>Review, shortlist, and manage all candidates.</p></div>
      </div>
      <div class="feature-item">
        <div class="fi-icon"><i class="fa-solid fa-calendar-check"></i></div>
        <div><h4>Schedule Interviews</h4><p>Organise and track every interview seamlessly.</p></div>
      </div>
    </div>
  </div>
  <!-- Right -->
  <div class="login-right">
    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your company account</p>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label>Username or Email</label>
        <div class="input-wrap">
          <i class="fa-regular fa-user"></i>
          <input type="text" name="email" placeholder="admin"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
      </div>
      <div class="form-row">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit" class="login-btn">Sign In</button>
      <?php if ($error): ?>
        <p class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
    </form>

    <div class="divider">Default credentials</div>
    <div class="demo-note">
      <strong>Username:</strong> admin &nbsp;|&nbsp; <strong>Password:</strong> Admin123
    </div>
  </div>
</div>
</body>
</html>
