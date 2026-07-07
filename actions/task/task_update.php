<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);

$id          = isset($data['id']) ? (int) $data['id'] : 0;
$task_name   = trim($data['task_name'] ?? '');
$description = trim($data['description'] ?? '');

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID tugas tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE tasks SET task_name = ?, description = ?, updated_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssi", $task_name, $description, $id);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(["success" => $ok]);
