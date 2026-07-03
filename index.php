<?php
// index.php – entry point, redirect to login or dashboard
require_once 'includes/auth.php';

if (!empty($_SESSION['company_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
