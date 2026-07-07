<?php
session_start();
header('Content-Type: application/json');
include "../connection.php";

$task_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($task_id <= 0) {
    echo json_encode(["success" => false, "message" => "ID tugas tidak valid."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tasks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$task = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$task) {
    echo json_encode(["success" => false, "message" => "Tugas tidak ditemukan."]);
    exit;
}

$board_id = (int) $task['board_id'];

$stmt = mysqli_prepare($conn, "
    SELECT u.id, u.name, u.avatar
    FROM board_members bm
    JOIN users u ON u.id = bm.user_id
    WHERE bm.board_id = ?
    ORDER BY u.name ASC
");
mysqli_stmt_bind_param($stmt, "i", $board_id);
mysqli_stmt_execute($stmt);
$board_members = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

$stmt = mysqli_prepare($conn, "
    SELECT u.id, u.name, u.avatar
    FROM task_assign ta
    JOIN users u ON u.id = ta.user_id
    WHERE ta.task_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$assignees = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

$stmt = mysqli_prepare($conn, "SELECT * FROM subtasks WHERE task_id = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$subtasksRaw = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

$subtasks = [];
foreach ($subtasksRaw as $st) {
    $stmt2 = mysqli_prepare($conn, "
        SELECT u.id, u.name, u.avatar
        FROM subtask_assign sa
        JOIN users u ON u.id = sa.user_id
        WHERE sa.subtask_id = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt2, "i", $st['id']);
    mysqli_stmt_execute($stmt2);
    $assignee = mysqli_stmt_get_result($stmt2)->fetch_assoc();

    $subtasks[] = [
        "id"        => (int) $st['id'],
        "name"      => $st['subtask_name'],
        "done"      => $st['subtask_status'] === 'DONE',
        "priority"  => $st['subtask_priority'],
        "due_date"  => $st['due_date'],
        "assignee"  => $assignee ?: null,
    ];
}

$stmt = mysqli_prepare($conn, "
    SELECT c.id, c.comment, c.created_at, u.id AS user_id, u.name, u.avatar
    FROM comment c
    JOIN users u ON u.id = c.user_id
    WHERE c.task_id = ?
    ORDER BY c.created_at ASC
");
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$comments = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "success"       => true,
    "task"          => $task,
    "board_members" => $board_members,
    "assignees"     => $assignees,
    "subtasks"      => $subtasks,
    "comments"      => $comments,
]);
