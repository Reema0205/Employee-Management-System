<?php
/**
 * ============================================================
 *  db.php  —  THE ONLY DATABASE CONNECTION FILE FOR THIS PROJECT
 * ============================================================
 * Every page/module (Login, Dashboard, Employees, Payroll,
 * Attendance, Leave, Reports, Settings) includes THIS file.
 *
 * Change your DB host / username / password / database name
 * in ONE place only — right here.
 * ============================================================
 */

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "ems_db";

// ---- mysqli connection (used by most pages: $conn) ----
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// ---- PDO connection (used by the Attendance module: $pdo_conn) ----
try {
    $pdo_conn = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS
    );
    $pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Only fatal for pages that actually need PDO
    $pdo_conn = null;
}
?>
