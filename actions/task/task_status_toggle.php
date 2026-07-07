<?php

require_once __DIR__ . '/../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Kamu harus login dulu.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

$task_id = (int) ($_POST['task_id'] ?? 0);

if ($task_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'task_id tidak valid.']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT task_status FROM tasks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Tugas tidak ditemukan.']);
    exit;
}

$task = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$newStatus = $task['task_status'] === 'DONE' ? 'TODO' : 'DONE';

$update = mysqli_prepare($conn, "UPDATE tasks SET task_status = ?, updated_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($update, "si", $newStatus, $task_id);
$success = mysqli_stmt_execute($update);
mysqli_stmt_close($update);

echo json_encode([
    'success'    => (bool) $success,
    'task_id'    => $task_id,
    'new_status' => $newStatus,
    'message'    => $success ? 'Status tugas diperbarui.' : 'Gagal memperbarui status.',
]);
