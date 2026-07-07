<?php
require_once __DIR__ . '.../../connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM boards
     WHERE owner = ?
     AND starred = 1
     ORDER BY created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$starredboards = mysqli_stmt_get_result($stmt);
