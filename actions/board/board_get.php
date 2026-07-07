<?php
require_once __DIR__ . '.../../connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        DISTINCT b.*
    FROM
        boards b
    INNER JOIN board_members bm 
        ON b.id = bm.board_id
    WHERE b.owner = ? OR bm.user_id = ? 
     ORDER BY b.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);

mysqli_stmt_execute($stmt);

$boards = mysqli_stmt_get_result($stmt);
