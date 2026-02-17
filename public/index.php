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
    
    // --- NOTE: Logic POST untuk add_student, add_income, dll sebaiknya ada di dalam file view masing-masing
    // atau controller terpisah. Di sini kita biarkan agar file view yang menangani logic-nya sendiri (self-processing form).
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

// Handler hapus pemasukan (GET) - DELETE ACTION
if ($page === 'income' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    require_once __DIR__ . '/../app/Models/Database.php';
    $conn = Database::getInstance()->getConnection();
    if ($_SESSION['role'] !== 'admin') { header('Location: ?page=income'); exit; }
    
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM pemasukan WHERE id_pemasukan = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = 'Data pemasukan berhasil dihapus.';
    } else {
        $_SESSION['error_msg'] = 'Gagal menghapus data pemasukan.';
    }
    header('Location: ?page=income');
    exit;
}

// Handler hapus pengeluaran (GET)
if ($page === 'expenses' && isset($_GET['id_pengeluaran']) && is_numeric($_GET['id_pengeluaran'])) {
    require_once __DIR__ . '/../app/Models/Database.php';
    $conn = Database::getInstance()->getConnection();
    if ($_SESSION['role'] !== 'admin') { header('Location: ?page=expenses'); exit; }

    $id = (int)$_GET['id_pengeluaran'];
    
    // Hapus foto dulu
    $qFoto = $conn->prepare("SELECT bukti_foto FROM pengeluaran WHERE id_pengeluaran = ?");
    $qFoto->bind_param("i", $id);
    $qFoto->execute();
    $resFoto = $qFoto->get_result()->fetch_assoc();
    
    if($resFoto && $resFoto['bukti_foto']) {
        $path = __DIR__ . '/uploads/' . $resFoto['bukti_foto'];
        if(file_exists($path)) unlink($path);
    }

    $stmt = $conn->prepare("DELETE FROM pengeluaran WHERE id_pengeluaran = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = 'Data pengeluaran berhasil dihapus.';
    } else {
        $_SESSION['error_msg'] = 'Gagal menghapus data pengeluaran.';
    }
    header('Location: ?page=expenses');
    exit;
}

// Handler hapus siswa (GET) - Route: ?page=delete_student&id=...
if ($page === 'delete_student' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    require_once __DIR__ . '/../app/Models/Database.php';
    $conn = Database::getInstance()->getConnection();
    if ($_SESSION['role'] !== 'admin') { header('Location: ?page=students'); exit; }

    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM siswa WHERE id_siswa = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = 'Data siswa berhasil dihapus.';
    } else {
        $_SESSION['error_msg'] = 'Gagal menghapus data siswa.';
    }
    header('Location: ?page=students');
    exit;
}

$error_msg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['error_msg']);
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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    
    <style>
        /* =========================================
           DESIGN SYSTEM & RESET
           ========================================= */
        :root {
            /* Indigo Palette (Primary) */
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-200: #c7d2fe;
            --primary-300: #a5b4fc;
            --primary-400: #818cf8;
            --primary-500: #6366f1;
            --primary-600: #4f46e5;
            --primary-700: #4338ca;
            --primary-800: #3730a3;
            --primary-900: #312e81;
            
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
            
            /* Spacing */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            
            /* Typography */
            --text-xs: 0.75rem;
            --text-sm: 0.875rem;
            --text-base: 1rem;
            --text-lg: 1.125rem;
            --text-xl: 1.25rem;
            --text-2xl: 1.5rem;
            --text-3xl: 1.875rem;
            --text-4xl: 2.25rem;
            
            /* Borders */
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-2xl: 1rem;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            
            /* Transitions */
            --transition-base: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-secondary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Heading styles */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-primary);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        a {
            color: var(--primary-600);
            text-decoration: none;
            transition: var(--transition-base);
        }
        a:hover { color: var(--primary-700); }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--space-4);
        }

        /* Buttons (Global) */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: var(--text-sm);
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition-base);
            white-space: nowrap;
        }
        .btn-primary {
            background-color: var(--primary-600);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:hover {
            background-color: var(--primary-700);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(79, 70, 229, 0.4);
        }
        .btn-outline {
            background-color: white;
            border-color: var(--gray-300);
            color: var(--text-primary);
        }
        .btn-outline:hover {
            border-color: var(--primary-600);
            color: var(--primary-600);
            background-color: var(--primary-50);
        }

        /* Form Card (Shared) */
        .form-card {
            background: white;
            padding: 24px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
        }
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-primary);
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: var(--transition-base);
            margin-bottom: 16px;
        }
        .form-control:focus {
            border-color: var(--primary-500);
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Hero Section (Home) */
        .hero {
            padding: 80px 0;
            background: radial-gradient(circle at top right, var(--primary-50), transparent 70%);
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 1.125rem;
            color: var(--text-tertiary);
            max-width: 600px;
            margin: 0 auto 32px;
        }

        /* Features Section */
        .features { padding: 60px 0; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }
        .feature-card {
            background: white;
            padding: 32px;
            border-radius: var(--radius-2xl);
            border: 1px solid var(--border-light);
            transition: var(--transition-base);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-200);
        }
        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 12px;
            color: var(--primary-700);
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .hero { padding: 60px 0; }
            .hero h1 { font-size: 2.25rem; }
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
        echo '<header class="site-header" style="background:#fff;padding:15px;border-bottom:1px solid #eee;"><div class="container"><a href="?page=home" style="font-weight:800;font-size:1.2rem;color:#4f46e5;">📘 Bendehara Kelas</a></div></header>';
    }
    ?>

    <main>
        <?php if ($page === 'home'): ?>
            <section class="hero">
                <div class="container">
                    <h1>Kelola Keuangan Kelas<br>Lebih Profesional</h1>
                    <p>Sistem manajemen keuangan kelas yang transparan, mudah, dan aman. Pantau kas masuk dan keluar dalam satu Aplikasi.</p>
                    <div style="display:flex; gap:12px; justify-content:center;">
                        <a href="?page=login" class="btn btn-primary">Mulai Sekarang</a>
                        <a href="#features" class="btn btn-outline">Pelajari Fitur</a>
                    </div>
                </div>
            </section>

            <section id="features" class="features">
                <div class="container">
                    <div style="text-align:center;">
                        <h2 style="font-size:2rem; margin-bottom:10px;">Fitur Unggulan</h2>
                        <p style="color:var(--text-tertiary);">Solusi lengkap untuk bendahara masa kini.</p>
                    </div>
                    
                    <div class="grid">
                        <div class="feature-card">
                            <h3>📊 Dashboard Interaktif</h3>
                            <p>Pantau total saldo, pemasukan, dan pengeluaran secara real-time dengan visualisasi data yang mudah dipahami.</p>
                        </div>
                        <div class="feature-card">
                            <h3>👥 Manajemen Siswa</h3>
                            <p>Kelola data siswa, status pembayaran, dan riwayat iuran tanpa perlu mencatat di buku besar manual.</p>
                        </div>
                        <div class="feature-card">
                            <h3>📱 Mobile Friendly</h3>
                            <p>Akses sistem dari mana saja menggunakan smartphone, tablet, atau laptop dengan tampilan yang responsif.</p>
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
        echo '<footer style="padding:40px 0; text-align:center; color:#6b7280; font-size:0.9rem; border-top:1px solid #eee; margin-top:60px;"><p>&copy; ' . date('Y') . ' Bendehara Kelas. All rights reserved.</p></footer>';
    }
    ?>

    <script>
        // Auto-close alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 300);
            });
        }, 4000);
    </script>
</body>
</html>