const AuthForm = (() => {
  function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector("i");
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    icon.classList.toggle("fa-eye", !isHidden);
    icon.classList.toggle("fa-eye-slash", isHidden);
  }
 
  function showMessage(el, text, type) {
    el.textContent = text;
    el.className = "auth-message" + (type ? " " + type : "");
  }
 
  function setLoading(btn, loadingText, defaultHTML) {
    btn.disabled = true;
    btn.innerHTML = loadingText;
    return () => {
      btn.disabled = false;
      btn.innerHTML = defaultHTML;
    };
  }
 
  function initLogin() {
    const form = document.getElementById("loginForm");
    if (!form) return;
 
    const msg = document.getElementById("loginFormMessage");
    const submitBtn = document.getElementById("loginSubmitBtn");
    const defaultHTML = submitBtn.innerHTML;
 
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      showMessage(msg, "", "");
 
      const email = form.email.value.trim();
      const password = form.password.value;
 
      if (!email || !password) {
        showMessage(msg, "Email dan password wajib diisi.", "error");
        return;
      }
 
      const restore = setLoading(submitBtn, "Memproses…", defaultHTML);
 
      fetch(form.action, { method: "POST", body: new FormData(form) })
        .then((r) => r.json())
        .then((data) => {
          if (data.success) {
            window.location.href = "../dashboard.php";
          } else {
            showMessage(msg, data.message || "Email atau password salah.", "error");
          }
        })
        .catch(() => {
          showMessage(msg, "Terjadi kesalahan, coba lagi.", "error");
        })
        .finally(restore);
    });
  }

  function initRegister() {
    const form = document.getElementById("registerForm");
    if (!form) return;
    const msg = document.getElementById("registerFormMessage");
    const submitBtn = document.getElementById("registerSubmitBtn");
    const defaultHTML = submitBtn.innerHTML;

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      showMessage(msg, "", "");

      const name = form.user_name.value.trim();
      const email = form.user_email.value.trim();
      const password = form.password.value;
      const confirm = form.password_confirm.value;
      const agree = form.agree.checked;

      if (!name || !email || !password || !confirm) {
        showMessage(msg, "Semua kolom wajib diisi.", "error");
        return;
      }
      if (password.length < 8) {
        showMessage(msg, "Password minimal 8 karakter.", "error");
        return;
      }
      if (password !== confirm) {
        showMessage(msg, "Konfirmasi password tidak cocok.", "error");
        return;
      }
      if (!agree) {
        showMessage(msg, "Kamu perlu menyetujui syarat layanan dulu.", "error");
        return;
      }

      const restore = setLoading(submitBtn, "Membuat akun…", defaultHTML);

      // TODO: ganti dengan fetch ke controller/register.php
      fetch(form.action, { method: "POST", body: new FormData(form) })
        .then((r) => r.json())
        .then((data) => {
          if (data.success) window.location.href = "login.php";
          else showMessage(msg, data.message || "Pendaftaran gagal.", "error");
        })
        .finally(restore);

      setTimeout(restore, 600);
      setTimeout(()=>{location.href = 'dashboard.php'}, 800);
    });
  }

  function init() {
    initLogin();
    initRegister();
  }

  document.addEventListener("DOMContentLoaded", init);

  return { togglePassword };
})();
