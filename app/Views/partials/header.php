<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Helper sederhana untuk cek link aktif
if (!function_exists('isActive')) {
    function isActive($target) {
        $current = isset($_GET['page']) ? $_GET['page'] : 'home';
        return $current === $target ? 'active' : '';
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* =========================================
       1. VARIABLES & RESET
       ========================================= */
    :root {
        --header-height: 70px;
        --primary-color: #2563eb;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --z-overlay: 990;      /* Di belakang sidebar agar tidak menghalangi klik */
        --z-drawer: 1055;
        --z-toggle: 1060;
    }

    body { padding-top: var(--header-height); margin: 0; }

    /* =========================================
       2. HEADER STYLES (DESKTOP BASE)
       ========================================= */
    .site-header {
        background: #ffffff;
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--header-height);
        z-index: 1000;         /* Harus lebih tinggi dari z-overlay */
        border-bottom: 1px solid #e2e8f0;
        transition: box-shadow 0.3s ease;
    }

    .site-header.scrolled {
        box-shadow: 0 4px 20px -5px rgba(0,0,0,0.1);
    }

    .header-inner {
        max-width: 1200px;
        margin: 0 auto;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--text-main);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 1.2rem;
    }

    .brand-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 32px;
    }

    .nav-link {
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.95rem;
        position: relative;
        transition: color 0.2s;
        padding: 5px 0;
    }

    .nav-link:hover { color: var(--primary-color); }
    .nav-link.active { color: var(--text-main); }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 0%; height: 2px;
        background: var(--primary-color);
        transition: width 0.3s;
        border-radius: 2px;
    }
    .nav-link.active::after, .nav-link:hover::after { width: 100%; }

    /* Sembunyikan link profile ini di desktop (karena di desktop pakai dropdown) */
    .mobile-profile-link { display: none !important; }

    .auth-actions { display: flex; align-items: center; gap: 12px; }

    .btn-header {
        padding: 9px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-login { color: var(--text-muted); }
    .btn-login:hover { color: var(--primary-color); background: #eff6ff; }
    .btn-register { background: var(--primary-color); color: #fff; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
    .btn-register:hover { background: #1d4ed8; color: #fff !important; transform: translateY(-1px); box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3); }

    .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; color: var(--text-main); }
    
    /* Overlay background transparan agar tidak sumpek */
    .mobile-overlay { 
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
        background: transparent; z-index: var(--z-overlay); 
        opacity: 0; visibility: hidden; transition: all 0.3s ease; 
    }

    /* Sembunyikan tombol logout khusus mobile jika dibuka di Desktop */
    .mobile-logout-wrapper { display: none; }

    /* =========================================
       3. PROFILE DROPDOWN COMPONENT (DESKTOP)
       ========================================= */
    .profile-dropdown-container {
        position: relative;
        margin-left: 10px;
    }

    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 12px 4px 4px;
        border-radius: 50px;
        transition: all 0.2s;
        border: 1px solid transparent;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .profile-trigger:hover { background: #f8fafc; border-color: #e2e8f0; }

    .profile-avatar {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .profile-info { text-align: left; }
    .profile-name { display: block; font-weight: 700; color: var(--text-main); font-size: 0.95rem; }
    .profile-role-mobile { display: none; } /* Hanya muncul di mobile */
    
    .chevron-icon { color: var(--text-muted); transition: transform 0.3s; }
    .profile-dropdown-container.open .chevron-icon { transform: rotate(180deg); }

    .profile-dropdown-menu {
        position: absolute;
        top: calc(100% + 15px);
        right: 0;
        width: 240px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        overflow: hidden;
    }

    .profile-dropdown-container.open .profile-dropdown-menu {
        opacity: 1; visibility: visible; transform: translateY(0);
    }

    .dropdown-header { padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    .drop-name { display: block; font-weight: 800; color: var(--text-main); font-size: 1rem; }
    .drop-role { display: block; font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

    .dropdown-body { padding: 8px; }
    .dropdown-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; text-decoration: none; color: var(--text-main);
        font-weight: 600; font-size: 0.95rem; border-radius: 10px; transition: all 0.2s;
    }
    .dropdown-item:hover { background: #f1f5f9; color: var(--primary-color); }
    .dropdown-item svg { width: 18px; height: 18px; color: var(--text-muted); transition: color 0.2s; }
    .dropdown-item:hover svg { color: var(--primary-color); }

    .dropdown-divider { height: 1px; background: #e2e8f0; margin: 4px 8px; }
    .text-danger { color: #ef4444 !important; }
    .text-danger svg { color: #ef4444 !important; }
    .text-danger:hover { background: #fef2f2; color: #dc2626 !important; }
    .text-danger:hover svg { color: #dc2626 !important; }

    /* =========================================
       4. MOBILE RESPONSIVE
       ========================================= */
    @media (max-width: 992px) {
        .mobile-toggle { display: flex; align-items: center; justify-content: center; z-index: var(--z-toggle); }
        .mobile-overlay { display: block; }
        .mobile-overlay.active { opacity: 1; visibility: visible; pointer-events: auto; }

        .nav-menu {
            position: fixed !important; top: 0 !important; left: 0 !important; right: auto !important;
            width: 80vw !important; max-width: 340px !important; height: 100vh !important;
            z-index: var(--z-drawer) !important; background: #ffffff;
            flex-direction: column !important; align-items: flex-start !important;
            padding: 0 0 24px 0 !important; gap: 0 !important;
            border-right: 1px solid #e2e8f0 !important; box-shadow: 8px 0 32px -8px rgba(0,0,0,0.08) !important;
            transform: translateX(-100%) !important; transition: transform 0.22s cubic-bezier(0.4,0,0.2,1) !important;
            pointer-events: auto !important;
        }

        .nav-menu.open { transform: translateX(0) !important; pointer-events: auto !important; }

        .nav-link {
            width: 100%; padding: 16px 24px; font-size: 1rem; color: #334155;
            border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box;
        }
        .nav-link::after { display: none; }
        .nav-link::before { content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E"); order: 2; }
        .nav-link.active { background: #f0f9ff; color: var(--primary-color); border-left: 4px solid var(--primary-color); font-weight: 700; }
        .nav-link.active::before { content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E"); }

        /* Munculkan link profile khusus mobile di dalam list navigasi */
        .mobile-profile-link { display: flex !important; }

        /* Khusus Header Menu Mobile untuk GUEST (Belum Login) */
        .mobile-profile-header {
            width: 100%; padding: 70px 24px 24px 24px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 1px solid #e2e8f0; margin-bottom: 10px; box-sizing: border-box;
        }
        .user-greeting { display: block; font-size: 1.2rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
        .user-role { font-size: 0.9rem; color: #64748b; font-weight: 500; }
        
        /* Auth Buttons Guest Mobile */
        .auth-actions { flex-direction: column; width: 100%; padding: 24px; margin-top: auto; box-sizing: border-box; }
        .btn-header { width: 100%; padding: 14px; border-radius: 12px; font-size: 1rem; }
        .btn-login { border: 1px solid #e2e8f0; background: #fff; }

        /* Bagian Atas Sidebar User (Menampilkan Nama & Role statis) */
        .profile-dropdown-container {
            order: -1; /* Paksa pindah ke urutan paling atas di Sidebar */
            width: 100%; margin: 0 0 10px 0;
            padding: 60px 24px 20px 24px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 1px solid #e2e8f0; box-sizing: border-box;
        }
        .profile-trigger {
            width: 100%; padding: 0; border: none; background: transparent !important; justify-content: space-between;
            pointer-events: none; /* Matikan klik pada profil atas di mobile */
        }
        .profile-trigger .profile-avatar { width: 48px; height: 48px; font-size: 1.3rem; }
        .profile-info { flex-grow: 1; margin-left: 14px; }
        .profile-name { font-size: 1.15rem; color: #1e293b; }
        .profile-role-mobile { display: block; font-size: 0.85rem; color: #64748b; font-weight: 500; margin-top: 2px; }
        
        /* Hilangkan chevron panah & dropdown menu bawaan desktop */
        .chevron-icon { display: none !important; }
        .profile-dropdown-menu { display: none !important; }

        /* =========================================
           TOMBOL LOGOUT KHUSUS MOBILE (DI BAWAH)
           ========================================= */
        .mobile-logout-wrapper {
            display: flex;
            width: 100%;
            padding: 24px;
            margin-top: auto; /* Ini yang membuat tombol terdorong ke paling bawah layar */
            box-sizing: border-box;
        }
        .btn-logout-mobile {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout-mobile:hover {
            background: #fee2e2;
            color: #dc2626;
        }
    }
</style>

<div class="mobile-overlay" id="mobileOverlay"></div>

<header class="site-header" id="mainHeader">
    <div class="header-inner">

        <a class="brand" href="?page=home">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
            </div>
            <span>BendeharaKu</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <span class="icon-menu">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </span>
            <span class="icon-close" style="display: none;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </span>
        </button>

        <nav class="nav-menu" id="navMenu">
            <?php if (!isset($_SESSION['nis']) && !isset($_SESSION['user_id'])): ?>
                <div class="mobile-profile-header">
                    <span class="user-greeting">Selamat Datang!</span>
                    <span class="user-role">Silakan masuk untuk akses penuh.</span>
                </div>

                <a href="?page=home" class="nav-link <?= isActive('home') ?>">Beranda</a>
                <a href="#features" class="nav-link">Fitur</a>
                <a href="#about" class="nav-link">Tentang</a>

                <div class="auth-actions">
                    <a href="?page=register" class="btn-header btn-login">Daftar Sekarang</a>
                    <a href="?page=login" class="btn-header btn-register">Masuk</a>
                </div>

            <?php else: ?>
                <a href="?page=dashboard" class="nav-link <?= isActive('dashboard') ?>">Dashboard</a>
                <a href="?page=income" class="nav-link <?= isActive('income') ?>">Pemasukan</a>
                <a href="?page=expenses" class="nav-link <?= isActive('expenses') ?>">Pengeluaran</a>
                <a href="?page=report" class="nav-link <?= isActive('report') ?>">Laporan Kas</a>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="?page=students" class="nav-link <?= isActive('students') ?>">Data Siswa</a>
                <?php endif; ?>

                <a href="?page=profile" class="nav-link mobile-profile-link <?= isActive('profile') ?>">Profil Saya</a>

                <div class="mobile-logout-wrapper">
                    <a href="#" class="btn-logout-mobile" onclick="openWarning('?page=logout', 'logout'); return false;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Keluar Akun
                    </a>
                </div>

                <div class="profile-dropdown-container" id="profileDropdownContainer">
                    <button class="profile-trigger" id="profileTrigger" aria-expanded="false">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="profile-info">
                            <span class="profile-name"><?= htmlspecialchars(explode(' ', $_SESSION['nama'] ?? 'User')[0]) ?></span>
                            <span class="profile-role-mobile"><?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Siswa' ?></span>
                        </div>
                        <svg class="chevron-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    
                    <div class="profile-dropdown-menu" id="profileDropdownMenu">
                        <div class="dropdown-header">
                            <span class="drop-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></span>
                            <span class="drop-role"><?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Siswa' ?></span>
                        </div>
                        <div class="dropdown-body">
                            <a href="?page=profile" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Lihat Profile Saya
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-danger" onclick="openWarning('?page=logout', 'logout'); return false;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Keluar Akun
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('mobileToggle');
        const menu = document.getElementById('navMenu');
        const overlay = document.getElementById('mobileOverlay');
        const iconMenu = toggle?.querySelector('.icon-menu');
        const iconClose = toggle?.querySelector('.icon-close');
        const header = document.getElementById('mainHeader');

        // Logic Mobile Menu
        function toggleMenu(force) {
            const willOpen = force !== undefined ? force : !menu.classList.contains('open');
            if (willOpen) {
                menu.classList.add('open');
                overlay.classList.add('active');
                if (iconMenu) iconMenu.style.display = 'none';
                if (iconClose) iconClose.style.display = 'block';
                document.body.style.overflow = 'hidden'; 
            } else {
                menu.classList.remove('open');
                overlay.classList.remove('active');
                if (iconMenu) iconMenu.style.display = 'block';
                if (iconClose) iconClose.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        if(toggle) toggle.addEventListener('click', (e) => { e.stopPropagation(); toggleMenu(); });
        if(overlay) overlay.addEventListener('click', () => toggleMenu(false));

        // Tutup menu saat link diklik agar proses loading ke halaman baru terlihat rapi
        if (menu) {
            const navLinks = menu.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 992 && menu.classList.contains('open')) {
                        toggleMenu(false);
                    }
                });
            });
        }

        // Logic Profile Dropdown (Hanya aktif di Desktop)
        const profileTrigger = document.getElementById('profileTrigger');
        const profileContainer = document.getElementById('profileDropdownContainer');
        
        if (profileTrigger && profileContainer) {
            profileTrigger.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) return; // Abaikan klik jika di mobile
                e.preventDefault();
                e.stopPropagation();
                const isOpen = profileContainer.classList.contains('open');
                profileContainer.classList.toggle('open');
                profileTrigger.setAttribute('aria-expanded', !isOpen);
            });

            // Tutup dropdown jika klik di luar area profile container
            document.addEventListener('click', function(e) {
                if (!profileContainer.contains(e.target) && profileContainer.classList.contains('open')) {
                    profileContainer.classList.remove('open');
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Handle Resize agar dropdown/menu tidak stuck
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992 && menu?.classList.contains('open')) {
                toggleMenu(false);
            }
        });

        // Efek Scroll Header
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        });
    });
</script>

<?php include_once __DIR__ . '/warning.php'; ?>