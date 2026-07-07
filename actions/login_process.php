<?php
require_once 'connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode(['success' => true, 'message' => 'Sudah login.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email dan Password wajib diisi.']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        if (isset($_POST['remember'])) {
            setcookie("remember_me", $user['email'], time() + (86400 * 30), "/");
        }

        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'message' => 'Login berhasil.']);
        exit;
    }
}

mysqli_stmt_close($stmt);
echo json_encode(['success' => false, 'message' => 'Email atau Password salah.']);
exit;
