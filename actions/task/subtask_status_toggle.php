<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = (int) ($data['subtask_id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID subtask tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT subtask_status FROM subtasks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "message" => "Subtask tidak ditemukan."]);
    exit;
}

$new_status = $row['subtask_status'] === 'DONE' ? 'TODO' : 'DONE';

$stmt = mysqli_prepare($conn, "UPDATE subtasks SET subtask_status = ?, updated_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
mysqli_stmt_execute($stmt);

echo json_encode(["success" => true, "status" => $new_status]);
