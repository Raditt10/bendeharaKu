<?php
// Pastikan session aktif & cek login
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['nis'])) {
    header("Location: ?page=login");
    exit;
}
?>

<style>
    /* =========================================
       PROFILE PAGE STYLES & NOTIFICATION
       ========================================= */
    .profile-wrapper {
        padding: 40px 20px 80px;
        max-width: 800px;
        margin: 0 auto;
        min-height: calc(100vh - 70px);
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes fadeUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 32px;
        text-align: center;
        letter-spacing: -0.02em;
    }

    /* Notifikasi Alert */
    .alert-success {
        background-color: #ecfdf5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 16px 20px;
        margin-bottom: 24px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        animation: slideDownFade 0.5s ease forwards;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-card {
        background: #ffffff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.8);
        position: relative;
    }

    .profile-cover {
        height: 200px;
        background: radial-gradient(circle at top right, #3b82f6, #4f46e5, #312e81);
        position: relative;
        overflow: hidden;
    }

    .profile-cover::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%),
            radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%);
        background-size: 30px 30px;
        background-position: 0 0, 15px 15px;
        opacity: 0.3;
        animation: moveBg 20s linear infinite;
    }

    @keyframes moveBg {
        100% {
            transform: translateY(30px);
        }
    }

    .profile-body {
        padding: 0 40px 48px;
        text-align: center;
        margin-top: -65px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar-large {
        width: 130px;
        height: 130px;
        margin: 0 auto 20px;
        background: #ffffff;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
        position: relative;
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #eef2ff, #c7d2fe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        color: #4f46e5;
        border: 2px solid #e0e7ff;
    }

    .profile-role-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #10b981;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    .profile-name-large {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .profile-sub-role {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        text-align: left;
        margin-bottom: 36px;
    }

    .info-box {
        background: #f8fafc;
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .info-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(15, 23, 42, 0.06);
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .info-icon-wrap {
        width: 48px;
        height: 48px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-icon-wrap.green {
        background: #ecfdf5;
        color: #10b981;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
    }

    .profile-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        padding-top: 32px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-profile {
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-edit {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    }

    .btn-edit:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .btn-logout-main {
        background: #ffffff;
        color: #ef4444;
        border: 1px solid #fecaca;
    }

    .btn-logout-main:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fca5a5;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .profile-wrapper {
            padding: 20px 16px 80px;
        }

        .profile-card {
            border-radius: 20px;
        }

        .profile-cover {
            height: 150px;
        }

        .profile-avatar-large {
            width: 110px;
            height: 110px;
        }

        .avatar-inner {
            font-size: 3rem;
        }

        .profile-body {
            padding: 0 20px 32px;
        }

        .profile-name-large {
            font-size: 1.5rem;
        }

        .profile-info-grid {
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-box {
            padding: 16px;
        }

        .profile-actions {
            flex-direction: column;
            gap: 12px;
            padding-top: 24px;
        }

        .btn-profile {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="profile-wrapper">

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div class="alert-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Informasi profil Anda berhasil diperbarui!
        </div>
        <script>
            // Membersihkan URL agar notif tidak muncul lagi jika user me-refresh halaman
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('status');
            window.history.replaceState({}, document.title, currentUrl.toString());

            // Menghilangkan notif setelah 4 detik
            setTimeout(() => {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 300);
                }
            }, 4000);
        </script>
    <?php endif; ?>

    <h1 class="profile-header-title">Profil Pengguna</h1>

    <div class="profile-card">
        <div class="profile-cover"></div>

        <div class="profile-body">
            <div class="profile-avatar-large">
                <div class="avatar-inner">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="profile-role-badge" title="Verified Member">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
            </div>

            <h2 class="profile-name-large"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna Tidak Diketahui') ?></h2>
            <div class="profile-sub-role">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Siswa' ?> Kelas XI RPL 1
            </div>

            <div class="profile-info-grid">
                <div class="info-box">
                    <div class="info-icon-wrap">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Nomor Induk Siswa</div>
                        <div class="info-value"><?= htmlspecialchars($_SESSION['nis'] ?? 'Belum Terdaftar') ?></div>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-icon-wrap green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Status Akun</div>
                        <div class="info-value" style="color: #059669;">Aktif & Terverifikasi</div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="?page=edit_profile" class="btn-profile btn-edit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Pengaturan Profil
                </a>

                <a href="#" class="btn-profile btn-logout-main" onclick="openWarning('?page=logout', 'logout'); return false;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Keluar Akun
                </a>
            </div>

        </div>
    </div>
</div>