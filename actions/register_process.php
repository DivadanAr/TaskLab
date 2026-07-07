<?php
require_once 'connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {

        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: ../register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_SESSION['error'] = "Format email tidak valid.";
        header("Location: ../register.php");
        exit;
    }

    if (strlen($password) < 6) {

        $_SESSION['error'] = "Password minimal 6 karakter.";
        header("Location: ../register.php");
        exit;
    }

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);

    $result = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($result) > 0) {

        $_SESSION['error'] = "Email sudah terdaftar.";
        header("Location: ../register.php");
        exit;
    }

    mysqli_stmt_close($check);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users(name,email,password) VALUES(?,?,?)");

    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);

    if (mysqli_stmt_execute($stmt)) {

        $_SESSION['success'] = "Registrasi berhasil. Silakan login.";

        header("Location: ../login.php");
        exit;
    } else {

        $_SESSION['error'] = "Registrasi gagal.";

        header("Location: ../register.php");
        exit;
    }
}
