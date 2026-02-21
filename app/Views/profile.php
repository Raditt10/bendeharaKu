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
    }

    .profile-header-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main, #0f172a);
        margin-bottom: 24px;
        text-align: center;
    }

    /* Notifikasi Alert */
    .alert-success {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-left: 5px solid #10b981;
        color: #065f46;
        padding: 16px 20px;
        margin-bottom: 24px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
        animation: slideDownFade 0.5s ease forwards;
    }

    @keyframes slideDownFade {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        position: relative;
    }

    .profile-cover {
        height: 180px;
        background: linear-gradient(135deg, #2563eb 0%, #6366f1 100%);
        position: relative;
    }
    .profile-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.6;
    }

    .profile-body {
        padding: 0 32px 32px;
        text-align: center;
        margin-top: -60px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar-large {
        width: 120px;
        height: 120px;
        margin: 0 auto 16px;
        background: #ffffff;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #e0e7ff, #eef2ff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: #4f46e5;
        border: 2px solid #e2e8f0;
    }

    .profile-name-large {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: -0.02em;
    }

    .profile-badge-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4f46e5;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 32px;
        border: 1px solid #c7d2fe;
    }

    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        text-align: left;
        margin-bottom: 32px;
    }

    .info-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }
    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-label svg {
        color: #94a3b8;
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
        padding-top: 24px;
    }

    .btn-profile {
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .btn-edit {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    .btn-edit:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-logout-main {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .btn-logout-main:hover {
        background: #fee2e2;
        color: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    @media (max-width: 768px) {
        .profile-wrapper { padding: 20px 16px 80px; }
        .profile-cover { height: 140px; }
        .profile-avatar-large { width: 100px; height: 100px; margin-top: 0; }
        .avatar-inner { font-size: 2.5rem; }
        .profile-body { margin-top: -50px; padding: 0 20px 24px; }
        .profile-name-large { font-size: 1.4rem; }
        .profile-info-grid { grid-template-columns: 1fr; gap: 16px; }
        .profile-actions { flex-direction: column; }
        .btn-profile { width: 100%; justify-content: center; }
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
            if(alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 300);
            }
        }, 4000);
    </script>
    <?php endif; ?>

    <h1 class="profile-header-title">Profil Saya</h1>

    <div class="profile-card">
        <div class="profile-cover"></div>

        <div class="profile-body">
            <div class="profile-avatar-large">
                <div class="avatar-inner">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                </div>
            </div>

            <h2 class="profile-name-large"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna Tidak Diketahui') ?></h2>
            <div class="profile-badge-role">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Siswa' ?>
            </div>

            <div class="profile-info-grid">
                <div class="info-box">
                    <div class="info-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Nomor Induk Siswa (NIS)
                    </div>
                    <div class="info-value"><?= htmlspecialchars($_SESSION['nis'] ?? 'Tidak ada data NIS') ?></div>
                </div>

                <div class="info-box">
                    <div class="info-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Status Akun
                    </div>
                    <div class="info-value" style="color: #10b981;">Aktif & Terverifikasi</div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="?page=edit_profile" class="btn-profile btn-edit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit Profil
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