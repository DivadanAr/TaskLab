<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Path absolut dari root domain ke folder project ini.
// Dipakai di form action / link supaya gak rusak walau partial
// (modal, dsb) di-include dari halaman root ATAU dari subfolder dashboard/.
// Ganti nilainya kalau nama folder project kamu bukan "TaskLab".
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/TaskLab/');
}

$host     = "localhost";
$username = "root";
$password = "";
$database = "tasklab_v2";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
