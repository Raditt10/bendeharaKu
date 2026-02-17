<?php
// index.php - Router Utama Bendehara Kelas (Versi Profesional)

// Mulai session lebih awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration (DB credentials, paths)
require_once __DIR__ . '/../config/config.php';

$page = isset($_GET['page']) && is_string($_GET['page']) ? trim($_GET['page']) : 'home';
$safePage = basename($page); 
// Primary view path (try top-level views)
$viewPath = __DIR__ . '/../app/Views/' . $safePage . '.php';

// Fallbacks: try admin subfolder if top-level view missing
$altAdminView = __DIR__ . '/../app/Views/admin/' . $safePage . '.php';
if (!is_file($viewPath) && is_file($altAdminView)) {
    $viewPath = $altAdminView;
}

// Daftar halaman yang ditangani oleh controller saja (tidak perlu file view)
$controllerOnly = ['logout'];

// Cek file view (kecuali halaman controller-only seperti logout)
if ($page !== 'home' && !in_array($page, $controllerOnly, true) && !is_file($viewPath)) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p>";
    exit;
}

// Protect certain pages and redirect to login if user not authenticated
$authRequired = ['dashboard','income','expenses','students','add_income','add_expense','add_student','edit_income','edit_expense','edit_student','report'];
if (in_array($page, $authRequired, true)) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['nis'])) {
        $script = $_SERVER['PHP_SELF'] ?? './';
        header('Location: ' . $script . '?page=login');
        exit;
    }
}

// Simple POST handler for forms that map to controllers (e.g., login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'login') {
        // Route POST /?page=login -> AuthController::login()
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        // AuthController will redirect on success or render login on failure
        exit;
    }

    // Handle admin add_income form here before any output is sent
    if ($page === 'add_income') {
        require_once __DIR__ . '/../app/Models/Database.php';
        $conn = Database::getInstance()->getConnection();

        // basic validation and insertion
        $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan'] ?? ''));
        $jumlah = (float)($_POST['jumlah'] ?? 0);
        $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));

        if ($bulan !== '' && $jumlah > 0) {
            $sql = "INSERT INTO pemasukan (bulan, jumlah, keterangan) VALUES ('{$bulan}', '{$jumlah}', '{$keterangan}')";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success_msg'] = 'Pemasukan berhasil ditambahkan.';
            } else {
                $_SESSION['error_msg'] = 'Gagal menyimpan data.';
            }
        } else {
            $_SESSION['error_msg'] = 'Bulan dan jumlah wajib diisi.';
        }

        header('Location: ' . ($_SERVER['PHP_SELF'] ?? './') . '?page=income');
        exit;
    }

    // Handle admin add_student form before output
    if ($page === 'add_student') {
        require_once __DIR__ . '/../app/Models/Database.php';
        $conn = Database::getInstance()->getConnection();

        $nis = mysqli_real_escape_string($conn, trim($_POST['nis'] ?? ''));
        $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
        $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));

        if ($nis !== '' && $nama !== '') {
            $query = "INSERT INTO siswa (nis, nama, kontak_orangtua) VALUES ('{$nis}', '{$nama}', '{$kontak}')";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success_msg'] = 'Data siswa berhasil ditambahkan!';
            } else {
                $_SESSION['error_msg'] = 'Gagal menambahkan data!';
            }
        } else {
            $_SESSION['error_msg'] = 'NIS dan nama wajib diisi!';
        }

        header('Location: ' . ($_SERVER['PHP_SELF'] ?? './') . '?page=students');
        exit;
    }
}

// GET actions that map to controllers
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($page === 'logout') {
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        // logout() will redirect
        exit;
    }
}

