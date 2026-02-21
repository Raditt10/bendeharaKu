<?php
// ==========================================
// 1. SESSION & CONFIGURATION
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Models/Database.php';

// Helper: Get Page
$page = isset($_GET['page']) && is_string($_GET['page']) ? trim($_GET['page']) : 'home';
$safePage = basename($page); 

// ==========================================
// 2. ROUTING LOGIC & AUTHENTICATION
// ==========================================
$authRequired = ['dashboard','income','expenses','students','add_income','add_expense','add_student','edit_income','edit_expense','edit_student','report', 'add_dues', 'edit_dues', 'delete_report'];

if (in_array($page, $authRequired, true)) {
    if (!isset($_SESSION['nis'])) {
        $script = $_SERVER['PHP_SELF'] ?? './';
        header('Location: ' . $script . '?page=login');
        exit;
    }
}

// Handler Controller (Logout)
if ($page === 'logout') {
    require_once __DIR__ . '/../app/Controllers/AuthController.php';
    $auth = new AuthController();
    $auth->logout();
    exit;
}

// ==========================================
// 3. BACKEND HANDLERS (TERMASUK GOOGLE LOGIN)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
    
    // --- LOGIKA GOOGLE LOGIN ---
    if (isset($_POST['credential'])) {
        $jwt = $_POST['credential'];
        $tokenParts = explode(".", $jwt);
        
        if (isset($tokenParts[1])) {
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);
            $google_email = $jwtPayload->email ?? null;
            
            if ($google_email) {
                $conn = Database::getInstance()->getConnection();
                
                $stmt = $conn->prepare("SELECT id_user, nama, nis, role FROM users WHERE email = ?");
                $stmt->bind_param('s', $google_email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user) {
                    if ($user['role'] === 'siswa') {
                        $_SESSION['user_id'] = $user['id_user'];
                        $_SESSION['nama']    = $user['nama'];
                        $_SESSION['nis']     = $user['nis'];
                        $_SESSION['role']    = $user['role'];
                        
                        // Nama di-escape dengan htmlspecialchars untuk keamanan di HTML
                        $namaAman = htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8');
                        ?>
                    <!DOCTYPE html>
                    <html lang="id">
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <!-- REDIRECT PALING RELIABLE: meta refresh sebagai fallback utama -->
                        <meta http-equiv="refresh" content="2;url=?page=dashboard">
                        <title>Login Berhasil</title>
                        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body {
                                font-family: 'Plus Jakarta Sans', sans-serif;
                                background: #f8fafc;
                                min-height: 100vh;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                overflow: hidden;
                            }
                            .overlay {
                                position: fixed;
                                inset: 0;
                                background: rgba(15, 23, 42, 0.45);
                                backdrop-filter: blur(6px);
                                -webkit-backdrop-filter: blur(6px);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                animation: fadeIn 0.3s ease forwards;
                            }
                            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

                            .card {
                                background: #ffffff;
                                border-radius: 28px;
                                padding: 52px 48px 44px;
                                width: 420px;
                                max-width: calc(100vw - 40px);
                                text-align: center;
                                position: relative;
                                overflow: hidden;
                                box-shadow:
                                    0 0 0 1px rgba(79, 70, 229, 0.08),
                                    0 24px 64px -12px rgba(79, 70, 229, 0.22),
                                    0 8px 24px -4px rgba(0,0,0,0.08);
                                animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                            }
                            @keyframes slideUp {
                                from { opacity: 0; transform: translateY(40px) scale(0.94); }
                                to   { opacity: 1; transform: translateY(0) scale(1); }
                            }

                            /* Shimmer stripe di atas card */
                            .card::before {
                                content: '';
                                position: absolute;
                                top: 0; left: 0; right: 0;
                                height: 4px;
                                background: linear-gradient(90deg, #6366f1, #4f46e5, #818cf8, #4f46e5);
                                background-size: 200% 100%;
                                animation: shimmer 2s linear infinite;
                            }
                            @keyframes shimmer {
                                0%   { background-position: 200% 0; }
                                100% { background-position: -200% 0; }
                            }

                            /* Icon glow */
                            .glow-ring {
                                position: relative;
                                width: 96px;
                                height: 96px;
                                margin: 0 auto 28px;
                            }
                            .glow-ring::before {
                                content: '';
                                position: absolute;
                                inset: -10px;
                                border-radius: 50%;
                                background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
                                animation: pulse 2s ease-in-out infinite;
                            }
                            @keyframes pulse {
                                0%, 100% { transform: scale(1);    opacity: 1; }
                                50%       { transform: scale(1.12); opacity: 0.7; }
                            }
                            .icon-circle {
                                width: 96px;
                                height: 96px;
                                border-radius: 50%;
                                background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                position: relative;
                                border: 2px solid rgba(99, 102, 241, 0.2);
                                animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
                            }
                            @keyframes popIn {
                                from { transform: scale(0); opacity: 0; }
                                to   { transform: scale(1); opacity: 1; }
                            }
                            .icon-circle svg {
                                animation: drawCheck 0.5s ease 0.5s both;
                            }
                            @keyframes drawCheck {
                                from { opacity: 0; transform: scale(0.5) rotate(-15deg); }
                                to   { opacity: 1; transform: scale(1) rotate(0deg); }
                            }

                            /* Badge */
                            .badge {
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                background: #eef2ff;
                                color: #4f46e5;
                                font-size: 0.72rem;
                                font-weight: 700;
                                letter-spacing: 0.08em;
                                text-transform: uppercase;
                                padding: 5px 12px;
                                border-radius: 50px;
                                margin-bottom: 16px;
                                border: 1px solid #e0e7ff;
                                animation: fadeUp 0.4s ease 0.4s both;
                            }
                            .badge-dot {
                                width: 6px; height: 6px;
                                border-radius: 50%;
                                background: #4f46e5;
                                animation: blink 1.2s ease-in-out infinite;
                            }
                            @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

                            /* Text */
                            .title {
                                font-size: 1.75rem;
                                font-weight: 800;
                                color: #0f172a;
                                letter-spacing: -0.03em;
                                margin-bottom: 8px;
                                animation: fadeUp 0.4s ease 0.5s both;
                            }
                            .subtitle {
                                font-size: 1rem;
                                color: #64748b;
                                font-weight: 500;
                                margin-bottom: 32px;
                                animation: fadeUp 0.4s ease 0.6s both;
                            }
                            .subtitle strong { color: #4f46e5; font-weight: 700; }
                            @keyframes fadeUp {
                                from { opacity: 0; transform: translateY(12px); }
                                to   { opacity: 1; transform: translateY(0); }
                            }

                            /* Progress bar — animasi berjalan 1.8s, redirect di 2s */
                            .progress-wrap {
                                background: #f1f5f9;
                                border-radius: 99px;
                                height: 6px;
                                overflow: hidden;
                                margin-bottom: 14px;
                                animation: fadeUp 0.4s ease 0.7s both;
                            }
                            .progress-bar {
                                height: 100%;
                                width: 0%;
                                border-radius: 99px;
                                background: linear-gradient(90deg, #6366f1, #818cf8);
                                /* Tidak pakai CSS animation — digerakkan JS agar sinkron dengan redirect */
                                transition: width 1.8s cubic-bezier(0.4, 0, 0.2, 1);
                            }
                            .progress-label {
                                font-size: 0.8rem;
                                color: #94a3b8;
                                font-weight: 500;
                                animation: fadeUp 0.4s ease 0.7s both;
                            }

                            /* Confetti */
                            .particles { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
                            .particle {
                                position: absolute;
                                opacity: 0;
                                animation: confetti 1.2s ease forwards;
                            }
                            @keyframes confetti {
                                0%   { opacity: 1; transform: translate(0, 0) rotate(0deg) scale(1); }
                                100% { opacity: 0; transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(0.3); }
                            }
                        </style>
                    </head>
                    <body>

                    <div class="overlay">
                        <div class="card">
                            <div class="particles" id="particles"></div>

                            <div class="glow-ring">
                                <div class="icon-circle">
                                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none"
                                        stroke="#4f46e5" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </div>

                            <div class="badge">
                                <span class="badge-dot"></span>
                                Autentikasi Berhasil
                            </div>

                            <h1 class="title">Selamat Datang! 👋</h1>
                            <p class="subtitle">
                                Halo, <strong><?= $namaAman ?></strong> — kamu berhasil masuk ke<br>
                                Bendahara Kas XI RPL 1.
                            </p>

                            <div class="progress-wrap">
                                <div class="progress-bar" id="progressBar"></div>
                            </div>
                            <p class="progress-label">Mengalihkan ke Dashboard...</p>
                        </div>
                    </div>

                    <script>
                    (function() {
                        // ── Confetti ──
                        var colors = ['#4f46e5','#818cf8','#6366f1','#a5b4fc','#c7d2fe','#34d399','#fbbf24'];
                        var container = document.getElementById('particles');
                        for (var i = 0; i < 28; i++) {
                            var p = document.createElement('div');
                            p.className = 'particle';
                            var angle = Math.random() * 360;
                            var dist  = 80 + Math.random() * 160;
                            var tx = Math.cos(angle * Math.PI / 180) * dist;
                            var ty = Math.sin(angle * Math.PI / 180) * dist - 60;
                            var color = colors[Math.floor(Math.random() * colors.length)];
                            var w = (5 + Math.random() * 7).toFixed(1);
                            var h = (5 + Math.random() * 7).toFixed(1);
                            var br = Math.random() > 0.5 ? '50%' : '2px';
                            var delay = (0.3 + Math.random() * 0.4).toFixed(2);
                            var dur   = (0.8 + Math.random() * 0.6).toFixed(2);
                            p.style.left = '50%';
                            p.style.top  = '50%';
                            p.style.background = color;
                            p.style.setProperty('--tx', tx.toFixed(1) + 'px');
                            p.style.setProperty('--ty', ty.toFixed(1) + 'px');
                            p.style.setProperty('--rot', (Math.random() * 540 - 270).toFixed(1) + 'deg');
                            p.style.animationDelay    = delay + 's';
                            p.style.animationDuration = dur + 's';
                            p.style.width  = w + 'px';
                            p.style.height = h + 'px';
                            p.style.borderRadius = br;
                            container.appendChild(p);
                        }

                        // ── Progress bar: mulai setelah 100ms agar transition terdeteksi browser ──
                        var bar = document.getElementById('progressBar');
                        setTimeout(function() {
                            bar.style.width = '100%';
                        }, 100);

                        // ── Redirect setelah 2100ms (sedikit lebih dari transisi 1.8s + 100ms delay) ──
                        setTimeout(function() {
                            window.location.href = '?page=dashboard';
                        }, 2100);
                    })();
                    </script>

                    </body>
                    </html>
                    <?php
                        exit;

                    } else {
                        $_SESSION['error_msg'] = "Login Google hanya untuk siswa.";
                    }
                } else {
                    $_SESSION['error_msg'] = "Akun Google ($google_email) belum terdaftar di sistem.";
                }
                $stmt->close();
            } else {
                $_SESSION['error_msg'] = "Gagal memverifikasi akun Google.";
            }
        } else {
            $_SESSION['error_msg'] = "Token Google tidak valid.";
        }
        
        // Jika gagal, kembalikan ke halaman login
        header("Location: ?page=login");
        exit;
    }
    // --- AKHIR LOGIKA GOOGLE LOGIN ---

    // Jika bukan Google (Login Manual), lanjutkan ke Controller bawaanmu
    require_once __DIR__ . '/../app/Controllers/AuthController.php';
    $auth = new AuthController();
    $auth->login();
    exit;
}

if ($page === 'income' && isset($_GET['id']) && $_SESSION['role'] === 'admin') {
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("DELETE FROM pemasukan WHERE id_pemasukan = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    header('Location: ?page=income'); exit;
}

// ==========================================
// 4. VIEW RENDERING
// ==========================================
$viewPath = __DIR__ . '/../app/Views/' . $safePage . '.php';
$altAdminView = __DIR__ . '/../app/Views/admin/' . $safePage . '.php';

if (!is_file($viewPath) && is_file($altAdminView)) {
    $viewPath = $altAdminView;
}

$error_msg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['error_msg']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bendeharaku - Kas XI RPL 1</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* =========================================
           DESIGN SYSTEM & RESET
           ========================================= */
        :root {
            --primary-50: #eef2ff; --primary-100: #e0e7ff; --primary-200: #c7d2fe;
            --primary-500: #6366f1; --primary-600: #4f46e5; --primary-700: #4338ca;
            --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb;
            --gray-500: #6b7280; --gray-600: #4b5563; --gray-900: #111827;
            --bg-body: var(--gray-50); --text-primary: var(--gray-900);
            --radius-lg: 0.5rem; --radius-xl: 0.75rem; --radius-2xl: 1rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --transition-base: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 100px; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--gray-600); line-height: 1.6; overflow-x: hidden; }
        h1, h2, h3 { color: var(--text-primary); font-weight: 700; letter-spacing: -0.02em; }
        a { color: var(--primary-600); text-decoration: none; transition: var(--transition-base); }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }

        /* Buttons & Forms */
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 28px; border-radius: 50px; font-weight: 700; font-size: 1rem; cursor: pointer; border: 1px solid transparent; transition: var(--transition-base); text-decoration: none; }
        .btn-primary { background-color: var(--primary-600); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .btn-primary:hover { background-color: var(--primary-700); transform: translateY(-2px); color: white; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4); }
        .btn-outline { background-color: white; border-color: var(--gray-300); color: var(--text-primary); }
        .btn-outline:hover { border-color: var(--primary-600); color: var(--primary-600); background-color: var(--primary-50); }
        
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-lg); font-size: 1rem; margin-bottom: 16px; }
        .form-control:focus { border-color: var(--primary-500); outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        .form-card { background: white; padding: 24px; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); }

        /* --- LANDING PAGE STYLES --- */
        .hero { padding: 100px 0 80px; background: radial-gradient(circle at top right, var(--primary-50), transparent 70%); text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 16px; background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary-600) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: 1.15rem; color: var(--gray-500); max-width: 600px; margin: 0 auto 32px; }

        .section-title { text-align: center; margin-bottom: 40px; }
        .section-title h2 { font-size: 2.25rem; margin-bottom: 10px; }
        .section-title p { color: var(--gray-500); }

        /* Features */
        .features { padding: 60px 0; background: white; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
        .feature-card { padding: 32px; border-radius: var(--radius-2xl); border: 1px solid var(--gray-200); transition: var(--transition-base); background: var(--gray-50); }
        .feature-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--primary-200); background: white; }

        /* Student Cards */
        .about-section { padding: 80px 0; background: var(--gray-50); border-top: 1px solid var(--gray-200); }
        .student-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px; margin-top: 40px; }
        .student-card { background: white; border: 1px solid var(--gray-200); border-radius: 16px; padding: 24px; text-align: center; position: relative; overflow: hidden; transition: all 0.3s ease; }
        .student-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.15); border-color: var(--primary-200); }
        .card-deco { position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--primary-400), var(--primary-600)); }
        .student-avatar { width: 70px; height: 70px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-100), var(--primary-50)); color: var(--primary-700); font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 0 0 2px var(--primary-100); }
        .student-name { font-size: 1.1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 4px; }
        .student-nis { display: inline-block; background: var(--gray-100); color: var(--gray-500); padding: 2px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 12px; }
        .student-role { font-size: 0.85rem; color: var(--primary-600); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; display: block; margin-bottom: 16px;}

        @media (max-width: 768px) {
            .hero { padding: 60px 0; }
            .hero h1 { font-size: 2.5rem; }
        }

        /* --- SCROLL ANIMATION CLASSES --- */
        .reveal { opacity: 0; transition: all 0.8s ease-out; }
        .fade-up { transform: translateY(40px); }
        .fade-left { transform: translateX(-40px); }
        .fade-right { transform: translateX(40px); }
        .reveal.active { opacity: 1; transform: translate(0, 0); }
        
        .delay-100 { transition-delay: 0.1s; }
        .delay-200 { transition-delay: 0.2s; }
        .delay-300 { transition-delay: 0.3s; }
    </style>
