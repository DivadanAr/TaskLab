<?php

require_once __DIR__ . '.../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id       = (int) $_SESSION['user_id'];
$invitation_id = (int) ($_POST['invitation_id'] ?? 0);
$action        = $_POST['action'] ?? '';

if ($invitation_id <= 0 || !in_array($action, ['accept', 'reject'], true)) {
    $_SESSION['error'] = "Permintaan tidak valid.";
    header("Location: ../../dashboard/inbox.php");
    exit;
}

// Cuma penerima undangan itu sendiri yang boleh merespon, dan cuma yang masih pending.
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM board_invitations WHERE id = ? AND receiver_id = ? AND status = 'pending'"
);
mysqli_stmt_bind_param($stmt, "ii", $invitation_id, $user_id);
mysqli_stmt_execute($stmt);
$invitation = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$invitation) {
    $_SESSION['error'] = "Undangan tidak ditemukan atau sudah diproses sebelumnya.";
    header("Location: ../../dashboard/inbox.php");
    exit;
}

$board_id = (int) $invitation['board_id'];

$stmt = mysqli_prepare($conn, "SELECT board_name FROM boards WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $board_id);
mysqli_stmt_execute($stmt);
$board = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$boardName = $board['board_name'] ?? 'board tersebut';

if ($action === 'accept') {

    $newStatus = 'accepted';

    // Cek dulu supaya gak nabrak unique index board_members(board_id, user_id)
    $stmt = mysqli_prepare($conn, "SELECT id FROM board_members WHERE board_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $board_id, $user_id);
    mysqli_stmt_execute($stmt);
    $isMember = mysqli_stmt_get_result($stmt)->num_rows > 0;
    mysqli_stmt_close($stmt);

    if (!$isMember) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO board_members (board_id, user_id, role, created_at, updated_at)
             VALUES (?, ?, 'editor', NOW(), NOW())"
        );
        mysqli_stmt_bind_param($stmt, "ii", $board_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $_SESSION['success'] = "Kamu bergabung ke board \"{$boardName}\".";

} else {

    $newStatus = 'rejected';
    $_SESSION['success'] = "Undangan \"{$boardName}\" ditolak.";

}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE board_invitations SET status = ?, responded_at = NOW() WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "si", $newStatus, $invitation_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: ../../dashboard/inbox.php");
exit;
