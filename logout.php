<?php
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();
$company = $_SESSION['company'] ?? ['name' => 'Company'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout – IPS Company Panel</title>
  <link rel="stylesheet" href="css/logout.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="logout-container">
  <div class="logout-card">
    <div class="logout-icon">
      <i class="fa-solid fa-right-from-bracket"></i>
    </div>
    <h1>Sign Out</h1>
    <p> Are you sure you want to sign out of <strong><?= htmlspecialchars($company['name']) ?></strong>'s account?</p>
    <div class="buttons">
      <button class="cancel-btn" onclick="history.back()">
        <i class="fa-solid fa-arrow-left"></i> Go Back
      </button>
      <form method="POST" action="logout.php" style="display:inline;">
        <input type="hidden" name="confirm" value="1">
        <button type="submit" class="logout-btn">
          <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