</head>
<body>

    <?php 
    // LOAD HEADER
    $headerPath = __DIR__ . '/../app/Views/partials/header.php';
    if (is_file($headerPath)) include $headerPath;
    ?>

    <main>
        <?php if ($page === 'home'): ?>
            <section class="hero">
                <div class="container">
                    <h1 class="reveal fade-up">Kas XI RPL 1</h1>
                    <p class="reveal fade-up delay-100">Platform manajemen keuangan yang transparan, akuntabel, dan profesional untuk kemajuan kelas kita bersama.</p>
                    
                    <?php if (!isset($_SESSION['nis'])): ?>
                        <div class="reveal fade-up delay-200" style="display:flex; gap:12px; justify-content:center;">
                            <a href="?page=login" class="btn btn-primary">
                                Mulai Jelajahi
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:8px"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="reveal fade-up delay-200" style="display:flex; gap:12px; justify-content:center;">
                            <a href="?page=dashboard" class="btn btn-primary">Buka Dashboard</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="features" class="features">
                <div class="container">
                    <div class="section-title reveal fade-up">
                        <h2>Fitur & Keunggulan</h2>
                        <p>Mengapa sistem ini dibuat untuk XI RPL 1?</p>
                    </div>
                    <div class="feature-grid">
                        <div class="feature-card reveal fade-left">
                            <h3 style="color:var(--primary-600); margin-bottom:10px;">📊 Transparansi Penuh</h3>
                            <p>Semua data pemasukan dan pengeluaran dicatat secara digital dan dapat diakses kapan saja.</p>
                        </div>
                        <div class="feature-card reveal fade-up delay-100">
                            <h3 style="color:var(--primary-600); margin-bottom:10px;">👥 Database Terpusat</h3>
                            <p>Data seluruh siswa tersimpan rapi, memudahkan pengecekan status iuran bulanan.</p>
                        </div>
                        <div class="feature-card reveal fade-right delay-200">
                            <h3 style="color:var(--primary-600); margin-bottom:10px;">🔒 Aman & Terpercaya</h3>
                            <p>Sistem login membatasi akses edit hanya untuk pengurus, data aman dari perubahan tak sah.</p>
                        </div>
                    </div>
                </div>
            </section>

            <?php
            $conn = Database::getInstance()->getConnection();
            $querySiswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
            ?>
            <section id="about" class="about-section">
                <div class="container">
                    <div class="section-title reveal fade-up">
                        <h2>Anggota Kami</h2>
                        <p>Developer muda dari XI RPL 1 yang siap berkarya.</p>
                    </div>

                    <div class="student-grid">
                        <?php if ($querySiswa && mysqli_num_rows($querySiswa) > 0): ?>
                            <?php 
                            $delayCounter = 0;
                            while ($row = mysqli_fetch_assoc($querySiswa)): 
                                $initials = '';
                                $parts = explode(' ', $row['nama']);
                                $count = 0;
                                foreach($parts as $part) { 
                                    if($count < 2) { $initials .= strtoupper(substr($part, 0, 1)); $count++; }
                                }
                                $delayClass = 'delay-' . (($delayCounter % 3) * 100);
                                $delayCounter++;
                            ?>
                            <div class="student-card reveal fade-up <?= $delayClass ?>">
                                <div class="card-deco"></div>
                                <div class="student-avatar"><?= $initials ?></div>
                                <h3 class="student-name"><?= htmlspecialchars($row['nama']) ?></h3>
                                <span class="student-nis"><?= htmlspecialchars($row['nis']) ?></span>
                                <span class="student-role">Software Engineer</span>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="reveal fade-up" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--gray-500);">
                                <p>Belum ada data siswa yang ditampilkan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        <?php else: 
            // Render View Halaman Lain
            if (is_file($viewPath)) include $viewPath; 
        endif; ?>
    </main>

    <?php 
    // LOAD FOOTER
    $footerPath = __DIR__ . '/../app/Views/partials/footer.php';
    if (is_file($footerPath)) {
        include $footerPath;
    } else {
        echo '<footer style="padding:40px 0; text-align:center; color:#6b7280; font-size:0.9rem; background:white; border-top:1px solid #eee;"><p>&copy; ' . date('Y') . ' XI RPL 1. All rights reserved.</p></footer>';
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, options);
            const revealElements = document.querySelectorAll('.reveal');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>

</body>
</html>