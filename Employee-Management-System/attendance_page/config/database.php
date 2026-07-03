<?php
// This module now uses the ONE shared database connection for the whole project.
// The Attendance module's code uses PDO-style queries ($conn->prepare(), $conn->query()),
// so we alias the shared PDO connection to $conn here.
require_once __DIR__ . '/../../db.php';
$conn = $pdo_conn;
