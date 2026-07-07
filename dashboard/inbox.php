<?php

include "../actions/connection.php";
include "../actions/collaboration/inbox_get.php";

$page_title = 'Inbox';

$invitationCount = mysqli_num_rows($invitations);
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
    <title>TaskLab — Kotak Masuk</title>

    <?php include '../includes/theme-init.php'; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="../public/css/profile.css">
    <link rel="stylesheet" href="../public/css/global.css" />
    <link rel="stylesheet" href="../public/css/theme.css" />
    <link rel="stylesheet" href="../public/css/index.css" />
    <link rel="stylesheet" href="../public/css/modal.css" />
    <link rel="stylesheet" href="../public/css/header.css" />
    <link rel="stylesheet" href="../public/css/sidebar.css" />
    <link rel="stylesheet" href="../public/css/inbox.css" />

</head>

<body>

    <div class="shell">
        <?php include '../includes/sidebar.php'; ?>
        <div class="content-col">
            <?php include '../includes/header.php'; ?>
            <main class="content-body" id="contentBody" role="main">
                <div class="root">

                    <div class="inbox-header">
                        <h1 class="inbox-title">Kotak Masuk</h1>
                        <p class="inbox-subtitle">Undangan board dan notifikasi lain yang masuk untuk kamu.</p>
                    </div>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="flash-banner flash-success">
                            <i class="fa-solid fa-circle-check"></i>
                            <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="flash-banner flash-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="inbox-tabs">
                        <button class="inbox-tab active" onclick="Inbox.switchTab(this, 'invitations')">
                            Undangan
                            <?php if ($invitationCount > 0): ?>
                                <span class="pill pill-purple"><?= $invitationCount ?></span>
                            <?php endif; ?>
                        </button>
                        <button class="inbox-tab" onclick="Inbox.switchTab(this, 'notifications')">
                            Notifikasi
                        </button>
                    </div>

                    <!-- ============ TAB: UNDANGAN BOARD ============ -->
                    <div class="inbox-list" data-inbox-panel="invitations" style="display:flex;">

                        <div id="inviteList" style="display:contents;">

                            <?php if ($invitationCount > 0): ?>

                                <?php while ($invite = mysqli_fetch_assoc($invitations)): ?>
                                    <div class="invite-card">
                                        <div class="invite-card-icon"><?= getInitials($invite['board_name']) ?></div>
                                        <div class="invite-card-body">
                                            <div class="invite-card-text">
                                                <b><?= htmlspecialchars($invite['sender_name']) ?></b> mengundang kamu untuk berkolaborasi di board
                                                <span class="board-name"><?= htmlspecialchars($invite['board_name']) ?></span>
                                            </div>
                                            <div class="invite-card-meta">
                                                <div class="inviter-avatar"><?= getInitials($invite['sender_name']) ?></div>
                                                <span>Diundang <?= timeAgoID($invite['invited_at']) ?></span>
                                            </div>
                                        </div>
                                        <div class="invite-card-actions">
                                            <form action="../actions/collaboration/invitation_respond.php" method="POST">
                                                <input type="hidden" name="invitation_id" value="<?= (int) $invite['invitation_id'] ?>">
                                                <button type="submit" name="action" value="reject" class="btn-invite-decline">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            </form>
                                            <form action="../actions/collaboration/invitation_respond.php" method="POST">
                                                <input type="hidden" name="invitation_id" value="<?= (int) $invite['invitation_id'] ?>">
                                                <button type="submit" name="action" value="accept" class="btn-invite-accept">
                                                    <i class="fa-solid fa-check"></i> Terima
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>

                            <?php endif; ?>

                        </div>

                        <div class="empty-state" id="inviteEmptyState" style="<?= $invitationCount > 0 ? 'display:none;' : 'display:flex;' ?>">
                            <i class="fa-solid fa-envelope-open"></i>
                            <p>Tidak ada undangan yang tersisa.</p>
                        </div>

                    </div>

                    <!-- ============ TAB: NOTIFIKASI LAIN ============ -->
                    <?php if (mysqli_num_rows($notifications) > 0): ?>
                        <div class="inbox-list" data-inbox-panel="notifications" style="display:none;">

                            <div id="inviteList" style="display:contents;">


                                <?php while ($notif = mysqli_fetch_assoc($notifications)): ?>
                                    <div class="invite-card">
                                        <div class="invite-card-icon"><?= getInitials($notif['board_name']) ?></div>
                                        <div class="invite-card-body">
                                            <div class="invite-card-text">
                                                <b><?= htmlspecialchars($notif['sender_name']) ?></b> mengundang kamu untuk berkolaborasi di board
                                                <span class="board-name"><?= htmlspecialchars($notif['board_name']) ?></span>
                                            </div>
                                            <div class="invite-card-meta">
                                                <div class="inviter-avatar"><?= getInitials($notif['sender_name']) ?></div>
                                                <span>Diundang <?= timeAgoID($notif['invited_at']) ?></span>
                                            </div>
                                        </div>
                                        <div class="invite-card-actions">
                                            <div class="btn-invite-decline">
                                                <?= $notif['status'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>


                            </div>
                        <?php else: ?>

                            <div class="empty-state">
                                <i class="fa-solid fa-bell-slash"></i>
                                <p>Belum ada notifikasi.</p>
                            </div>
                        <?php endif; ?>


                        </div>

                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/modal-add-board.php' ?>
    <?php include '../includes/modal-add-task.php' ?>

    <script src="../public/js/theme.js"></script>
    <script src="../public/js/app.js"></script>
    <script src="../public/js/sidebar.js"></script>
    <script src="../public/js/inbox.js"></script>

</body>

</html>