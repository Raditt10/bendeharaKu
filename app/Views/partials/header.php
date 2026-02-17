<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Helper untuk cek link aktif
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
       HEADER STYLES
       ========================================= */
    .site-header {
        background: #fff;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.03);
        transition: box-shadow 0.3s ease, background 0.3s;
    }

    .site-header.scrolled {
        box-shadow: 0 4px 16px -2px rgba(0,0,0,0.06);
        background: #fff;
    }

    .header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px;
        position: relative;
    }

    /* --- Brand --- */
    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--text-main, #0f172a);
        text-decoration: none;
        letter-spacing: -0.02em;
        padding: 6px 8px;
        border-radius: 8px;
    }
    
    .brand-icon {
        width: 32px;
        height: 32px;
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand span {
        background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }

    /* --- Navigation (Desktop) --- */
    .nav-menu {
        display: flex;
        align-items: center;
        gap: 28px;
    }

    .nav-link {
        color: var(--text-muted, #64748b);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 10px 12px;
        position: relative;
        transition: all 0.18s ease;
        border-radius: 8px;
    }

    .nav-link:hover {
        color: var(--primary, #2563eb);
        background: rgba(37,99,235,0.04);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(2,6,23,0.03);
    }

    .nav-link.active {
        color: var(--text-main, #0f172a);
        font-weight: 600;
    }

    /* Indikator titik kecil untuk link aktif */
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        background-color: var(--primary, #2563eb);
        border-radius: 50%;
    }

    /* --- Auth Buttons --- */
    .auth-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Ensure header buttons keep readable text on hover */
    .auth-actions .btn-primary {
        color: #fff; /* enforce white text */
        padding: 8px 12px;
        border-radius: 10px;
        font-weight: 700;
    }
    .auth-actions .btn-primary:hover {
        color: #fff; /* keep white when hovered */
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(59,130,246,0.18);
    }
    .auth-actions .btn-sm {
        padding: 0.375rem 0.85rem;
    }

    /* --- Mobile Toggle (Hamburger) --- */
    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: var(--text-main, #0f172a);
    }

    /* =========================================
       RESPONSIVE (MOBILE)
       ========================================= */
    @media (max-width: 768px) {
        .mobile-toggle {
            display: block;
        }

        .nav-menu {
            position: absolute;
            top: 100%; /* Tepat di bawah header */
            left: 0;
            width: 100%;
            background: white;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            gap: 16px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            
            /* Animasi Slide Down */
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        /* Kelas .open ditambahkan via JS */
        .nav-menu.open {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .nav-link {
            width: 100%;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .nav-link.active::after { display: none; } /* Hapus dot di mobile */
        .nav-link.active { border-bottom-color: var(--primary); color: var(--primary); }

        .auth-actions {
            flex-direction: column;
            width: 100%;
            gap: 10px;
            margin-top: 10px;
        }
        
        .auth-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<header class="site-header" id="mainHeader">
    <div class="container header-inner">
        
        <a class="brand" href="?page=home">
            <div class="brand-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
            </div>
            <span>BendeharaKu</span>
        </a>

        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>

        <nav class="nav-menu" id="navMenu">
            <?php if (!isset($_SESSION['nis']) && !isset($_SESSION['user_id'])): ?>
                <a href="?page=home" class="nav-link <?= isActive('home') ?>">Beranda</a>
                <a href="#features" class="nav-link">Fitur</a>
                
                <div class="auth-actions">
                    <a href="?page=login" class="btn btn-sm btn-outline">Masuk</a>
                    <a href="?page=register" class="btn btn-sm btn-primary">Daftar</a>
                </div>

            <?php else: ?>
                <a href="?page=dashboard" class="nav-link <?= isActive('dashboard') ?>">Dashboard</a>
                <a href="?page=income" class="nav-link <?= isActive('income') ?>">Pemasukan</a>
                <a href="?page=expenses" class="nav-link <?= isActive('expenses') ?>">Pengeluaran</a>
                <a href="?page=students" class="nav-link <?= isActive('students') ?>">Data Siswa</a>
                
                <div class="auth-actions">
                    <a href="?page=logout" class="btn btn-sm btn-outline" style="border-color: #fee2e2; color: #ef4444;" onclick="return confirm('Yakin ingin keluar?');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Keluar
                    </a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<script>
    // Script Khusus Header
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('mobileToggle');
        const menu = document.getElementById('navMenu');
        const header = document.getElementById('mainHeader');

        // Toggle Mobile Menu
        toggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Mencegah event bubbling
            menu.classList.toggle('open');
            
            // Ubah icon hamburger (opsional)
            const isOpen = menu.classList.contains('open');
            toggle.innerHTML = isOpen 
                ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' // Icon X
                : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>'; // Icon Hamburger
        });

        // Close menu ketika klik di luar
        document.addEventListener('click', (e) => {
            if (menu.classList.contains('open') && !menu.contains(e.target) && !toggle.contains(e.target)) {
                menu.classList.remove('open');
                // Reset icon ke hamburger
                toggle.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
            }
        });

        // Efek shadow saat scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    });
</script>