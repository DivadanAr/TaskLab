<?php

include "../actions/connection.php";
include "../actions/board/detail_board_get.php";
include "../actions/board/board_collab_get.php";
include "../actions/collaboration/invite_helper.php";

$page_title = $board['board_name'] . ' Board';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$board_id = (int) $_GET['id'];
$tasks  = getTasksByBoard($board_id);

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);
$canInvite        = canInviteToBoard($conn, $board_id, $current_user_id);

$inviteQuery   = trim((string) ($_GET['invite_q'] ?? ''));
$inviteResults = $canInvite ? searchInvitableUsers($conn, $board_id, $inviteQuery, $current_user_id) : [];
$showInviteModal = $canInvite && isset($_GET['invite_q']);

function initials($name)
{
    if (empty($name)) return '?';

    $words = preg_split('/\s+/', trim($name));
    $words = array_slice($words, 0, 2);

    $initial = '';

    foreach ($words as $word) {
        $initial .= strtoupper(substr($word, 0, 1));
    }

    return $initial;
}

function avatarColorClass($id)
{
    $classes = ['a1', 'a2', 'a3'];

    $sum = 0;
    foreach (str_split((string)$id) as $char) {
        $sum += ord($char);
    }

    return $classes[$sum % count($classes)];
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
    <link rel="stylesheet" href="../public/css/board-detail.css" />
    <link rel="stylesheet" href="../public/css/board-info-panel.css" />
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
            <div class="root">

                <main class="main-board">
                    <div class="board-detail">
                        <div class="toolbar">

                            <a href="/dashboard.php" class="btn-back">
                                <i class="fa-solid fa-arrow-left"></i> Kembali
                            </a>

                            <button class="btn-add" onclick="openTodoModal(<?= $board['id'] ?>)">
                                <i class="fa-solid fa-plus"></i> Tambah Tugas
                            </button>

                        </div>

                        <div class="board-detail-header">

                            <div class="board-detail-left">

                                <div class="group-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h.01M3 12h.01M3 19h.01M8 5h13M8 12h13M8 19h13" />
                                    </svg>
                                </div>

                                <div>
                                    <h1 class="board-detail-title" id="boardTitle">
                                        <?= $board['board_name'] ?>
                                    </h1>

                                    <p class="board-detail-desc" id="boardDesc">
                                        <?= $board['description'] ?>
                                    </p>
                                </div>

                            </div>

                            <div class="board-detail-actions">
                                <?php if ($canInvite): ?>
                                    <button class="btn-cancel" id="boardInviteToggleBtn"
                                        onclick="document.getElementById('inviteModal').classList.add('show')">
                                        <i class="fa-solid fa-user-plus"></i>
                                        <span style="margin-left: 5px;">Undang</span>
                                    </button>
                                <?php endif; ?>

                                <button class="btn-cancel" id="boardChatToggleBtn" onclick="BoardInfoPanel.toggle()">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span style="margin-left: 5px;">Info</span>
                                </button>

                            </div>

                        </div>

                        <div class="divider-section"></div>

                        <div class="board-detail-list" id="boardDetailBody">

                            <div class="tasks-list tasks-list-detail">

                                <?php if (mysqli_num_rows($tasks) === 0): ?>

                                    <div class="empty-state">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                        <p>Belum ada tugas di board ini.</p>
                                    </div>

                                <?php else: ?>


                                    <?php while ($task = mysqli_fetch_assoc($tasks)): ?>

                                        <?php
                                        $dot = "gray";
                                        switch ($task['task_priority']) {
                                            case "HIGH":
                                                $dot = "#FF3483";
                                                break;
                                            case "MEDIUM":
                                                $dot = "#FFCF00";
                                                break;
                                            case "LOW":
                                                $dot = "#5996FF";
                                                break;
                                        }
                                        $isDone = $task['task_status'] === 'DONE';
                                        ?>

                                        <div class="task-card <?= $isDone ? 'done' : '' ?>">

                                            <div class="task-check <?= $isDone ? 'done' : '' ?>"
                                                data-task-id="<?= $task['id'] ?>"
                                                onclick="event.stopPropagation(); TaskToggle.toggle(this)"></div>

                                            <div class="task-body" onclick="TaskDetailModal.open(<?= $task['id'] ?>)">

                                                <div class="task-title">
                                                    <?= $task['task_name'] ?>
                                                </div>

                                                <div class="task-desc">
                                                    <?= $task['description'] ?>
                                                </div>

                                                <div class="task-meta">

                                                    <?php if (!empty($task['due_date'])): ?>
                                                        <div class="task-meta-item">
                                                            <i class="fa-solid fa-calendar-day"></i>
                                                            <span><?= date('Y-m-d', strtotime($task['due_date'])) ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>

                                            </div>
                                            <div class="task-right">
                                                <i class="fa-solid fa-flag" style="color: <?= $dot ?>"></i>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>

                                <?php endif; ?>


                            </div>

                        </div>
                    </div>
                    <div class="board-info-panel" id="boardInfoPanel">
                        <div class="board-info-panel-inner">

                            <div class="board-info-panel-header">
                                <span>Detail Board</span>
                                <button class="modal-close" onclick="BoardInfoPanel.close()">
                                    <i class="fa-solid fa-x" style="font-size: 13px;"></i>
                                </button>
                            </div>

                            <div class="board-info-panel-body">
                                <input type="hidden" id="boardId" value="<?= $board['id'] ?>" name="boar_id">
                                <div class="board-info-name-wrap">
                                    <?php if ($canInvite): ?>
                                        <input type="text" class="board-info-name-input" id="boardInfoNameInput"
                                            value="<?= $board['board_name'] ?>"
                                            onblur="BoardInfoPanel.onNameBlur(this)" />
                                        <i class="fa-solid fa-pen"></i>
                                    <?php else: ?>
                                        <p class="board-info-name-input">
                                            <?= $board['board_name'] ?>
                                        </p>
                                    <?php endif ?>

                                </div>
                                <div class="board-info-save-hint" id="boardInfoSaveHint">
                                    <i class="fa-solid fa-check"></i> Tersimpan
                                </div>

                                <label class="board-info-label">Deskripsi</label>

                                <div class="richtext-block" id="boardDescBlock" style="margin-bottom: 15px;">
                                    <?php if ($canInvite): ?>
                                        <div class="richtext-viewer" id="boardDescViewer"
                                            onclick="BoardInfoPanel.enterDescEditMode()">
                                            <div class="richtext-viewer-content" id="boardDescViewerContent">
                                                <?= $board['description'] ?>
                                            </div>
                                            <?php if ($canInvite): ?>
                                                <div class="richtext-viewer-hint">
                                                    <i class="fa-solid fa-pen"></i> Klik untuk edit
                                                </div>
                                            <?php endif ?>
                                        </div>

                                    <?php else: ?>
                                        <div class="richtext-viewer" id="boardDescViewer">
                                            <div class="richtext-viewer-content" id="boardDescViewerContent">
                                                <?= $board['description'] ?>
                                            </div>
                                            <?php if ($canInvite): ?>
                                                <div class="richtext-viewer-hint">
                                                    <i class="fa-solid fa-pen"></i> Klik untuk edit
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>

                                    <div class="richtext-wrap" id="boardDescEditWrap" style="display:none;">
                                        <div class="richtext-toolbar">
                                            <button type="button" data-cmd="bold" title="Bold" onmousedown="event.preventDefault()" onclick="RichEditor.exec('bold')">
                                                <i class="fa-solid fa-bold"></i>
                                            </button>
                                            <button type="button" data-cmd="italic" title="Italic" onmousedown="event.preventDefault()" onclick="RichEditor.exec('italic')">
                                                <i class="fa-solid fa-italic"></i>
                                            </button>
                                            <button type="button" data-cmd="underline" title="Underline" onmousedown="event.preventDefault()" onclick="RichEditor.exec('underline')">
                                                <i class="fa-solid fa-underline"></i>
                                            </button>
                                            <button type="button" data-cmd="strikeThrough" title="Strikethrough" onmousedown="event.preventDefault()" onclick="RichEditor.exec('strikeThrough')">
                                                <i class="fa-solid fa-strikethrough"></i>
                                            </button>

                                            <span class="richtext-sep"></span>

                                            <button type="button" data-cmd="insertUnorderedList" title="Bullet List" onmousedown="event.preventDefault()" onclick="RichEditor.exec('insertUnorderedList')">
                                                <i class="fa-solid fa-list-ul"></i>
                                            </button>
                                            <button type="button" data-cmd="insertOrderedList" title="Numbered List" onmousedown="event.preventDefault()" onclick="RichEditor.exec('insertOrderedList')">
                                                <i class="fa-solid fa-list-ol"></i>
                                            </button>

                                            <span class="richtext-sep"></span>

                                            <button type="button" data-cmd="justifyLeft" title="Rata Kiri" onmousedown="event.preventDefault()" onclick="RichEditor.exec('justifyLeft')">
                                                <i class="fa-solid fa-align-left"></i>
                                            </button>
                                            <button type="button" data-cmd="justifyCenter" title="Rata Tengah" onmousedown="event.preventDefault()" onclick="RichEditor.exec('justifyCenter')">
                                                <i class="fa-solid fa-align-center"></i>
                                            </button>
                                            <button type="button" data-cmd="justifyRight" title="Rata Kanan" onmousedown="event.preventDefault()" onclick="RichEditor.exec('justifyRight')">
                                                <i class="fa-solid fa-align-right"></i>
                                            </button>

                                            <span class="richtext-sep"></span>

                                            <button type="button" title="Hapus Format" onmousedown="event.preventDefault()" onclick="RichEditor.exec('removeFormat')">
                                                <i class="fa-solid fa-eraser"></i>
                                            </button>
                                        </div>

                                        <div class="richtext-editor" id="boardDescEditor" contenteditable="true"
                                            data-placeholder="Tulis deskripsi board di sini…"
                                            onblur="BoardInfoPanel.onDescBlur(this)">
                                            <p><?= $board['description'] ?></p>
                                        </div>
                                    </div>

                                </div>

                                <label class="task-detail-label" style="padding-top: 5px; margin-bottom: 3px;">Collaboration</label>
                                <div class="assignee-row-board" id="taskAssigneeRowBoard">
                                    <?php foreach ($assigneesBoard as $member): ?>
                                        <div class="assignee-chip">

                                            <?php if (empty($member['avatar'])): ?>
                                                <div
                                                    class="avatar-mini <?= avatarColorClass(1) ?>"
                                                    title="<?= htmlspecialchars($member['name']) ?>">
                                                    <?= initials($member['name']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <span><?= $member['name'] ?></span>
                                            <button class="assignee-remove" onclick="TaskDetailModal.removeAssigneeBoard(<?= $member['user_id'] ?>, <?= $board['id'] ?>)">
                                                <i class="fa-solid fa-x"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <?php if ($canInvite): ?>
                                <div class="board-info-panel-footer">
                                    <button type="button" class="btn-delete-board" onclick="BoardInfoPanel.deleteBoard(<?= $board['id'] ?>)">
                                        <i class="fa-solid fa-trash"></i> Hapus Board
                                    </button>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                </main>



                <div class="modal-overlay" id="taskDetailModal">

                    <div class="modal task-detail-modal" style="width: 78%;">

                        <div class="modal-header">
                            <div class="task-detail-header-main">
                                <input type="text"
                                    class="task-detail-title-input"
                                    id="taskDetailTitleInput"
                                    placeholder="Nama tugas"
                                    onblur="TaskDetailModal.onTitleBlur(this)" />
                                <div class="task-detail-save-hint" id="taskDetailSaveHint">
                                    <i class="fa-solid fa-check"></i> Tersimpan
                                </div>
                            </div>
                            <button class="modal-close" onclick="TaskDetailModal.close()">
                                <i class="fa-solid fa-x" style="font-size: 15px;"></i>
                            </button>
                        </div>

                        <div class="modal-body task-detail-body">

                            <div class="task-detail-main">

                                <div class="task-detail-section">
                                    <label class="task-detail-label">Ditugaskan ke</label>
                                    <div class="assignee-row" id="taskAssigneeRow">

                                        <button class="btn-add-assignee" id="btnAddAssignee" onclick="TaskDetailModal.togglePicker('task')">
                                            <i class="fa-solid fa-plus"></i> Assign
                                        </button>

                                        <div class="assignee-picker open" id="assigneePicker-task">
                                            <div class="assignee-picker-search">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                <input type="text" placeholder="Cari anggota..." oninput="TaskDetailModal.filterPicker('task', this.value)">
                                            </div>
                                            <div class="assignee-picker-list" id="assigneePickerList-task"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="task-detail-section">
                                    <label class="task-detail-label">Deskripsi</label>
                                    <textarea
                                        class="task-detail-desc-input"
                                        id="taskDetailDescInput"
                                        placeholder="Tulis deskripsi tugas..."
                                        onblur="TaskDetailModal.onDescBlur(this)"></textarea>
                                </div>

                                <div class="task-detail-section">
                                    <div class="subtask-header">
                                        <label class="task-detail-label" style="margin:0;">Subtask</label>
                                        <span class="subtask-progress" id="subtaskProgress">0/0</span>
                                    </div>

                                    <div class="subtask-progress-bar">
                                        <div class="subtask-progress-fill" id="subtaskProgressFill" style="width:0%;"></div>
                                    </div>

                                    <div class="subtask-list" id="subtaskList">

                                    </div>

                                    <div class="subtask-add-row">
                                        <i class="fa-solid fa-plus"></i>
                                        <input type="text"
                                            id="subtaskAddInput"
                                            placeholder="Tambah subtask, lalu tekan Enter"
                                            onkeydown="TaskDetailModal.onSubtaskInputKeydown(event)" />
                                    </div>
                                </div>

                            </div>

                            <div class="task-detail-comments">
                                <div class="comments-header">
                                    <i class="fa-solid fa-comments"></i> Komentar
                                </div>

                                <div class="comments-list" id="commentsList">
                                </div>

                                <div class="comment-composer">
                                    <textarea
                                        id="commentInput"
                                        rows="1"
                                        placeholder="Tulis komentar..."
                                        onkeydown="TaskDetailModal.onCommentInputKeydown(event)"></textarea>
                                    <button class="comment-send-btn" onclick="TaskDetailModal.addComment()">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-delete-board" onclick="TaskDetailModal.deleteTask()">
                                <i class="fa-solid fa-trash"></i> Hapus Tugas
                            </button>
                            <button type="button" class="btn-cancel" onclick="TaskDetailModal.close()">Tutup</button>
                        </div>

                    </div>

                </div>



                <div class="modal-overlay" id="confirmModal">

                    <div class="modal modal-confirm">

                        <div class="confirm-icon-wrap">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <h2 id="confirmTitle" class="confirm-title">Hapus Subtask ini?</h2>

                        <p id="confirmMessage" class="confirm-message">Tindakan ini tidak bisa dibatalkan.</p>

                        <div class="confirm-actions">
                            <button type="button" class="btn-cancel" onclick="document.getElementById('confirmModal').classList.remove('show');">Batal</button>
                            <button type="button" class="btn-confirm-danger" id="confirmActionBtn" onclick="TaskDetailModal.deleteTask()">
                                <i class="fa-solid fa-trash"></i> Ya, Hapus
                            </button>
                        </div>

                    </div>

                </div>

                <?php if ($canInvite): ?>
                    <div class="modal-overlay <?= $showInviteModal ? 'show' : '' ?>" id="inviteModal">

                        <div class="modal" style="width: 480px;">

                            <div class="modal-header">
                                <div>
                                    <h2>Undang Anggota</h2>
                                    <p>Cari nama atau email pengguna untuk diundang ke board ini.</p>
                                </div>
                                <button class="modal-close" onclick="closeInviteModal()">
                                    <i class="fa-solid fa-x" style="font-size: 15px;"></i>
                                </button>
                            </div>

                            <div class="invite-body">

                                <form action="board.php" method="GET" class="sb-search invite-search-form">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="hidden" name="id" value="<?= (int) $board_id ?>">
                                    <input type="text" name="invite_q" value="<?= htmlspecialchars($inviteQuery) ?>"
                                        placeholder="Ketik nama atau email pengguna…" autofocus>
                                    <button type="submit" class="btn-save">Cari</button>
                                </form>

                                <div class="invite-search-results">

                                    <?php if ($inviteQuery === ''): ?>

                                        <p class="invite-hint">Ketik nama atau email untuk mulai mencari pengguna.</p>

                                    <?php elseif (empty($inviteResults)): ?>

                                        <p class="invite-hint">Tidak ada pengguna yang cocok / bisa diundang.</p>

                                    <?php else: ?>

                                        <?php foreach ($inviteResults as $u): ?>
                                            <div class="invite-result-item">
                                                <div class="avatar-mini a1"><?= getInitials($u['name']) ?></div>
                                                <div class="invite-result-info">
                                                    <div class="invite-result-name"><?= htmlspecialchars($u['name']) ?></div>
                                                    <div class="invite-result-email"><?= htmlspecialchars($u['email']) ?></div>
                                                </div>
                                                <form action="../actions/collaboration/invite_post.php" method="POST">
                                                    <input type="hidden" name="board_id" value="<?= (int) $board_id ?>">
                                                    <input type="hidden" name="receiver_id" value="<?= (int) $u['id'] ?>">
                                                    <input type="hidden" name="invite_q" value="<?= htmlspecialchars($inviteQuery) ?>">
                                                    <button type="submit" class="btn-add-assignee">
                                                        <i class="fa-solid fa-user-plus"></i> Undang
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>
                <?php endif; ?>
            </div>


        </div>

    </div>
    <script>
        function closeInviteModal() {
            document.getElementById("inviteModal").classList.remove("show");

            const url = new URL(window.location.href);
            url.searchParams.delete("invite_q");
            history.replaceState({}, "", url);
        }
    </script>
    <?php include '../includes/modal-add-task.php' ?>
    <script src="../public/js/theme.js"></script>
    <script src="../public/js/app.js"></script>
    <script src="../public/js/task.js"></script>
    <script src="../public/js/task-toggle.js"></script>
    <script src="../public/js/board-info-panel.js"></script>
    <script src="../public/js/sidebar.js"></script>

</body>

</html>