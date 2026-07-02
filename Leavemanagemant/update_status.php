<?php

include "db.php";

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE leaves
        SET status = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("si", $status, $id);

if($stmt->execute()){
    echo "success";
}else{
    echo "error";
}

$stmt->close();
$conn->close();
?>

