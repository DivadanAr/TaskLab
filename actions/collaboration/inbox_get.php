<?php

require_once __DIR__ . '.../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// ---------- Undangan board yang masih pending ----------
$stmt = mysqli_prepare(
    $conn,
    "SELECT bi.id AS invitation_id, bi.created_at AS invited_at,
            b.id AS board_id, b.board_name,
            u.id AS sender_id, u.name AS sender_name
     FROM board_invitations bi
     INNER JOIN boards b ON b.id = bi.board_id
     INNER JOIN users u ON u.id = bi.sender_id
     WHERE bi.receiver_id = ? AND bi.status = 'pending'
     ORDER BY bi.created_at DESC"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$invitations = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare(
    $conn,
    "SELECT bi.id AS invitation_id, bi.created_at AS invited_at,
            b.id AS board_id, b.board_name,
            u.id AS sender_id, u.name AS sender_name,
            bi.status AS status
     FROM board_invitations bi
     INNER JOIN boards b ON b.id = bi.board_id
     INNER JOIN users u ON u.id = bi.sender_id
     WHERE bi.receiver_id = ?
     ORDER BY bi.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$notifications = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
/**
 * Format waktu relatif sederhana ala "2 jam yang lalu", tanpa perlu JS/lib tambahan.
 */
function timeAgoID($datetime)
{
    if (empty($datetime)) {
        return '-';
    }

    $diff = time() - strtotime($datetime);

    if ($diff < 60) {
        return "Baru saja";
    }
    if ($diff < 3600) {
        return floor($diff / 60) . " menit yang lalu";
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . " jam yang lalu";
    }
    if ($diff < 172800) {
        return "Kemarin";
    }

    return floor($diff / 86400) . " hari yang lalu";
}
