<?php
session_start();
header('Content-Type: application/json');
require_once "./connection.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak valid, silakan login ulang.']);
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = trim($_POST['user_name'] ?? '');
$userEmail = trim($_POST['user_email'] ?? '');
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['new_password_confirm'] ?? '';

if ($userName === '' || $userEmail === '') {
    echo json_encode(['success' => false, 'message' => 'Nama dan email wajib diisi.']);
    exit;
}

if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
    exit;
}

if ($newPassword !== '' && $newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok.']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
mysqli_stmt_bind_param($stmt, "si", $userEmail, $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email sudah digunakan akun lain.']);
    exit;
}
mysqli_stmt_close($stmt);

if ($newPassword !== '') {
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $userName, $userEmail, $hashed, $userId);
} else {
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $userName, $userEmail, $userId);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['user_name']  = $userName;
    $_SESSION['user_email'] = $userEmail;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
