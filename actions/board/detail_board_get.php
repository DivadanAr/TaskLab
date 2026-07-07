<?php

$board_id = (int) $_GET['id'];

$stmtboard = mysqli_prepare($conn, "SELECT * FROM boards WHERE id = ?");
mysqli_stmt_bind_param($stmtboard, "i", $board_id);
mysqli_stmt_execute($stmtboard);
$resultboard = mysqli_stmt_get_result($stmtboard);

if (mysqli_num_rows($resultboard) === 0) {
    // board tidak ditemukan
    $_SESSION['error'] = "board tidak ditemukan.";
    header("Location: ../");
    exit;
}

$board = mysqli_fetch_assoc($resultboard);
mysqli_stmt_close($stmtboard);


function getTasksByBoard($board_id)
{
    global $conn;

    $stmt = mysqli_prepare(
        $conn,
        "SELECT *
         FROM tasks
         WHERE board_id = ?
         ORDER BY created_at DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $board_id);

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}
