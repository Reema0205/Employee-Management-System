<?php
/**
 * login.php
 * Handles the login form submit (from login.html) via fetch/AJAX.
 * Checks username + password against the `users` table and
 * starts a PHP session used by index.php / logout.php.
 */
header('Content-Type: application/json');
session_start();

require_once 'db.php';

$data     = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$role     = trim($data['role'] ?? '');

if ($username === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Please enter username and password"]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid username or password"]);
    exit;
}

if ($role !== '' && strcasecmp($role, $user['role']) !== 0) {
    echo json_encode(["success" => false, "message" => "This account's role is \"{$user['role']}\", not \"$role\". Please select the correct role."]);
    exit;
}

$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

echo json_encode(["success" => true, "redirect" => "index.php"]);

mysqli_close($conn);
?>
