<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Helper sederhana untuk cek link aktif
if (!function_exists('isActive')) {
    function isActive($target)
    {
        $current = isset($_GET['page']) ? $_GET['page'] : 'home';
        return $current === $target ? 'active' : '';
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* =========================================
        1. VARIABLES & RESET (PREMIUM)
       ========================================= */
    :root {
        --header-height: 75px;
        --primary-500: #6366f1;
        --primary-600: #4f46e5;
        --primary-glow: rgba(99, 102, 241, 0.4);
        --text-main: #0f172a;
        --text-muted: #64748b;
        --surface-glass: rgba(255, 255, 255, 0.8);
        --surface-glass-mobile: rgba(255, 255, 255, 0.95);
        --border-glass: rgba(255, 255, 255, 0.5);
        --z-overlay: 990;
        --z-drawer: 1055;
        --z-toggle: 1060;
        --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-bounce: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    body {
        padding-top: var(--header-height);
        margin: 0;
    }

    /* =========================================
        2. HEADER STYLES (DESKTOP BASE)
       ========================================= */
    .site-header {
        background: rgba(248, 250, 252, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        z-index: 1000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        transition: var(--transition-smooth);
    }

    .site-header.scrolled {
        background: var(--surface-glass);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        border-bottom-color: rgba(226, 232, 240, 0.5);
    }

    .header-inner {
        max-width: 1200px;
        margin: 0 auto;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--text-main);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 1.3rem;
        letter-spacing: -0.03em;
    }

    .brand-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
        color: #fff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px var(--primary-glow);
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 36px;
    }

    .nav-link {
        text-decoration: none;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.95rem;
        position: relative;
        transition: color 0.2s;
        padding: 8px 0;
    }

    .nav-link:hover {
        color: var(--primary-600);
    }

    .nav-link.active {
        color: var(--primary-600);
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0%;
        height: 3px;
        background: var(--primary-600);
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 3px;
        opacity: 0;
    }

    .nav-link.active::after,
    .nav-link:hover::after {
        width: 20px;
        opacity: 1;
    }

    .mobile-profile-link {
        display: none !important;
    }

    .auth-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .btn-header {
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: var(--transition-bounce);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-login {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .btn-login:hover {
        color: var(--primary-600);
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .btn-register {
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
        color: #fff;
        box-shadow: 0 4px 15px var(--primary-glow);
        border: 1px solid transparent;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px var(--primary-glow);
        color: #fff !important;
    }

    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: var(--text-main);
        transition: var(--transition-smooth);
    }

    .mobile-toggle:hover {
        color: var(--primary-600);
    }

    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: var(--z-overlay);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .mobile-logout-wrapper {
        display: none;
    }

    /* Profile Dropdown Desktop */
    .profile-dropdown-container {
        position: relative;
        margin-left: 12px;
    }

    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(226, 232, 240, 0.8);
        cursor: pointer;
        padding: 4px 16px 4px 4px;
        border-radius: 50px;
        transition: var(--transition-smooth);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .profile-trigger:hover {
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .profile-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.2);
    }

    .profile-info {
        text-align: left;
    }

    .profile-name {
        display: block;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .profile-role-mobile {
        display: none;
    }

    .chevron-icon {
        color: var(--text-muted);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .profile-dropdown-container.open .chevron-icon {
        transform: rotate(180deg);
        color: var(--primary-600);
    }

    .profile-dropdown-menu {
        position: absolute;
        top: calc(100% + 15px);
        right: 0;
        width: 260px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 20px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(226, 232, 240, 0.8);
        border: none;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px) scale(0.95);
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 1000;
        overflow: hidden;
        transform-origin: top right;
    }

    .profile-dropdown-container.open .profile-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .dropdown-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, rgba(238, 242, 255, 0.8), rgba(255, 255, 255, 0.5));
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }

    .drop-name {
        display: block;
        font-weight: 800;
        color: var(--text-main);
        font-size: 1.05rem;
    }

    .drop-role {
        display: block;
        font-size: 0.85rem;
        color: var(--primary-600);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .dropdown-body {
        padding: 12px;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        text-decoration: none;
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 12px;
        transition: var(--transition-smooth);
    }

    .dropdown-item:hover {
        background: rgba(241, 245, 249, 0.8);
        color: var(--primary-600);
        transform: translateX(4px);
    }

    .dropdown-item svg {
        width: 18px;
        height: 18px;
        color: var(--text-muted);
        transition: color 0.2s;
    }

    .dropdown-item:hover svg {
        color: var(--primary-600);
    }

    .dropdown-divider {
        height: 1px;
        background: rgba(226, 232, 240, 0.8);
        margin: 8px 12px;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-danger svg {
        color: #ef4444 !important;
    }

    .text-danger:hover {
        background: rgba(254, 242, 242, 0.8);
        color: #dc2626 !important;
    }

    .text-danger:hover svg {
        color: #dc2626 !important;
    }

    /* =========================================
        4. MOBILE RESPONSIVE
       ========================================= */
    @media (max-width: 992px) {
        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: var(--z-toggle);
        }

        .mobile-overlay {
            display: block;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .nav-menu {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: auto !important;
            width: 85vw !important;
            max-width: 360px !important;
            height: 100vh !important;
            z-index: var(--z-drawer) !important;
            background: var(--surface-glass-mobile);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 0 0 24px 0 !important;
            gap: 0 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.5) !important;
            box-shadow: 10px 0 40px -10px rgba(0, 0, 0, 0.15) !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            pointer-events: auto !important;
        }

        .nav-menu.open {
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }

        .nav-link {
            width: 100%;
            padding: 18px 28px;
            font-size: 1.05rem;
            color: var(--text-main);
            border-bottom: 1px solid rgba(226, 232, 240, 0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            transition: var(--transition-smooth);
        }

        .nav-link::after {
            display: none;
        }

        .nav-link::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E");
            order: 2;
            opacity: 0.5;
            transition: var(--transition-smooth);
        }

        .nav-link:hover {
            background: rgba(248, 250, 252, 0.5);
            padding-left: 32px;
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(238, 242, 255, 0.8), transparent);
            color: var(--primary-600);
            border-left: 4px solid var(--primary-600);
            font-weight: 800;
            padding-left: 24px;
        }

        .nav-link.active::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%234f46e5' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E");
            opacity: 1;
        }

        .mobile-profile-link {
            display: flex !important;
        }

        .mobile-profile-header {
            width: 100%;
            padding: 80px 28px 24px 28px;
            background: linear-gradient(135deg, rgba(238, 242, 255, 0.8), rgba(224, 231, 255, 0.5));
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            margin-bottom: 8px;
            box-sizing: border-box;
            position: relative;
        }

        .mobile-profile-header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.02), transparent);
            pointer-events: none;
        }

        .user-greeting {
            display: block;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .user-role {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .auth-actions {
            flex-direction: column;
            width: 100%;
            padding: 28px;
            margin-top: auto;
            box-sizing: border-box;
            gap: 16px;
        }

        .btn-header {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 1.05rem;
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.8);
            color: var(--text-main);
        }

        .profile-dropdown-container {
            order: -1;
            width: 100%;
            margin: 0;
            padding: 70px 28px 24px 28px;
            background: linear-gradient(135deg, rgba(238, 242, 255, 0.8), rgba(224, 231, 255, 0.5));
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            box-sizing: border-box;
        }

        .profile-trigger {
            width: 100%;
            padding: 0;
            border: none;
            background: transparent !important;
            justify-content: space-between;
            pointer-events: none;
            box-shadow: none !important;
        }

        .profile-trigger .profile-avatar {
            width: 56px;
            height: 56px;
            font-size: 1.5rem;
        }

        .profile-info {
            flex-grow: 1;
            margin-left: 16px;
        }

        .profile-name {
            font-size: 1.25rem;
            color: var(--text-main);
            font-weight: 800;
        }

        .profile-role-mobile {
            display: block;
            font-size: 0.9rem;
            color: var(--primary-600);
            font-weight: 700;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chevron-icon {
            display: none !important;
        }

        .profile-dropdown-menu {
            display: none !important;
        }

        .mobile-logout-wrapper {
            display: flex;
            width: 100%;
            padding: 28px;
            margin-top: auto;
            box-sizing: border-box;
        }

        .btn-logout-mobile {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: rgba(254, 242, 242, 0.8);
            color: #ef4444;
            border: 1px solid rgba(254, 202, 202, 0.5);
            font-weight: 800;
            text-decoration: none;
            transition: var(--transition-bounce);
        }

        .btn-logout-mobile:hover {
            background: rgba(254, 226, 226, 0.9);
            color: #dc2626;
            transform: translateY(-2px);
        }
    }

    @media (min-width: 993px) {
        .mobile-profile-header {
            display: none !important;
        }
    }
</style>

<div class="mobile-overlay" id="mobileOverlay"></div>

<header class="site-header" id="mainHeader">
    <div class="header-inner">

        <a class="brand" href="?page=home">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2" />
                    <path d="M7 15h0M2 9.5h20" />
                </svg>
            </div>
            <span>BendeharaKu</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <span class="icon-menu">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </span>
            <span class="icon-close" style="display: none;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </span>
        </button>

        <nav class="nav-menu" id="navMenu">
            <?php if (!isset($_SESSION['nis']) && !isset($_SESSION['user_id'])): ?>
                <div class="mobile-profile-header">
                    <span class="user-greeting">Selamat Datang!</span>
                    <span class="user-role">Silakan masuk untuk akses penuh.</span>
                </div>

                <a href="?page=home" class="nav-link <?= isActive('home') ?>">Beranda</a>
                <a href="?page=home#features" class="nav-link">Fitur</a>
                <a href="?page=home#terms" class="nav-link">Syarat</a> <a href="?page=home#contact" class="nav-link">Hubungi Kami</a>
                <div class="auth-actions">
                    <a href="?page=register" class="btn-header btn-login">Daftar Sekarang</a>
                    <a href="?page=login" class="btn-header btn-register">Masuk</a>
                </div>

            <?php else: ?>
                <a href="?page=dashboard" class="nav-link <?= isActive('dashboard') ?>">Dashboard</a>
                <a href="?page=income" class="nav-link <?= isActive('income') ?>">Pemasukan</a>
                <a href="?page=expenses" class="nav-link <?= isActive('expenses') ?>">Pengeluaran</a>
                <a href="?page=report" class="nav-link <?= isActive('report') ?>">Laporan Kas</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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

        if (toggle) toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });
        if (overlay) overlay.addEventListener('click', () => toggleMenu(false));

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

        const profileTrigger = document.getElementById('profileTrigger');
        const profileContainer = document.getElementById('profileDropdownContainer');

        if (profileTrigger && profileContainer) {
            profileTrigger.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) return;
                e.preventDefault();
                e.stopPropagation();
                const isOpen = profileContainer.classList.contains('open');
                profileContainer.classList.toggle('open');
                profileTrigger.setAttribute('aria-expanded', !isOpen);
            });

            document.addEventListener('click', function(e) {
                if (!profileContainer.contains(e.target) && profileContainer.classList.contains('open')) {
                    profileContainer.classList.remove('open');
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 992 && menu?.classList.contains('open')) {
                toggleMenu(false);
            }
        });

        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        });
    });
</script>

<?php include_once __DIR__ . '/warning.php'; ?>