$error_msg = $error_msg ?? '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bendehara Kelas — Manajemen Keuangan Sekolah Profesional</title>
    <meta name="description" content="Sistem manajemen iuran kelas yang transparan, mudah, dan aman untuk guru dan bendahara.">
    <meta name="author" content="Bendehara Kelas">
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📘</text></svg>">
    
    <!-- Font Inter dengan variasi lebih lengkap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    
    <style>
        /* =========================================
           DESIGN SYSTEM & RESET
           ========================================= */
        :root {
            /* Color Palette - Profesional dengan aksen biru */
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --primary-300: #93c5fd;
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --primary-800: #1e40af;
            --primary-900: #1e3a8a;
            
            /* Netral */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Semantic */
            --bg-body: var(--gray-50);
            --text-primary: var(--gray-900);
            --text-secondary: var(--gray-600);
            --text-tertiary: var(--gray-500);
            --border-light: var(--gray-200);
            --card-bg: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            
            /* Spacing system (4px base) */
            --space-1: 0.25rem;  /* 4px */
            --space-2: 0.5rem;   /* 8px */
            --space-3: 0.75rem;  /* 12px */
            --space-4: 1rem;     /* 16px */
            --space-5: 1.25rem;  /* 20px */
            --space-6: 1.5rem;   /* 24px */
            --space-8: 2rem;     /* 32px */
            --space-10: 2.5rem;  /* 40px */
            --space-12: 3rem;    /* 48px */
            --space-16: 4rem;    /* 64px */
            --space-20: 5rem;    /* 80px */
            --space-24: 6rem;    /* 96px */
            
            /* Typography scale (Modular scale 1.25) */
            --text-xs: 0.75rem;   /* 12px */
            --text-sm: 0.875rem;  /* 14px */
            --text-base: 1rem;    /* 16px */
            --text-md: 1.125rem;  /* 18px */
            --text-lg: 1.25rem;   /* 20px */
            --text-xl: 1.5rem;    /* 24px */
            --text-2xl: 1.875rem; /* 30px */
            --text-3xl: 2.25rem;  /* 36px */
            --text-4xl: 3rem;     /* 48px */
            --text-5xl: 3.75rem;  /* 60px */
            --text-6xl: 4.5rem;   /* 72px */
            
            /* Line heights */
            --leading-tight: 1.2;
            --leading-snug: 1.375;
            --leading-normal: 1.5;
            --leading-relaxed: 1.625;
            --leading-loose: 2;
            
            /* Borders */
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-2xl: 1rem;
            
            /* Transitions */
            --transition-base: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-secondary);
            line-height: var(--leading-relaxed);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Heading styles */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-primary);
            font-weight: 700;
            line-height: var(--leading-tight);
            letter-spacing: -0.025em;
        }

        h1 { font-size: var(--text-5xl); }
        h2 { font-size: var(--text-4xl); }
        h3 { font-size: var(--text-3xl); }
        h4 { font-size: var(--text-2xl); }
        h5 { font-size: var(--text-xl); }
        h6 { font-size: var(--text-lg); }

        p {
            margin-bottom: var(--space-4);
        }

        a {
            color: var(--primary-600);
            text-decoration: none;
            transition: var(--transition-base);
        }
        a:hover {
            color: var(--primary-700);
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--space-6);
        }

        /* Header styles moved to app/Views/partials/header.php */

        /* =========================================
           HERO SECTION
           ========================================= */
        .hero {
            padding: var(--space-24) 0 var(--space-16);
            background: radial-gradient(circle at 70% 30%, var(--primary-50), transparent 50%);
        }
        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 0.9fr;
            gap: var(--space-16);
            align-items: center;
        }
        .hero h1 {
            font-size: clamp(var(--text-4xl), 5vw, var(--text-6xl));
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: var(--space-6);
        }
        .hero .highlight {
            background: linear-gradient(135deg, var(--primary-600), var(--primary-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero .lead {
            font-size: var(--text-md);
            color: var(--text-tertiary);
            margin-bottom: var(--space-8);
            max-width: 540px;
        }

        /* =========================================
           BUTTONS
           ========================================= */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: var(--text-sm);
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition-base);
            white-space: nowrap;
        }
        .btn-primary {
            background-color: var(--primary-600);
            color: white;
            box-shadow: var(--shadow-md);
        }
        .btn-primary:hover {
            background-color: var(--primary-700);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .btn-outline {
            background-color: white;
            border-color: var(--border-light);
            color: var(--text-primary);
        }
        .btn-outline:hover {
            border-color: var(--primary-600);
            color: var(--primary-600);
            background-color: var(--primary-50);
        }

        /* Small variant for compact buttons */
        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.875rem;
            border-radius: var(--radius-md);
        }

        /* =========================================
           FEATURES SECTION
           ========================================= */
        .features {
            padding: var(--space-20) 0;
            background: white;
        }
        .section-title {
            text-align: center;
            font-size: var(--text-4xl);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: var(--space-4);
        }
        .section-subtitle {
            text-align: center;
            color: var(--text-tertiary);
            max-width: 600px;
            margin: 0 auto var(--space-12);
            font-size: var(--text-md);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-6);
        }
        .feature-card {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
        }
        .feature-card:hover {
            border-color: var(--primary-200);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .feature-card h3 {
            font-size: var(--text-lg);
            font-weight: 700;
            margin-bottom: var(--space-3);
            color: var(--text-primary);
        }
        .feature-card p {
            color: var(--text-tertiary);
            line-height: var(--leading-relaxed);
            margin: 0;
        }

        /* Auth form styles moved to assets/css/auth.css */

        /* =========================================
           RESPONSIVE
           ========================================= */
        @media (max-width: 768px) {
            :root {
                --text-5xl: 3rem;
                --text-4xl: 2.5rem;
            }
            .hero-inner {
                grid-template-columns: 1fr;
                text-align: center;
                gap: var(--space-8);
            }
            .hero .lead {
                margin-left: auto;
                margin-right: auto;
            }
            .cta {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <?php 
    // INCLUDE HEADER
    $headerPath = __DIR__ . '/../app/Views/partials/header.php';
    if (is_file($headerPath)) {
        include $headerPath;
    } else {
        echo '<header class="site-header"><div class="container header-inner"><a class="brand" href="?page=home">📘 Bendehara Kelas</a></div></header>';
    }
    ?>

    <main>
        <?php if ($page === 'home'): ?>
            <section class="hero">
                <div class="container hero-inner">
                    <div class="hero-copy">
                        <h1>Kelola iuran kelas dengan <span class="highlight">profesional</span></h1>
                        <p class="lead">Sistem transparan untuk guru dan bendahara. Laporan otomatis, riwayat transaksi, dan manajemen siswa dalam satu genggaman.</p>
                        <div class="cta">
                            <a class="btn btn-primary" href="?page=login">Mulai Sekarang</a>
                            <a class="btn btn-outline" href="?page=register">Daftar Guru</a>
                        </div>
                    </div>
                    <div class="hero-media">
                        <!-- Bisa ditambahkan ilustrasi atau pattern nantinya -->
                    </div>
                </div>
            </section>

            <section id="features" class="features">
                <div class="container">
                    <h2 class="section-title">Solusi Keuangan Sekolah</h2>
                    <p class="section-subtitle">Didesain khusus untuk memudahkan pencatatan tanpa ribet.</p>
                    
                    <div class="grid reveal-stagger">
                        <div class="feature-card reveal slide-in-left">
                            <h3>Manajemen Siswa</h3>
                            <p>Pantau status pembayaran kas setiap siswa secara real-time tanpa perlu membuka buku catatan manual yang ribet.</p>
                        </div>
                        <div class="feature-card reveal slide-in-right">
                            <h3>Laporan Otomatis</h3>
                            <p>Generate laporan bulanan untuk diserahkan ke wali kelas atau orang tua siswa hanya dengan satu klik mudah.</p>
                        </div>
                        <div class="feature-card reveal slide-in-left">
                            <h3>Keamanan Data</h3>
                            <p>Data keuangan disimpan dengan aman menggunakan sistem login terenkripsi, menjaga privasi keuangan kelas.</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php else: 
            include $viewPath; 
        endif; ?>
    </main>

    <?php 
    // INCLUDE FOOTER
    $footerPath = __DIR__ . '/../app/Views/partials/footer.php';
    if (is_file($footerPath)) {
        include $footerPath;
    } else {
        // Fallback footer sederhana (menggunakan kelas yang sama dengan partial)
        echo '<footer class="site-footer"><div class="container footer-bottom-inner"><p>&copy; ' . date('Y') . ' Bendehara Kelas</p></div></footer>';
    }
    ?>

    <script>
        function closeNotif(){
            const n = document.getElementById('notif-error'); 
            if(n) n.style.opacity = '0';
            setTimeout(() => { if(n) n.style.display='none'; }, 300);
        }
    </script>
</body>
</html>