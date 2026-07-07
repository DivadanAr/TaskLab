<?php

require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id     = (int) $_SESSION['user_id'];
$board_id    = (int) ($_POST['board_id'] ?? 0);
$task_name   = trim($_POST['task_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date    = $_POST['due_date'] ?? '';
$priority    = strtoupper(trim($_POST['priority'] ?? '')); // "" | LOW | MEDIUM | HIGH

if ($board_id <= 0) {
    $_SESSION['error'] = "Board tidak valid.";
    header("Location: ../../dashboard/my-board.php");
    exit;
}

if ($task_name === '') {
    $_SESSION['error'] = "Nama task wajib diisi.";
    header("Location: ../../dashboard/board.php?id={$board_id}");
    exit;
}

$due_date = $due_date === '' ? null : $due_date;

$allowedPriority = ['LOW', 'MEDIUM', 'HIGH'];
if ($priority !== '' && !in_array($priority, $allowedPriority, true)) {
    $_SESSION['error'] = "Priority tidak valid.";
    header("Location: ../../dashboard/board.php?id={$board_id}");
    exit;
}
$priority = $priority === '' ? null : $priority;

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tasks (task_name, description, due_date, board_id, task_status, task_priority, created_at, updated_at)
     VALUES (?, ?, ?, ?, 'TODO', ?, NOW(), NOW())"
);
mysqli_stmt_bind_param($stmt, "sssis", $task_name, $description, $due_date, $board_id, $priority);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION['error'] = "Gagal membuat task, coba lagi.";
    header("Location: ../../dashboard/board.php?id={$board_id}");
    exit;
}

mysqli_stmt_close($stmt);

$_SESSION['success'] = "Task \"{$task_name}\" berhasil dibuat.";
header("Location: ../../dashboard/board.php?id={$board_id}");
exit;
