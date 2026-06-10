<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

$action = $_GET['action'] ?? '';

if ($action === 'getAll') {

    $dept   = $_GET['dept']   ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';

    $where  = [];
    $types  = '';
    $params = [];

    if ($dept !== '') {
        $where[]  = "dept = ?";
        $types   .= 's';
        $params[] = $dept;
    }

    if ($status !== '') {
        $where[]  = "status = ?";
        $types   .= 's';
        $params[] = $status;
    }

    if ($search !== '') {
        $where[]  = "(name LIKE ? OR id LIKE ?)";
        $types   .= 'ss';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql = "SELECT * FROM employees";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $sql);
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
    $role   = $data['role'];
    $dept   = $data['dept'];
    $salary = $data['salary'];
    $status = $data['status'];
    $email  = $data['email'] ?? '';
    $phone  = $data['phone'] ?? '';

    $stmt = mysqli_prepare($conn,
        "INSERT INTO employees (id, name, role, dept, salary, status, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssisss", $id, $name, $role, $dept, $salary, $status, $email, $phone);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Employee added"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }

} elseif ($action === 'update') {

    $data   = json_decode(file_get_contents("php://input"), true);
    $id     = $data['id'];
    $name   = $data['name'];
    $role   = $data['role'];
    $dept   = $data['dept'];
    $salary = $data['salary'];
    $status = $data['status'];
    $email  = $data['email'] ?? '';
    $phone  = $data['phone'] ?? '';

    $stmt = mysqli_prepare($conn,
        "UPDATE employees SET name=?, role=?, dept=?, salary=?, status=?, email=?, phone=? WHERE id=?"
    );
    mysqli_stmt_bind_param($stmt, "sssissss", $name, $role, $dept, $salary, $status, $email, $phone, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true, "message" => "Employee updated"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }

} elseif ($action === 'delete') {

    $id   = $_GET['id'] ?? '';
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
