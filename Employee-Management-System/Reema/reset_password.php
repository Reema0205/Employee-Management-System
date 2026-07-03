<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "ems");

if($conn->connect_error){
    echo json_encode(['success' => false, 'message' => 'Database connection failed!']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$newPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);

// Check if user exists
$check = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $username);
$check->execute();
$check->store_result();

if($check->num_rows === 0){
    echo json_encode(['success' => false, 'message' => 'Username or Email not found!']);
    exit;
}

// Update password
$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ? OR email = ?");
$stmt->bind_param("sss", $newPassword, $username, $username);

if($stmt->execute()){
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password!']);
}

$conn->close();
?>