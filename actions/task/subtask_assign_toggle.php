<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$subtask_id = (int) ($data['subtask_id'] ?? 0);
$user_id    = (int) ($data['user_id'] ?? 0);

if ($subtask_id <= 0 || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Data tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM subtask_assign WHERE subtask_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $subtask_id, $user_id);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt)->fetch_assoc();

$stmt = mysqli_prepare($conn, "DELETE FROM subtask_assign WHERE subtask_id = ?");
mysqli_stmt_bind_param($stmt, "i", $subtask_id);
mysqli_stmt_execute($stmt);

if ($existing) {
    echo json_encode(["success" => true, "assignee_id" => null]);
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO subtask_assign (subtask_id, user_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    mysqli_stmt_bind_param($stmt, "ii", $subtask_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["success" => true, "assignee_id" => $user_id]);
}
