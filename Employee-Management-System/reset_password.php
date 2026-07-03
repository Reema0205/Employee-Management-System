<?php
header('Content-Type: application/json');
require_once 'db.php';

$data        = json_decode(file_get_contents("php://input"), true);
$username    = trim($data['username'] ?? '');
$newPassword = $data['newPassword'] ?? '';

if ($username === '' || $newPassword === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields!']);
    exit;
}

$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
mysqli_stmt_bind_param($check, "ss", $username, $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Username or Email not found!']);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ? OR email = ?");
mysqli_stmt_bind_param($stmt, "sss", $hashed, $username, $username);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password!']);
}

mysqli_close($conn);
?>
