<?php

require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}


$user_id     = (int) $_SESSION['user_id'];
$board_name  = trim($_POST['board_name'] ?? '');
$description = $_POST['description'] ?? '';

if ($board_name === '') {
    $_SESSION['error'] = "Nama board wajib diisi.";
    header("Location: ../dashboard/my-board.php");
    exit;
}

$allowedTags = '<p><h1><h2><h3><b><strong><i><em><u><ul><ol><li><a><blockquote><br>';
$description = strip_tags($description, $allowedTags);

$coverPathForDb = null;

if (isset($_FILES['cover_board']) && $_FILES['cover_board']['error'] !== UPLOAD_ERR_NO_FILE) {

    $file = $_FILES['cover_board'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Upload cover gagal (kode error: {$file['error']}).";
        header("Location: ../../dashboard/my-board.php");;
        exit;
    }

    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $_SESSION['error'] = "Ukuran cover maksimal 5 MB.";
        header("Location: ../../dashboard/my-board.php");
        exit;
    }

    $allowedMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMime[$mime])) {
        $_SESSION['error'] = "Format cover harus JPG, PNG, WEBP, atau GIF.";
        header("Location: ../../dashboard/my-board.php");
        exit;
    }

    $ext        = $allowedMime[$mime];
    $filename   = 'board_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $uploadDir  = __DIR__ . '/../../public/uploads/board-covers/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        $_SESSION['error'] = "Gagal menyimpan file cover.";
        header("Location: ../../dashboard/my-board.php");
        exit;
    }

    $coverPathForDb = 'public/uploads/board-covers/' . $filename;
}

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO boards (board_name, description, cover_board, owner, created_at, updated_at)
     VALUES (?, ?, ?, ?, NOW(), NOW())"
);
mysqli_stmt_bind_param($stmt, "sssi", $board_name, $description, $coverPathForDb, $user_id);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION['error'] = "Gagal membuat board, coba lagi.";
    header("Location: ../../dashboard/my-board.php");
    exit;
}

$newBoardId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

$stmtMember = mysqli_prepare(
    $conn,
    "INSERT INTO board_members (board_id, user_id, role, created_at, updated_at)
     VALUES (?, ?, 'admin', NOW(), NOW())"
);
mysqli_stmt_bind_param($stmtMember, "ii", $newBoardId, $user_id);
mysqli_stmt_execute($stmtMember);
mysqli_stmt_close($stmtMember);

$_SESSION['success'] = "Board \"{$board_name}\" berhasil dibuat.";
header("Location: ../../dashboard/board.php?id={$newBoardId}");
exit;
