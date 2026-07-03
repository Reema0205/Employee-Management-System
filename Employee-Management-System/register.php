<?php
/**
 * register.php
 * Handles the signup form (Register.html) via fetch/AJAX.
 * Validates the fields and inserts a new row into the `users` table.
 */
header('Content-Type: application/json');
require_once 'db.php';

$data      = json_decode(file_get_contents("php://input"), true);
$fullname  = trim($data['fullname'] ?? '');
$username  = trim($data['username'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = $data['password'] ?? '';
$confirm   = $data['confirmPassword'] ?? '';
$role      = trim($data['role'] ?? 'Admin');

// ---- Validation ----
if ($fullname === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
    echo json_encode(["success" => false, "message" => "Please fill in all fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters."]);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit;
}

// ---- Check username / email not already taken ----
$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
mysqli_stmt_bind_param($check, "ss", $username, $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    echo json_encode(["success" => false, "message" => "That username or email is already registered."]);
    exit;
}

// ---- Insert the new user ----
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (username, email, password, role, fullname) VALUES (?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $hashed, $role, $fullname);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "message" => "Registration successful!"]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
