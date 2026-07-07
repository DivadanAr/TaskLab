<?php

header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$board_id = (int) ($data['board_id'] ?? 0);
$user_id = (int) ($data['user_id'] ?? 0);

if ($board_id <= 0 || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Data tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM board_members WHERE board_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $board_id, $user_id);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($existing) {
    $stmt = mysqli_prepare($conn, "DELETE FROM board_members WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $existing['id']);
    mysqli_stmt_execute($stmt);

    echo json_encode(["success" => true, "assigned" => false]);
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO board_members (board_id, user_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    mysqli_stmt_bind_param($stmt, "ii", $board_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["success" => true, "assigned" => true]);
}
