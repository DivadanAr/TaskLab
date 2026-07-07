<?php
require_once __DIR__ . '/../connection.php';

$boardId = $_POST['board_id'] ?? null;

if (!$boardId) {
    echo json_encode([
        'success' => false,
        'message' => 'Board tidak ditemukan'
    ]);
    exit;
}

$field = $_POST['field'];
$value = $_POST['value'];

$allowed = ['board_name', 'description'];

if (!in_array($field, $allowed)) {
    exit(json_encode([
        'success' => false,
        'message' => 'Field tidak valid'
    ]));
}

$sql = "UPDATE boards SET $field = ?, updated_at = NOW() WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $value, $boardId);
mysqli_stmt_execute($stmt);
echo json_encode([
    'success' => true
]);
