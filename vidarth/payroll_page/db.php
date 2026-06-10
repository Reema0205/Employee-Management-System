<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "payroll_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die(json_encode([
        "error" => "Connection failed: " . mysqli_connect_error()
    ]));
}
?>
