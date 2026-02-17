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
        --z-overlay: 1040;
        --z-drawer: 1055;      /* menu lebih tinggi dari overlay */
        --z-toggle: 1060;       /* tombol di atas segalanya */
    }

    /* Mencegah konten tertutup header fixed */
    body { padding-top: var(--header-height); margin: 0; }

    /* =========================================
       2. HEADER STYLES (DESKTOP BASE)
       ========================================= */
    .site-header {
        background: #ffffff;
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--header-height);
        z-index: 1000; /* header umum */
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

    /* --- Brand / Logo --- */
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

    /* --- Navigation Menu (Desktop) --- */
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

    /* Garis bawah animasi untuk Desktop */
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

    /* --- Auth Buttons (Desktop) --- */
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

    .btn-register {
        background: var(--primary-color);
        color: #fff;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    .btn-register:hover {
        background: #1d4ed8;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
    }

    /* Toggle Button (Hidden on Desktop) */
    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: var(--text-main);
    }

    /* Overlay (Hidden on Desktop) */
    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: var(--z-overlay);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    /* =========================================
       3. MOBILE RESPONSIVE (APP-LIKE FEEL)
       ========================================= */
    @media (max-width: 992px) {
        /* Tombol hamburger muncul */
        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: var(--z-toggle);
        }

        /* Overlay aktif */
        .mobile-overlay {
            display: block;
        }
        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* Sidebar Drawer */
        .nav-menu {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: auto !important;
            width: 80vw !important;
            max-width: 340px !important;
            height: 100vh !important;
            z-index: var(--z-drawer) !important;   /* lebih tinggi dari overlay */
            background: #ffffff;
            flex-direction: column !important;
            align-items: flex-start !important;
            padding: 0 0 24px 0 !important;
            gap: 0 !important;
            border-right: 1px solid #e2e8f0 !important;
            box-shadow: 8px 0 32px -8px rgba(0,0,0,0.08) !important;
            transform: translateX(-100%) !important;
            transition: transform 0.22s cubic-bezier(0.4,0,0.2,1) !important;
            pointer-events: auto !important; /* pastikan bisa diklik */
        }

        .nav-menu.open {
            transform: translateX(0) !important;
            pointer-events: auto !important;
        }

        /* Profile Header di Menu Mobile */
        .mobile-profile-header {
            width: 100%;
            padding: 70px 24px 24px 24px; /* padding atas agar tidak tertutup header */
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .user-greeting {
            display: block;
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .user-role {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Link Menu Mobile */
        .nav-link {
            width: 100%;
            padding: 16px 24px;
            font-size: 1rem;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
        }
        .nav-link::after { display: none; } /* hilangkan garis bawah desktop */

        /* Icon panah kecil */
        .nav-link::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E");
            order: 2;
        }

        .nav-link.active {
            background: #f0f9ff;
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
            font-weight: 700;
        }
        .nav-link.active::before {
            content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E");
        }

        /* Tombol Auth Mobile */
        .auth-actions {
            flex-direction: column;
            width: 100%;
            padding: 24px;
            margin-top: auto; /* dorong ke bawah */
            box-sizing: border-box;
        }
        .btn-header {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
        }
        .btn-login {
            border: 1px solid #e2e8f0;
            background: #fff;
        }
    }

    /* Utility: tampilkan profile header hanya di mobile */
    @media (min-width: 993px) {
        .mobile-profile-header {
            display: none !important;
        }
    }

    /* === REMOVE MOBILE OVERLAY DARK === */
    @media (max-width: 992px) {
        .mobile-overlay,
        .mobile-overlay.active {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            background: none !important;
        }
    }
</style>

<!-- Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<header class="site-header" id="mainHeader">
    <div class="header-inner">

        <!-- Logo -->
        <a class="brand" href="?page=home">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
            </div>
            <span>BendeharaKu</span>
        </a>

        <!-- Tombol Hamburger (dengan ikon berganti) -->
        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <span class="icon-menu">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </span>
            <span class="icon-close" style="display: none;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </span>
        </button>

        <!-- Navigasi Menu -->
        <nav class="nav-menu" id="navMenu">

            <!-- Profile Header (khusus mobile) -->
            <div class="mobile-profile-header">
                <?php if (isset($_SESSION['nama'])): ?>
                    <span class="user-greeting">Halo, <?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?>! 👋</span>
                    <span class="user-role"><?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Siswa' ?></span>
                <?php else: ?>
                    <span class="user-greeting">Selamat Datang!</span>
                    <span class="user-role">Silakan masuk untuk akses penuh.</span>
                <?php endif; ?>
            </div>

            <?php if (!isset($_SESSION['nis']) && !isset($_SESSION['user_id'])): ?>
                <!-- Menu untuk tamu -->
                <a href="?page=home" class="nav-link <?= isActive('home') ?>">Beranda</a>
                <a href="#features" class="nav-link">Fitur</a>
                <a href="#about" class="nav-link">Tentang</a>

                <div class="auth-actions">
                    <a href="?page=register" class="btn-header btn-login">Daftar Sekarang</a>
                    <a href="?page=login" class="btn-header btn-register">Masuk</a>
                </div>

            <?php else: ?>
                <!-- Menu untuk user yang sudah login -->
                <a href="?page=dashboard" class="nav-link <?= isActive('dashboard') ?>">Dashboard</a>
                <a href="?page=income" class="nav-link <?= isActive('income') ?>">Pemasukan</a>
                <a href="?page=expenses" class="nav-link <?= isActive('expenses') ?>">Pengeluaran</a>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="?page=students" class="nav-link <?= isActive('students') ?>">Data Siswa</a>
                <?php endif; ?>

                <div class="auth-actions">
                    <a href="#" class="btn-header btn-login" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;" onclick="openWarning('?page=logout', 'logout'); return false;">
                        Keluar Akun
                    </a>
                    <?php include_once __DIR__ . '/warning.php'; ?>
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

        // Jika elemen penting tidak ada, hentikan
        if (!toggle || !menu || !overlay) return;

        // Fungsi untuk membuka/menutup menu
        function toggleMenu(force) {
            const willOpen = force !== undefined ? force : !menu.classList.contains('open');

            if (willOpen) {
                // Buka menu
                menu.classList.add('open');
                overlay.classList.add('active');
                if (iconMenu) iconMenu.style.display = 'none';
                if (iconClose) iconClose.style.display = 'block';
                document.body.style.overflow = 'hidden'; // kunci scroll
            } else {
                // Tutup menu
                menu.classList.remove('open');
                overlay.classList.remove('active');
                if (iconMenu) iconMenu.style.display = 'block';
                if (iconClose) iconClose.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Event klik pada tombol hamburger
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMenu();
        });

        // Klik overlay menutup menu
        overlay.addEventListener('click', function() {
            toggleMenu(false);
        });

        // Tutup menu ketika salah satu link diklik (untuk kenyamanan)
        const allLinks = menu.querySelectorAll('a');
        allLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (menu.classList.contains('open')) {
                    toggleMenu(false);
                }
            });
        });

        // Saat layar di-resize ke ukuran desktop, pastikan menu tertutup
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992 && menu.classList.contains('open')) {
                toggleMenu(false);
            }
        });

        // Efek scroll pada header
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    });
</script>