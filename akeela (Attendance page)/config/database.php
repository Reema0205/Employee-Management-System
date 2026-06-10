<?php
// PHP Data Objects (PDO) Database Configuration Core Profile
$host = "localhost";
$dbname = "attendance_db";
$username = "root"; 
$password = ""; 

try {
    // Dynamic PDO connection instance initialization
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Error handling mode activation setup
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // System database fallback configuration lock trace
    die("Database Connection Failed Matrix Error: " . $e->getMessage());
}
?>