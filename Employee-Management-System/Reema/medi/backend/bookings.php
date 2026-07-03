<?php
include "db.php";

$method = $_SERVER['REQUEST_METHOD'];


// ================= CREATE BOOKING =================

if ($method == "POST") {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON body"]);
        exit;
    }

    // 1. Insert patient
    $stmt = $conn->prepare("INSERT INTO patients (name, email, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $input['name'], $input['email'], $input['phone']);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Patient insert failed: " . $stmt->error]);
        exit;
    }

    $patient_id = $stmt->insert_id;

    // 2. Insert booking
    // FIX: amount is stored as DECIMAL/FLOAT in DB — use 'd' not 'i' in bind_param
    $stmt2 = $conn->prepare("
        INSERT INTO bookings
        (doctor_id, patient_id, consult_type, booking_date, time_slot, symptoms, amount)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $doctor_id    = (int)($input['doctor_id']    ?? 0);
    $consult_type = $input['consult_type']  ?? '';
    $booking_date = $input['booking_date']  ?? '';
    $time_slot    = $input['time_slot']     ?? '';
    $symptoms     = $input['symptoms']      ?? '';
    $amount       = (float)($input['amount']     ?? 0.0);

    // FIX: correct type string — i,i,s,s,s,s,d
    $stmt2->bind_param(
        "iissssd",
        $doctor_id,
        $patient_id,
        $consult_type,
        $booking_date,
        $time_slot,
        $symptoms,
        $amount
    );

    if ($stmt2->execute()) {
        echo json_encode([
            "status"     => "success",
            "message"    => "Booking created",
            "booking_id" => $stmt2->insert_id
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Booking insert failed: " . $stmt2->error]);
    }
    exit;
}


// ================= GET ALL BOOKINGS =================

if ($method == "GET") {

    // FIX: Added p.email AS patient_email so admin table can show it
    $res = $conn->query("
        SELECT b.*, d.name AS doctor_name, p.name AS patient_name, p.email AS patient_email
        FROM bookings b
        JOIN doctors  d ON b.doctor_id  = d.id
        JOIN patients p ON b.patient_id = p.id
        ORDER BY b.id DESC
    ");

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


// ================= DELETE BOOKING =================
// FIX: Added DELETE handler — previously missing, so cancel-booking
// only removed the row from localStorage and never touched the DB.

if ($method == "DELETE") {

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid booking ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Booking deleted"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    exit;
}

// FIX: Catch unsupported methods
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
?>
