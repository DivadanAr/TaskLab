<?php

session_start();
header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$task_id = (int) ($data['task_id'] ?? 0);
$text    = trim($data['comment'] ?? '');
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if ($task_id <= 0 || $text === '' || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Data tidak valid atau belum login."]);
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO comment (comment, task_id, user_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
mysqli_stmt_bind_param($stmt, "sii", $text, $task_id, $user_id);
mysqli_stmt_execute($stmt);
$comment_id = mysqli_insert_id($conn);

$stmt = mysqli_prepare($conn, "
    SELECT c.id, c.comment, c.created_at, u.id AS user_id, u.name, u.avatar
    FROM comment c
    JOIN users u ON u.id = c.user_id
    WHERE c.id = ?
");
mysqli_stmt_bind_param($stmt, "i", $comment_id);
mysqli_stmt_execute($stmt);
$comment = mysqli_stmt_get_result($stmt)->fetch_assoc();

echo json_encode(["success" => true, "comment" => $comment]);
