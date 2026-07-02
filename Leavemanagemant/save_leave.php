<?php

include "db.php";

$employee_id = $_POST['employee_id'];
$leave_type = $_POST['leave_type'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$reason = $_POST['reason'];

$sql = "INSERT INTO leaves
(employee_id, leave_type, start_date, end_date, reason)
VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $employee_id,
    $leave_type,
    $start_date,
    $end_date,
    $reason
);

if($stmt->execute()){
    echo "success";
}else{
    echo "error";
}