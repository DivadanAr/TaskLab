<?php
header('Content-Type: application/json');
include "../connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID tugas tidak valid."]);
    exit;
}

mysqli_begin_transaction($conn);

try {
    $stmt = mysqli_prepare($conn, "DELETE FROM comment WHERE task_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "
        DELETE sa FROM subtask_assign sa
        JOIN subtasks s ON s.id = sa.subtask_id
        WHERE s.task_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM subtasks WHERE task_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM task_assign WHERE task_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(["success" => false, "message" => "Gagal menghapus tugas."]);
}
