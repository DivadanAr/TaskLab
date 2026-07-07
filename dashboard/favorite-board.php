<?php

include "../actions/my-board/my-starred_board_get.php";
include "../actions/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TaskLab — Dashboard</title>

    <?php include '../includes/theme-init.php'; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="../public/css/profile.css">
    <link rel="stylesheet" href="../public/css/global.css" />
    <link rel="stylesheet" href="../public/css/theme.css" />
    <link rel="stylesheet" href="../public/css/index.css" />
    <link rel="stylesheet" href="../public/css/modal.css" />
    <link rel="stylesheet" href="../public/css/header.css" />
    <link rel="stylesheet" href="../public/css/sidebar.css" />

</head>

<body>

    <div class="shell">
        <?php include '../includes/sidebar.php'; ?>
        <div class="content-col">
            <?php include '../includes/header.php'; ?>
            <main class="content-body" id="contentBody" role="main">
                <div class="root">
                    <div class="toolbar">
                        <div class="sb-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="sbSearch" placeholder="Cari tugas, proyek…" aria-label="Cari" />
                        </div>
                        <button class="btn-add" onclick="openBoardModal()">
                            <i class="fa-solid fa-plus"></i> Tambah Board
                        </button>
                    </div>

                    <p class="board-title">Your Collaboration Workspaces</p>
                    <?php if (mysqli_num_rows($starredboards) === 0): ?>

                        <div class="empty-state">
                            <i class="fa-solid fa-folder"></i>
                            <p>Kamu belum ada board favorite:(</p>
                        </div>

                    <?php endif ?>

                    <div class="board-container">
                        <?php if (mysqli_num_rows($starredboards) > 0): ?>

                            <?php while ($board = mysqli_fetch_assoc($starredboards)): ?>
                                <a href="/dashboard/board.php?id=<?= $board['id'] ?>">
                                    <div class="board-card" data-board-id="board-1">
                                        <button class="board-card-star <?= $board['starred'] ? "starred" : "" ?>" aria-label="Tandai sebagai favorit">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                        <?php if ($board['cover_board'] !== null): ?>
                                            <div class="board-card-placeholder">
                                                <?= getInitials($board['board_name']) ?>
                                            </div>
                                        <?php else : ?>
                                            <img class="board-card-image" src="https://picsum.photos/400/200?2" alt="Board">
                                        <?php endif ?>
                                        <div class="board-card-content">
                                            <span class="board-card-title"><?= $board['board_name'] ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php endif ?>

                    </div>



            </main>


        </div>

    </div>

    <?php include '../includes/modal-add-board.php' ?>
    <?php include '../includes/modal-add-task.php' ?>

    <script>
        (function() {
            var theme = localStorage.getItem('tasklab-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <script src="../public/js/theme.js"></script>
    <script src="../public/js/app.js"></script>
    <script src="../public/js/sidebar.js"></script>

</body>

</html>