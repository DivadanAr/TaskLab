<?php
$env = parse_ini_file('.env');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TaskLab — Kelola Tugas Tim, Tanpa Ribet</title>

    <?php include 'includes/theme-init.php'; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="public/css/global.css" />
    <link rel="stylesheet" href="public/css/theme.css" />
    <link rel="stylesheet" href="public/css/landing.css" />
</head>

<body>

    <header class="lp-nav">
        <a href="landing.php" class="lp-brand">
            <div class="lp-logo">Ts</div>
            <span>TaskLab</span>
        </a>


        <div class="lp-nav-actions">
            <button class="lp-theme-toggle" onclick="Theme.toggle()" aria-label="Ganti mode terang/gelap">
                <i class="fa-solid fa-moon"></i>
                <i class="fa-solid fa-sun"></i>
            </button>
            <a href="login.php" class="lp-btn lp-btn-ghost">Masuk</a>
            <a href="register.php" class="lp-btn lp-btn-primary">Daftar Gratis</a>
        </div>
    </header>

    <main>

        <section class="lp-hero">
            <div class="lp-hero-copy">
                <span class="lp-eyebrow"><i class="fa-solid fa-sparkles"></i> To-Do</span>
                <h1>Satu board untuk<br>semua tugas timmu.</h1>
                <p class="lp-hero-sub">
                    TaskLab menggabungkan board, chat tim, dan undangan kolaborasi
                    dalam satu tempat, supaya kamu berhenti bolak-balik antar aplikasi.
                </p>
                <div class="lp-hero-actions">
                    <a href="register.php" class="lp-btn lp-btn-primary lp-btn-lg">
                        Mulai Gratis <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="login.php" class="lp-btn lp-btn-ghost lp-btn-lg">Sudah punya akun?</a>
                </div>
                <p class="lp-hero-note"><i class="fa-solid fa-check"></i> Tanpa kartu kredit &nbsp;·&nbsp; Setup kurang dari 1 menit</p>
            </div>

            <div class="lp-hero-visual" aria-hidden="true">
                <div class="lp-mock-window">
                    <div class="lp-mock-topbar">
                        <span class="lp-mock-dot"></span>
                        <span class="lp-mock-dot"></span>
                        <span class="lp-mock-dot"></span>
                    </div>
                    <div class="lp-mock-board">
                        <div class="lp-mock-col">
                            <p class="lp-mock-col-title">Belum Dikerjakan</p>
                            <div class="lp-mock-card"><span class="lp-mock-tag lp-mock-tag-purple">Desain</span>Wireframe halaman login</div>
                            <div class="lp-mock-card"><span class="lp-mock-tag lp-mock-tag-amber">Riset</span>Kumpulkan feedback user</div>
                        </div>
                        <div class="lp-mock-col">
                            <p class="lp-mock-col-title">Dikerjakan</p>
                            <div class="lp-mock-card lp-mock-card-accent"><span class="lp-mock-tag lp-mock-tag-green">Dev</span>Integrasi board chat</div>
                        </div>
                        <div class="lp-mock-col">
                            <p class="lp-mock-col-title">Selesai</p>
                            <div class="lp-mock-card lp-mock-card-done"><span class="lp-mock-tag lp-mock-tag-green">QA</span>Uji coba undangan tim</div>
                        </div>
                    </div>
                </div>
                <div class="lp-mock-chat">
                    <div class="lp-mock-chat-avatar">RS</div>
                    <div class="lp-mock-chat-bubble">Board barunya udah aku share ya 👍</div>
                </div>
            </div>
        </section>

        <!-- ================= LOGOS / TRUST (ringkas) =================
        <section class="lp-stats">
            <div class="lp-stat"><strong>3-in-1</strong><span>Board, chat, undangan</span></div>
            <div class="lp-stat"><strong>Real-time</strong><span>Update tanpa refresh</span></div>
            <div class="lp-stat"><strong>Gratis</strong><span>Untuk tim kecil</span></div>
        </section>
 -->
        <section class="lp-features" id="fitur">
            <p class="lp-section-eyebrow">Fitur</p>
            <h2 class="lp-section-title">Semua yang tim kamu butuh, sudah ada di dalam</h2>

            <div class="lp-feature-grid">
                <div class="lp-feature-card">
                    <div class="lp-feature-icon lp-feature-icon-purple"><i class="fa-solid fa-table-cells-large"></i></div>
                    <h3>Board</h3>
                    <p>Atur tugasmu sesuai progres, dari ide sampai selesai.</p>
                </div>
                <div class="lp-feature-card">
                    <div class="lp-feature-icon lp-feature-icon-green"><i class="fa-regular fa-comments"></i></div>
                    <h3>Comment Tim</h3>
                    <p>Diskusi langsung di dalam board, tanpa perlu pindah ke aplikasi chat lain.</p>
                </div>
                <div class="lp-feature-card">
                    <div class="lp-feature-icon lp-feature-icon-amber"><i class="fa-solid fa-user-group"></i></div>
                    <h3>Undang & Kolaborasi</h3>
                    <p>Undang rekan kerja ke board tertentu, kelola akses lewat kotak masuk undangan.</p>
                </div>
                <div class="lp-feature-card">
                    <div class="lp-feature-icon lp-feature-icon-purple"><i class="fa-solid fa-star"></i></div>
                    <h3>Board Favorit</h3>
                    <p>Tandai board yang sering kamu pakai supaya selalu ada di jangkauan tangan.</p>
                </div>
            </div>
        </section>

        <section class="lp-steps" id="alur">
            <p class="lp-section-eyebrow">Cara Kerja</p>
            <h2 class="lp-section-title">Mulai dalam tiga langkah</h2>

            <div class="lp-steps-grid">
                <div class="lp-step">
                    <span class="lp-step-num">01</span>
                    <h3>Buat akun</h3>
                    <p>Daftar dengan email kamu, tidak perlu kartu kredit.</p>
                </div>
                <div class="lp-step">
                    <span class="lp-step-num">02</span>
                    <h3>Buat board pertama</h3>
                    <p>Susun kolom sesuai alur kerja tim kamu sendiri.</p>
                </div>
                <div class="lp-step">
                    <span class="lp-step-num">03</span>
                    <h3>Ajak tim kamu</h3>
                    <p>Undang rekan kerja dan mulai kerja bareng secara real-time.</p>
                </div>
            </div>
        </section>

        <section class="lp-cta">
            <h2>Siap merapikan alur kerja timmu?</h2>
            <p>Gratis untuk mulai, tidak perlu setup rumit.</p>
            <a href="register.php" class="lp-btn lp-btn-primary lp-btn-lg">
                Daftar Sekarang <i class="fa-solid fa-arrow-right"></i>
            </a>
        </section>

    </main>

    <footer class="lp-footer">
        <div class="lp-brand">
            <div class="lp-logo">Ts</div>
            <span>TaskLab</span>
        </div>
        <p>&copy; <?= date('Y') ?> TaskLab. Dibuat untuk tim yang suka kerja rapi.</p>
    </footer>

    <script src="<?= $env['BASE_URL'] ?>/public/js/theme.js"></script>
</body>

</html>