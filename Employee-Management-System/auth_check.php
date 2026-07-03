<?php
/**
 * auth_check.php
 * Include this at the very TOP of every protected page
 * (Dashboard, Settings, etc.) to make sure only logged-in
 * users can view it. If not logged in -> redirect to login.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
