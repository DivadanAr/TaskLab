<?php
$env = parse_ini_file('.env');
if (isset($_SESSION['user_id'])) {
    header("Location: ./dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar — TaskLab</title>

    <?php include 'includes/theme-init.php'; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="public/css/global.css" />
    <link rel="stylesheet" href="public/css/theme.css" />
    <link rel="stylesheet" href="public/css/auth.css" />
    <link rel="stylesheet" href="public/css/index.css" />
    <link rel="stylesheet" href="public/css/modal.css" />
    <link rel="stylesheet" href="public/css/header.css" />
    <link rel="stylesheet" href="public/css/sidebar.css" />

</head>

<body>

    <div class="auth-shell">

        <a href="landing.php" class="auth-brand">
            <div class="auth-logo">Ts</div>
            <span>TaskLab</span>
        </a>

        <div class="auth-card">
            <div class="auth-card-head">
                <h1>Buat akun baru</h1>
                <p>Masukan Nama, Email dan password kamu untuk menggunakan TaskLab</p>
            </div>

            <form class="auth-form" id="registerForm" action="actions/register_process.php" method="POST" novalidate>

                <div class="form-group">
                    <label for="reg_name">Nama Lengkap</label>
                    <input type="text" id="reg_name" name="name" placeholder="Nama kamu" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" placeholder="nama@email.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="reg_password">Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" id="reg_password" name="password" placeholder="Minimal 8 karakter" required minlength="8" autocomplete="new-password">
                        <button type="button" class="auth-toggle-pass" onclick="AuthForm.togglePassword('reg_password', this)" aria-label="Tampilkan password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg_password_confirm">Konfirmasi Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" id="reg_password_confirm" name="password_confirm" placeholder="Ulangi password" required minlength="8" autocomplete="new-password">
                        <button type="button" class="auth-toggle-pass" onclick="AuthForm.togglePassword('reg_password_confirm', this)" aria-label="Tampilkan password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <label class="auth-checkbox auth-checkbox-terms">
                    <input type="checkbox" name="agree" required>
                    <span>Saya setuju dengan <a href="#">Syarat Layanan</a> &amp; <a href="#">Kebijakan Privasi</a></span>
                </label>

                <div id="registerFormMessage" class="auth-message"></div>

                <button type="submit" class="auth-submit" id="registerSubmitBtn">
                    Daftar Gratis <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="auth-switch">
                Sudah punya akun? <a href="login.php">Masuk</a>
            </p>
        </div>

    </div>

    <script src="<?= $env['BASE_URL'] ?>public/js/theme.js"></script>
    <script src="<?= $env['BASE_URL'] ?>public/js/auth.js"></script>
</body>

</html>