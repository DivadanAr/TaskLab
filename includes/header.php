<?php

$page_title = $page_title ?? 'Dashboard';

$userName  = $_SESSION['user_name']  ?? 'Pengguna';
$userEmail = $_SESSION['user_email'] ?? '';
$userId    = $_SESSION['user_id']    ?? 0;
function getInitials($name)
{
    $name  = trim($name);
    $parts = preg_split('/\s+/', $name);
    $parts = array_filter($parts);

    if (empty($parts)) {
        return '?';
    }

    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 1));
    }

    $first = substr(reset($parts), 0, 1);
    $last  = substr(end($parts), 0, 1);

    return strtoupper($first . $last);
}

$userInitials = getInitials($userName);

?>

<header class="hdr" id="mainHeader" role="banner">

    <div class="hdr-left">
        <i class="fa-solid fa-list-check"></i>
        <h1 class="hdr-page-title" id="pageTitle"><?= $userId ?></h1>
    </div>

    <div class="hdr-right">
        <button class="hdr-icon-btn hdr-notif-btn" title="Notifikasi" aria-label="Notifikasi"
            onclick="App.toast('3 notifikasi baru', 'info')">
            <i class="fa-solid fa-bell"></i>
            <span class="hdr-notif-count" aria-label="3 notifikasi">3</span>
        </button>

        <div class="hdr-divider"></div>

        <div class="profile-wrap" id="profileWrap">

            <button
                class="rail-avatar"
                id="profileTrigger"
                title="Profil — Divadan"
                aria-label="Buka menu profil"
                aria-expanded="false"
                onclick="Profile.toggle()">
                DA
            </button>

            <div class="profile-dropdown" id="profileDropdown">

                <div class="profile-dropdown-header">
                    <div class="rail-avatar profile-dropdown-avatar">DA</div>
                    <div class="profile-dropdown-info">
                        <p class="profile-dropdown-name">Divadan</p>
                        <p class="profile-dropdown-email">divadan@gmail.com</p>
                    </div>
                </div>

                <div class="profile-dropdown-divider"></div>

                <div class="profile-dropdown-menu">

                    <button type="button" class="profile-dropdown-item" onclick="Profile.close(); ProfilePanel.open();">
                        <i class="fa-regular fa-user"></i>
                        <span>Profil Saya</span>
                    </button>

                    <button type="button" class="profile-dropdown-item profile-dropdown-item-danger" onclick="Profile.confirmLogout()">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>

                </div>

            </div>

        </div>

    </div>

</header>

<div class="modal-overlay" id="logoutConfirmModal">

    <div class="modal modal-confirm">

        <div class="confirm-icon-wrap confirm-icon-wrap-neutral">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>

        <h2 class="confirm-title">Keluar dari akun?</h2>

        <p class="confirm-message">Anda perlu login kembali untuk mengakses TaskSync.</p>

        <div class="confirm-actions">
            <button type="button" class="btn-cancel" onclick="Profile.closeLogoutModal()">Batal</button>
            <a href="../actions/logout.php" class="btn-confirm-danger" style="text-decoration:none;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Ya, Logout
            </a>
        </div>

    </div>

</div>

<div class="profile-panel-overlay" id="profilePanelOverlay" onclick="ProfilePanel.close()"></div>

<aside class="profile-panel" id="profilePanel" aria-hidden="true">

    <div class="profile-panel-header">
        <h2>Edit Profil</h2>
        <button class="modal-close" onclick="ProfilePanel.close()">
            <i class="fa-solid fa-x" style="font-size: 15px;"></i>
        </button>
    </div>

    <form id="profileForm" class="profile-panel-form">

        <input type="hidden" name="user_id" value="">

        <div class="profile-panel-avatar-row">
            <div class="rail-avatar profile-panel-avatar" id="profilePanelAvatar"></div>
            <div>
                <p class="profile-panel-avatar-name" id="profilePanelAvatarName"></p>
                <p class="profile-panel-avatar-hint">Inisial otomatis dari nama</p>
            </div>
        </div>

        <div class="form-group">
            <label for="profile_name">Nama Lengkap</label>
            <input type="text" id="profile_name" name="user_name" value="" required>
        </div>

        <div class="form-group">
            <label for="profile_email">Email</label>
            <input type="email" id="profile_email" name="user_email" value="" required>
        </div>

        <div class="profile-panel-divider"></div>

        <p class="profile-panel-section-title">Ubah Password (opsional)</p>

        <div class="form-group">
            <label for="profile_password">Password Baru</label>
            <input type="password" id="profile_password" name="new_password" placeholder="Kosongkan jika tidak ingin mengubah">
        </div>

        <div class="form-group">
            <label for="profile_password_confirm">Konfirmasi Password</label>
            <input type="password" id="profile_password_confirm" name="new_password_confirm" placeholder="Ulangi password baru">
        </div>

        <div id="profileFormMessage" class="profile-panel-message"></div>

        <div class="profile-panel-footer">
            <button type="button" class="btn-cancel" onclick="ProfilePanel.close()">Batal</button>
            <button type="submit" class="btn-save" id="profileSaveBtn">Simpan Perubahan</button>
        </div>

    </form>

</aside>

<script>
    const Profile = {
        toggle() {
            const dropdown = document.getElementById('profileDropdown');
            const trigger = document.getElementById('profileTrigger');
            const isOpen = dropdown.classList.toggle('show');
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        },

        close() {
            document.getElementById('profileDropdown').classList.remove('show');
            document.getElementById('profileTrigger').setAttribute('aria-expanded', 'false');
        },

        confirmLogout() {
            this.close();
            document.getElementById('logoutConfirmModal').classList.add('show');
        },

        closeLogoutModal() {
            document.getElementById('logoutConfirmModal').classList.remove('show');
        }
    };

    const ProfilePanel = {
        open() {
            document.getElementById('profilePanel').classList.add('show');
            document.getElementById('profilePanelOverlay').classList.add('show');
            document.getElementById('profilePanel').setAttribute('aria-hidden', 'false');
            document.getElementById('profile_name').focus();
        },
        close() {
            document.getElementById('profilePanel').classList.remove('show');
            document.getElementById('profilePanelOverlay').classList.remove('show');
            document.getElementById('profilePanel').setAttribute('aria-hidden', 'true');
        }
    };

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('profileWrap');
        if (wrap && !wrap.contains(e.target)) {
            Profile.close();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            Profile.close();
            Profile.closeLogoutModal();
            ProfilePanel.close();
        }
    });

    document.getElementById('logoutConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) Profile.closeLogoutModal();
    });

    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const messageEl = document.getElementById('profileFormMessage');
        const saveBtn = document.getElementById('profileSaveBtn');
        const password = document.getElementById('profile_password').value;
        const confirmPw = document.getElementById('profile_password_confirm').value;

        messageEl.textContent = '';
        messageEl.className = 'profile-panel-message';

        if (password && password !== confirmPw) {
            messageEl.textContent = 'Konfirmasi password tidak cocok.';
            messageEl.classList.add('error');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        const formData = new FormData(this);

        setTimeout(() => ProfilePanel.close(), 900);
    });
</script>