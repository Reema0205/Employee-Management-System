<?php
// ─────────────────────────────────────────────────────
//  auth.php – Start session & redirect if not logged in
// ─────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (empty($_SESSION['company_id'])) {
        header('Location: login.php');
        exit;
    }
}

function currentCompany(): array {
    return $_SESSION['company'] ?? ['name' => 'Company', 'email' => ''];
}
