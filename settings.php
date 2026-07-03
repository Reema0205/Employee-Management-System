<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid      = (int)$_SESSION['company_id'];
$db       = getDB();
$success  = '';
$error    = '';

// ── Load current data ─────────────────────────────────
$stmt = $db->prepare("SELECT name, email, phone FROM companies WHERE id=?");
$stmt->bind_param('i', $cid);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ── Handle form submissions ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'update_info') {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$name || !$email) {
            $error = 'Name and username/email are required.';
        } else {
            // Check email uniqueness (exclude self)
            $chk = $db->prepare("SELECT id FROM companies WHERE email=? AND id!=?");
            $chk->bind_param('si', $email, $cid);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $error = 'That email is already in use by another account.';
            } else {
                $upd = $db->prepare("UPDATE companies SET name=?,email=?,phone=? WHERE id=?");
                $upd->bind_param('sssi', $name, $email, $phone, $cid);
                if ($upd->execute()) {
                    $_SESSION['company'] = ['name' => $name, 'email' => $email];
                    $company = ['name' => $name, 'email' => $email, 'phone' => $phone];
                    $success = 'Company information updated successfully!';
                } else {
                    $error = 'Update failed. Please try again.';
                }
                $upd->close();
            }
            $chk->close();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $oldPass  = $_POST['old_password']     ?? '';
        $newPass  = $_POST['new_password']     ?? '';
        $confPass = $_POST['confirm_password'] ?? '';

        if (!$oldPass || !$newPass || !$confPass) {
            $error = 'All password fields are required.';
        } elseif ($newPass !== $confPass) {
            $error = 'New passwords do not match.';
        } elseif (strlen($newPass) < 6) {
            $error = 'New password must be at least 6 characters.';
        } else {
            $s = $db->prepare("SELECT password FROM companies WHERE id=?");
            $s->bind_param('i', $cid); $s->execute();
            $hash = $s->get_result()->fetch_assoc()['password'] ?? '';
            $s->close();
            if (!password_verify($oldPass, $hash)) {
                $error = 'Current password is incorrect.';
            } else {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                $upd = $db->prepare("UPDATE companies SET password=? WHERE id=?");
                $upd->bind_param('si', $newHash, $cid);
                if ($upd->execute()) $success = 'Password updated successfully!';
                else $error = 'Failed to update password.';
                $upd->close();
            }
        }
    }
}

$db->close();

$activePage = 'settings';
$pageTitle  = 'Settings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/settings.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
  .alert-success,.alert-error{padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;}
  .alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;}
  .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
  </style>
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <?php if ($success): ?>
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Company Info -->
      <div class="settings-card">
        <div class="settings-card-header">
          <div class="settings-icon blue"><i class="fa-solid fa-building"></i></div>
          <div>
            <h3>Company Information</h3>
            <p>Update your company profile details.</p>
          </div>
        </div>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="update_info">
          <div class="settings-fields">
            <div class="form-group">
              <label><i class="fa-solid fa-building"></i> Company Name</label>
              <input type="text" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label><i class="fa-regular fa-envelope"></i> Username / Email</label>
              <input type="text" name="email" value="<?= htmlspecialchars($company['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-phone"></i> Phone Number</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($company['phone'] ?? '') ?>" placeholder="+1 000 000 0000">
            </div>
          </div>
          <button type="submit" class="save-btn">
            <i class="fa-solid fa-floppy-disk"></i> Save Changes
          </button>
        </form>
      </div>

      <!-- Change Password -->
      <div class="settings-card">
        <div class="settings-card-header">
          <div class="settings-icon green"><i class="fa-solid fa-lock"></i></div>
          <div>
            <h3>Change Password</h3>
            <p>Keep your account secure with a strong password.</p>
          </div>
        </div>
        <form method="POST" action="settings.php">
          <input type="hidden" name="action" value="change_password">
          <div class="settings-fields">
            <div class="form-group">
              <label><i class="fa-solid fa-lock"></i> Current Password</label>
              <input type="password" name="old_password" placeholder="Enter current password">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-key"></i> New Password</label>
              <input type="password" name="new_password" placeholder="Enter new password (min 6 chars)">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-check-circle"></i> Confirm New Password</label>
              <input type="password" name="confirm_password" placeholder="Confirm new password">
            </div>
          </div>
          <button type="submit" class="save-btn green-btn">
            <i class="fa-solid fa-shield-halved"></i> Update Password
          </button>
        </form>
      </div>

    </div>
  </main>
</div>
</body>
</html>
