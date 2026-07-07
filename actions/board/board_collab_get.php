<?php
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$boar_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "
    SELECT u.id as user_id, u.name, u.avatar
    FROM board_members bm
    JOIN boards b
    ON b.id = bm.board_id
    JOIN users u ON u.id = bm.user_id
    WHERE b.id = ? AND bm.user_id <> b.owner
");
mysqli_stmt_bind_param($stmt, "i", $boar_id);
mysqli_stmt_execute($stmt);
$assigneesBoard = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
