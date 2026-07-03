<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

// FIX: admin_panel.php and admin.php are in the same folder (medi/)
// so db.php is in the same directory
include "db.php";

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === "GET") {

    $res = $conn->query("SELECT * FROM doctors ORDER BY id DESC");

    if (!$res) {
        echo json_encode(["status" => "error", "message" => $conn->error]);
        exit;
    }

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

if ($method === "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON body"]);
        exit;
    }

    $name          = trim($input['name']          ?? '');
    $specialty     = trim($input['specialty']     ?? '');
    $experience    = (int)  ($input['experience']    ?? 0);
    $rating        = (float)($input['rating']        ?? 0.0);
    $fee_in_person = (int)  ($input['fee_in_person'] ?? 0);
    $fee_virtual   = (int)  ($input['fee_virtual']   ?? 0);
    $avatar        = (int)  ($input['avatar']        ?? 0);
    $bio           = trim($input['bio']           ?? '');

    if (!$name || !$specialty) {
        echo json_encode(["status" => "error", "message" => "Name and specialty are required."]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO doctors
            (name, specialty, experience, rating, fee_in_person, fee_virtual, avatar, bio)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    // FIX: correct type string — s,s,i,d,i,i,i,s
    $stmt->bind_param("ssidiiis", $name, $specialty, $experience, $rating,
                                  $fee_in_person, $fee_virtual, $avatar, $bio);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Doctor added successfully",
            "id"      => $stmt->insert_id
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    exit;
}

if ($method === "PUT") {

    $id    = (int)($_GET['id'] ?? 0);
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$id || !$input) {
        echo json_encode(["status" => "error", "message" => "Doctor ID and JSON body required."]);
        exit;
    }

    $name          = trim($input['name']          ?? '');
    $specialty     = trim($input['specialty']     ?? '');
    $experience    = (int)  ($input['experience']    ?? 0);
    $rating        = (float)($input['rating']        ?? 0.0);
    $fee_in_person = (int)  ($input['fee_in_person'] ?? 0);
    $fee_virtual   = (int)  ($input['fee_virtual']   ?? 0);
    $bio           = trim($input['bio']           ?? '');

    if (!$name || !$specialty) {
        echo json_encode(["status" => "error", "message" => "Name and specialty are required."]);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE doctors
        SET name=?, specialty=?, experience=?, rating=?,
            fee_in_person=?, fee_virtual=?, bio=?
        WHERE id=?
    ");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    // FIX: correct type string — s,s,i,d,i,i,s,i
    $stmt->bind_param("ssidiisi",
        $name, $specialty, $experience, $rating,
        $fee_in_person, $fee_virtual, $bio, $id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Doctor updated successfully"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    exit;
}

if ($method === "DELETE") {

    $id = (int)($_GET['id'] ?? 0);

    if (!$id) {
        echo json_encode(["status" => "error", "message" => "ID required."]);
        exit;
    }

    // FIX: Handle booking delete separately via action param
    if ($action === 'deleteBooking') {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        if (!$stmt) {
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Booking deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    // Default: delete doctor
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Doctor deleted successfully"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    exit;
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
?>

