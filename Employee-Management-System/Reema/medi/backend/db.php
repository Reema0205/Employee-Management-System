<?php

$host = "localhost";
$user = "root";
$pass = "";       
$db   = "medicore";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    header("Content-Type: application/json");
    die(json_encode([
        "status"  => "error",
        "message" => "DB Connection failed: " . $conn->connect_error
    ]));
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");

?>
