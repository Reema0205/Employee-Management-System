<?php
// ─────────────────────────────────────────────────────
//  IPS Company Panel – Database Configuration
//  Edit the constants below to match your server.
// ─────────────────────────────────────────────────────

define('DB_HOST',     'localhost');
define('DB_USER',     'root');       // ← your MySQL username
define('DB_PASS',     '');           // ← your MySQL password
define('DB_NAME',     'ips_company_panel');
define('DB_CHARSET',  'utf8mb4');

// ─────────────────────────────────────────────────────
//  Create and return a MySQLi connection
// ─────────────────────────────────────────────────────
function getDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
