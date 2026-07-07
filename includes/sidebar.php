<?php

/**
 * Sidebar utama — gabungan rail + sidebar lama jadi satu komponen.
 * Saat "collapsed", sidebar ini mengecil jadi rail (icon-only),
 * bukan digantikan elemen lain. State collapse disimpan di localStorage
 * lewat includes/sidebar-init.php supaya tidak "kilat" saat reload.
 */

// require_once path-nya aman dipanggil dari kedalaman folder mana pun
// (root ATAU dashboard/) karena pakai __DIR__, dan gak bakal connect dobel
// ke database karena require_once melacak berdasarkan path absolut file ini.
require_once __DIR__ . '/../actions/connection.php';

// Badge "Kotak Masuk" nampilin jumlah undangan board yang masih pending,
// biar user langsung tahu ada undangan baru tanpa perlu buka halaman inbox.
$sbPendingInvitations = null;
if (isset($_SESSION['user_id'])) {
    $sbUserId = (int) $_SESSION['user_id'];
    $sbStmt = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS c FROM board_invitations WHERE receiver_id = ? AND status = 'pending'"
    );
    mysqli_stmt_bind_param($sbStmt, "i", $sbUserId);
    mysqli_stmt_execute($sbStmt);
    $sbCount = (int) (mysqli_fetch_assoc(mysqli_stmt_get_result($sbStmt))['c'] ?? 0);
    mysqli_stmt_close($sbStmt);
    $sbPendingInvitations = $sbCount > 0 ? $sbCount : null;
}

$sidebar_main = [
    [
        'icon'   => 'fa-solid fa-table-cells-large',
        'label'  => 'Semua Board',
        'desc'   => 'Semua board yang bisa kamu akses',
        'href'   => '/dashboard.php',
        'pill'   => null,
    ],
    [
        'icon'   => 'fa-solid fa-folder',
        'label'  => 'Board Saya',
        'desc'   => 'Board yang kamu buat',
        'href'   => '/dashboard/my-board.php',
        'pill'   => null,
    ],
    [
        'icon'   => 'fa-solid fa-user-group',
        'label'  => 'Kolaborasi',
        'desc'   => 'Board tempat kamu diundang',
        'href'   => '/dashboard/collaboration.php',
        'pill'   => null
    ],
    [
        'icon'   => 'fa-solid fa-star',
        'label'  => 'Favorit',
        'desc'   => 'Board yang kamu tandai bintang',
        'href'   => '/dashboard/favorite-board.php',
        'pill'   => null,
    ],
];

$sidebar_comm = [

    [
        'icon'   => 'fa-solid fa-inbox',
        'label'  => 'Kotak Masuk',
        'desc'   => 'Undangan board masuk',
        'href'   => '/dashboard/inbox.php',
        'pill'   => $sbPendingInvitations,
        'pill_type' => 'red',
    ],
];

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar" id="sidebar" aria-label="Navigasi Utama">

    <div class="sb-head">
        <div class="sb-brand">
            <div class="sb-logo" title="TaskLab">Ts</div>
            <span class="sb-title" id="navTitle">TaskLab</span>
        </div>
        <button class="sb-collapse-btn" onclick="Sidebar.toggle()" aria-label="Kecilkan sidebar" title="Kecilkan/Perbesar">
            <i class="fa-solid fa-chevron-left" id="sbChevron"></i>
        </button>
    </div>

    <hr class="sb-hr">

    <div class="sb-body" id="sbBody">

        <div class="sb-section">
            <p class="sb-section-label">Board</p>

            <?php foreach ($sidebar_main as $item): ?>
                <?php $isActive = basename($item['href']) === $currentPage; ?>
                <a
                    href="<?= htmlspecialchars($item['href']) ?>"
                    class="sb-item <?= $isActive ? 'active' : '' ?>"
                    data-tip="<?= htmlspecialchars($item['label']) ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <?php if ($item['pill']): ?>
                        <span class="pill pill-<?= $item['pill_type'] ?>" style="margin-left:auto;">
                            <?= $item['pill'] ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="sb-section">
            <p class="sb-section-label">Inbox</p>

            <?php foreach ($sidebar_comm as $item): ?>
                <?php $isActive = basename($item['href']) === $currentPage; ?>
                <a
                    href="<?= htmlspecialchars($item['href']) ?>"
                    class="sb-item <?= $isActive ? 'active' : '' ?>"
                    data-tip="<?= htmlspecialchars($item['label']) ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <?php if ($item['pill']): ?>
                        <span class="pill pill-<?= $item['pill_type'] ?>" style="margin-left:auto;">
                            <?= $item['pill'] ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- <div class="sb-group">
                <button class="sb-group-toggle open" onclick="Sidebar.toggleGroup(this)" aria-expanded="true">
                    <span>Board Kamu</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <div class="sb-group-body">

                    <a href="board-detail.php?board=1" class="sb-item sb-item-sub active" data-tip="Quilla">
                        <i class="fa-solid fa-folder"></i>
                        <span>Quilla</span>
                    </a>

                    <button class="sb-item sb-item-add" onclick="openBoardModal()" data-tip="Tambah Board">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Board</span>
                    </button>
                </div>
            </div> -->

    </div>

    <div class="sb-footer">
        <button class="sb-footer-btn" data-tip="Ganti Tema" aria-label="Ganti mode terang/gelap" onclick="Theme.toggle()">
            <i class="fa-solid fa-moon"></i>
            <span class="sb-footer-label">Ganti Tema</span>
        </button>
        <button class="sb-footer-btn" data-tip="Pengaturan" aria-label="Pengaturan">
            <i class="fa-solid fa-gear"></i>
            <span class="sb-footer-label">Pengaturan</span>
        </button>
    </div>

</nav>

<script>
    (function() {
        var collapsed = localStorage.getItem('tasklab-sidebar-collapsed') === '1';
        var el = document.getElementById('sidebar');
        if (el && collapsed) el.classList.add('collapsed');
        var chevron = document.getElementById('sbChevron');
        if (chevron && collapsed) chevron.style.transform = 'rotate(180deg)';
    })();
</script>