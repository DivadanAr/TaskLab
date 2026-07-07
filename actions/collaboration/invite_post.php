<?php

require_once __DIR__ . '.../../connection.php';
require_once __DIR__ . '/invite_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id     = (int) $_SESSION['user_id'];
$board_id    = (int) ($_POST['board_id'] ?? 0);
$receiver_id = (int) ($_POST['receiver_id'] ?? 0);
$inviteQuery = trim((string) ($_POST['invite_q'] ?? ''));

$redirectBack = "../../dashboard/board.php?id={$board_id}"
    . ($inviteQuery !== '' ? '&invite_q=' . urlencode($inviteQuery) : '');

if ($board_id <= 0 || $receiver_id <= 0) {
    $_SESSION['error'] = "Data undangan tidak valid.";
    header("Location: {$redirectBack}");
    exit;
}

if (!canInviteToBoard($conn, $board_id, $user_id)) {
    $_SESSION['error'] = "Kamu tidak punya izin untuk mengundang anggota di board ini.";
    header("Location: {$redirectBack}");
    exit;
}

if ($receiver_id === $user_id) {
    $_SESSION['error'] = "Tidak bisa mengundang diri sendiri.";
    header("Location: {$redirectBack}");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $receiver_id);
mysqli_stmt_execute($stmt);
$receiver = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$receiver) {
    $_SESSION['error'] = "User yang mau diundang tidak ditemukan.";
    header("Location: {$redirectBack}");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM board_members WHERE board_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $board_id, $receiver_id);
mysqli_stmt_execute($stmt);
$alreadyMember = mysqli_stmt_get_result($stmt)->num_rows > 0;
mysqli_stmt_close($stmt);

if ($alreadyMember) {
    $_SESSION['error'] = "{$receiver['name']} sudah menjadi anggota board ini.";
    header("Location: {$redirectBack}");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM board_invitations WHERE board_id = ? AND receiver_id = ? AND status = 'pending'"
);
mysqli_stmt_bind_param($stmt, "ii", $board_id, $receiver_id);
mysqli_stmt_execute($stmt);
$alreadyInvited = mysqli_stmt_get_result($stmt)->num_rows > 0;
mysqli_stmt_close($stmt);

if ($alreadyInvited) {
    $_SESSION['error'] = "{$receiver['name']} sudah punya undangan yang masih pending di board ini.";
    header("Location: {$redirectBack}");
    exit;
}

$token = bin2hex(random_bytes(16));

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO board_invitations (board_id, sender_id, receiver_id, status, invitation_token, created_at)
     VALUES (?, ?, ?, 'pending', ?, NOW())"
);
mysqli_stmt_bind_param($stmt, "iiis", $board_id, $user_id, $receiver_id, $token);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Undangan berhasil dikirim ke {$receiver['name']}.";
} else {
    $_SESSION['error'] = "Gagal mengirim undangan, coba lagi.";
}
mysqli_stmt_close($stmt);

header("Location: {$redirectBack}");
exit;
