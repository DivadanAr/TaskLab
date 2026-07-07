<?php
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
    <title>Masuk — TaskLab</title>

    <script>
        (function() {
            var theme = localStorage.getItem('tasklab-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

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
                <h1>Selamat datang kembali</h1>
                <p>Masuk untuk lanjut kelola board dan tugas timmu.</p>
            </div>

            <form class="auth-form" id="loginForm" action="actions/login_process.php" method="POST" novalidate>

                <div class="form-group">
                    <label for="login_email">Email</label>
                    <input type="email" id="login_email" name="email" placeholder="nama@email.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="login_password">Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" id="login_password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="auth-toggle-pass" onclick="AuthForm.togglePassword('login_password', this)" aria-label="Tampilkan password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-row-between">
                    <label class="auth-checkbox">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="forgot-password.php" class="auth-link-sm">Lupa password?</a>
                </div>

                <div id="loginFormMessage" class="auth-message"></div>

                <button type="submit" class="auth-submit" id="loginSubmitBtn">
                    Masuk <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="auth-switch">
                Belum punya akun? <a href="register.php">Daftar gratis</a>
            </p>
        </div>

    </div>

    <script src="public/js/theme.js"></script>
    <script src="public/js/auth.js"></script>
</body>

</html>