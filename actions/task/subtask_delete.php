<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = (int) ($data['subtask_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID subtask tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM subtask_assign WHERE subtask_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$stmt = mysqli_prepare($conn, "DELETE FROM subtasks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(["success" => $ok]);
