<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$task_id = (int) ($data['task_id'] ?? 0);
$subtask_name = trim($data['subtask_name'] ?? '');

if ($task_id <= 0 || $subtask_name === '') {
    echo json_encode(["success" => false, "message" => "Data tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "
    INSERT INTO subtasks (subtask_name, task_id, subtask_status, subtask_priority, created_at, updated_at)
    VALUES (?, ?, 'TODO', 'MEDIUM', NOW(), NOW())
");
mysqli_stmt_bind_param($stmt, "si", $subtask_name, $task_id);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(["success" => $ok, "id" => mysqli_insert_id($conn)]);
