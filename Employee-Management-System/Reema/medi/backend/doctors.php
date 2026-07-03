<?php
include "db.php";

$method = $_SERVER['REQUEST_METHOD'];


// ================= GET ALL DOCTORS =================

if ($method == "GET") {

    $res = $conn->query("SELECT * FROM doctors ORDER BY id ASC");

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


// ================= ADD DOCTOR =================

if ($method == "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON body"]);
        exit;
    }

    $name          = $input['name']          ?? '';
    $specialty     = $input['specialty']     ?? '';
    $experience    = (int)($input['experience']    ?? 0);
    $rating        = (float)($input['rating']      ?? 0.0);
    $fee_in_person = (int)($input['fee_in_person'] ?? 0);
    $fee_virtual   = (int)($input['fee_virtual']   ?? 0);
    $avatar        = (int)($input['avatar']        ?? 0);
    $bio           = $input['bio']           ?? '';

    // FIX: Use prepared statement (SQL injection prevention)
    // FIX: Correct type string — s,s,i,d,i,i,i,s
    $stmt = $conn->prepare("
        INSERT INTO doctors
        (name, specialty, experience, rating, fee_in_person, fee_virtual, avatar, bio)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssidiiis",
        $name,
        $specialty,
        $experience,
        $rating,
        $fee_in_person,
        $fee_virtual,
        $avatar,
        $bio
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Doctor added",
            "id"      => $stmt->insert_id
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}


// ================= DELETE DOCTOR =================

if ($method == "DELETE") {

    // FIX: Validate ID and use prepared statement
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid doctor ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Doctor deleted"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}

// FIX: Catch unsupported methods
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
?>
