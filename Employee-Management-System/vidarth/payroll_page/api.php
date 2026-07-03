<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$action = $_GET['action'] ?? '';

if ($action === 'getAll') {

    $dept = $_GET['dept'] ?? '';

    if ($dept !== '') {
        $stmt = mysqli_prepare($conn, "SELECT * FROM employees WHERE dept = ?");
        mysqli_stmt_bind_param($stmt, "s", $dept);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM employees");
    }

    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }

    echo json_encode($employees);

} elseif ($action === 'add') {

    $data   = json_decode(file_get_contents("php://input"), true);
    $id     = $data['id'];
    $name   = $data['name'];
    $dept   = $data['dept'];
    $basic  = $data['basic'];
    $bonus  = $data['bonus'];
    $deduct = $data['deduct'];

    $stmt = mysqli_prepare($conn,
        "INSERT INTO employees (id, name, dept, basic, bonus, deduct) VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sssiii", $id, $name, $dept, $basic, $bonus, $deduct);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Employee added"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }

} elseif ($action === 'update') {

    $data   = json_decode(file_get_contents("php://input"), true);
    $id     = $data['id'];
    $name   = $data['name'];
    $dept   = $data['dept'];
    $basic  = $data['basic'];
    $bonus  = $data['bonus'];
    $deduct = $data['deduct'];

    $stmt = mysqli_prepare($conn,
        "UPDATE employees SET name=?, dept=?, basic=?, bonus=?, deduct=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssiiis", $name, $dept, $basic, $bonus, $deduct, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Employee updated"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }

} elseif ($action === 'delete') {

    $id = $_GET['id'] ?? '';

    $stmt = mysqli_prepare($conn, "DELETE FROM employees WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Employee deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }

} else {
    echo json_encode(["error" => "Unknown action"]);
}

mysqli_close($conn);
?>
