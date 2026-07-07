<?php

/**
 * Helper untuk fitur invite anggota board.
 * Dipakai bareng oleh board.php, invite_post.php, dan invitation_respond.php
 * supaya query & aturan izinnya gak dobel-dobel ditulis di banyak tempat.
 */

/**
 * Ambil "role" seorang user terhadap sebuah board.
 * Return 'owner' kalau dia pemilik board, role dari board_members
 * ('admin'/'editor'/'view') kalau dia anggota, atau null kalau bukan siapa-siapa.
 */
function getBoardRole($conn, $board_id, $user_id)
{
    if (!$board_id || !$user_id) {
        return null;
    }

    $stmt = mysqli_prepare($conn, "SELECT owner FROM boards WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $board_id);
    mysqli_stmt_execute($stmt);
    $board = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$board) {
        return null;
    }

    if ((int) $board['owner'] === (int) $user_id) {
        return 'owner';
    }

    $stmt = mysqli_prepare($conn, "SELECT role FROM board_members WHERE board_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $board_id, $user_id);
    mysqli_stmt_execute($stmt);
    $member = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    return $member ? $member['role'] : null;
}

/**
 * Cuma owner & admin board yang boleh ngundang anggota baru.
 */
function canInviteToBoard($conn, $board_id, $user_id)
{
    $role = getBoardRole($conn, $board_id, $user_id);
    return in_array($role, ['owner', 'admin'], true);
}

/**
 * Cari user yang BISA diundang ke sebuah board.
 *
 * Aturan:
 * - Kalau keyword kosong -> return array kosong (gak nampilin semua user).
 * - User yang sudah jadi anggota board tidak ikut muncul.
 * - User yang masih punya undangan pending ke board itu tidak ikut muncul.
 * - Diri sendiri tidak ikut muncul.
 */
function searchInvitableUsers($conn, $board_id, $keyword, $current_user_id)
{
    $keyword = trim((string) $keyword);

    if ($keyword === '') {
        return [];
    }

    $like = '%' . $keyword . '%';

    $stmt = mysqli_prepare(
        $conn,
        "SELECT u.id, u.name, u.email, u.avatar
         FROM users u
         WHERE (u.name LIKE ? OR u.email LIKE ?)
           AND u.id != ?
           AND u.id NOT IN (
                SELECT user_id FROM board_members WHERE board_id = ?
           )
           AND u.id NOT IN (
                SELECT receiver_id FROM board_invitations WHERE board_id = ? AND status = 'pending'
           )
         ORDER BY u.name ASC
         LIMIT 8"
    );

    mysqli_stmt_bind_param($stmt, "ssiii", $like, $like, $current_user_id, $board_id, $board_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $users;
}
