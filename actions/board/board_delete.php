<?php

require_once __DIR__ . '/../connection.php';

header('Content-Type: application/json');

$boardId = (int)($_POST['board_id'] ?? 0);

if ($boardId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Board tidak ditemukan'
    ]);
    exit;
}

mysqli_begin_transaction($conn);

try {
    // ==========================
    // TASK
    // ==========================

    mysqli_query($conn, "
        DELETE sa
        FROM subtask_assign sa
        INNER JOIN subtasks s ON s.id = sa.subtask_id
        INNER JOIN tasks t ON t.id = s.task_id
        WHERE t.board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE s
        FROM subtasks s
        INNER JOIN tasks t ON t.id = s.task_id
        WHERE t.board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE ta
        FROM task_assign ta
        INNER JOIN tasks t ON t.id = ta.task_id
        WHERE t.board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE c
        FROM comment c
        INNER JOIN tasks t ON t.id = c.task_id
        WHERE t.board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE FROM tasks
        WHERE board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE FROM board_members
        WHERE board_id = {$boardId}
    ");

    mysqli_query($conn, "
        DELETE FROM board_invitations
        WHERE board_id = {$boardId}
    ");


    mysqli_query($conn, "
        DELETE FROM boards
        WHERE id = {$boardId}
    ");

    mysqli_commit($conn);

    echo json_encode([
        'success' => true
    ]);
} catch (Exception $e) {

    mysqli_rollback($conn);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
