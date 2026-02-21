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
$authRequired = ['dashboard', 'income', 'expenses', 'students', 'add_income', 'add_expense', 'add_student', 'edit_income', 'edit_expense', 'edit_student', 'report', 'add_dues', 'edit_dues', 'delete_report', 'profile', 'edit_profile'];

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
// HALAMAN ANIMASI LOGIN SUKSES
// ==========================================
if ($page === 'login_success') {
    if (!isset($_SESSION['nama'])) {
        header("Location: ?page=login");
        exit;
    }

    $namaAman = htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8');
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="2;url=?page=dashboard">
        <title>Login Berhasil</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

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

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .card {
                background: #ffffff;
                border-radius: 28px;
                padding: 52px 48px 44px;
                width: 420px;
                max-width: calc(100vw - 40px);
                text-align: center;
                position: relative;
                overflow: hidden;
                box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.08), 0 24px 64px -12px rgba(79, 70, 229, 0.22), 0 8px 24px -4px rgba(0, 0, 0, 0.08);
                animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(40px) scale(0.94);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #6366f1, #4f46e5, #818cf8, #4f46e5);
                background-size: 200% 100%;
                animation: shimmer 2s linear infinite;
            }

            @keyframes shimmer {
                0% {
                    background-position: 200% 0;
                }

                100% {
                    background-position: -200% 0;
                }
            }

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
                background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, transparent 70%);
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 1;
                }

                50% {
                    transform: scale(1.12);
                    opacity: 0.7;
                }
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
                from {
                    transform: scale(0);
                    opacity: 0;
                }

                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .icon-circle svg {
                animation: drawCheck 0.5s ease 0.5s both;
            }

            @keyframes drawCheck {
                from {
                    opacity: 0;
                    transform: scale(0.5) rotate(-15deg);
                }

                to {
                    opacity: 1;
                    transform: scale(1) rotate(0deg);
                }
            }

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
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #4f46e5;
                animation: blink 1.2s ease-in-out infinite;
            }

            @keyframes blink {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.3;
                }
            }

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

            .subtitle strong {
                color: #4f46e5;
                font-weight: 700;
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

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
                transition: width 1.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .progress-label {
                font-size: 0.8rem;
                color: #94a3b8;
                font-weight: 500;
                animation: fadeUp 0.4s ease 0.7s both;
            }

            .particles {
                position: absolute;
                inset: 0;
                pointer-events: none;
                overflow: hidden;
            }

            .particle {
                position: absolute;
                opacity: 0;
                animation: confetti 1.2s ease forwards;
            }

            @keyframes confetti {
                0% {
                    opacity: 1;
                    transform: translate(0, 0) rotate(0deg) scale(1);
                }

                100% {
                    opacity: 0;
                    transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(0.3);
                }
            }
        </style>
    </head>

    <body>
        <div class="overlay">
            <div class="card">
                <div class="particles" id="particles"></div>
                <div class="glow-ring">
                    <div class="icon-circle">
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="badge">
                    <span class="badge-dot"></span> Autentikasi Berhasil
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
                var colors = ['#4f46e5', '#818cf8', '#6366f1', '#a5b4fc', '#c7d2fe', '#34d399', '#fbbf24'];
                var container = document.getElementById('particles');
                for (var i = 0; i < 28; i++) {
                    var p = document.createElement('div');
                    p.className = 'particle';
                    var angle = Math.random() * 360;
                    var dist = 80 + Math.random() * 160;
                    var tx = Math.cos(angle * Math.PI / 180) * dist;
                    var ty = Math.sin(angle * Math.PI / 180) * dist - 60;
                    var color = colors[Math.floor(Math.random() * colors.length)];
                    var w = (5 + Math.random() * 7).toFixed(1);
                    var h = (5 + Math.random() * 7).toFixed(1);
                    var br = Math.random() > 0.5 ? '50%' : '2px';
                    var delay = (0.3 + Math.random() * 0.4).toFixed(2);
                    var dur = (0.8 + Math.random() * 0.6).toFixed(2);
                    p.style.left = '50%';
                    p.style.top = '50%';
                    p.style.background = color;
                    p.style.setProperty('--tx', tx.toFixed(1) + 'px');
                    p.style.setProperty('--ty', ty.toFixed(1) + 'px');
                    p.style.setProperty('--rot', (Math.random() * 540 - 270).toFixed(1) + 'deg');
                    p.style.animationDelay = delay + 's';
                    p.style.animationDuration = dur + 's';
                    p.style.width = w + 'px';
                    p.style.height = h + 'px';
                    p.style.borderRadius = br;
                    container.appendChild(p);
                }
                var bar = document.getElementById('progressBar');
                setTimeout(function() {
                    bar.style.width = '100%';
                }, 100);
                setTimeout(function() {
                    window.location.href = '?page=dashboard';
                }, 2100);
            })();
        </script>
    </body>

    </html>
<?php
    exit;
}

// ==========================================
// 3. BACKEND HANDLERS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'edit_profile') {
    if (isset($_POST['simpan_profil'])) {
        $nama_baru  = trim($_POST['nama']);
        $email_baru = trim($_POST['email']);
        $id_user = $_SESSION['user_id'];
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id_user = ?");
        $stmt->bind_param("ssi", $nama_baru, $email_baru, $id_user);
        if ($stmt->execute()) {
            $_SESSION['nama']  = $nama_baru;
            $_SESSION['email'] = $email_baru;
            header("Location: ?page=profile&status=sukses");
            exit;
        } else {
            $_SESSION['error_msg'] = "Gagal memperbarui profil: " . $conn->error;
            header("Location: ?page=edit_profile");
            exit;
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
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
                        header("Location: ?page=login_success");
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
        header("Location: ?page=login");
        exit;
    }
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
    header('Location: ?page=income');
    exit;
}

// ==========================================
// 4. VIEW RENDERING & ERROR HANDLING
// ==========================================
$viewPath = __DIR__ . '/../app/Views/' . $safePage . '.php';
$altAdminView = __DIR__ . '/../app/Views/admin/' . $safePage . '.php';

if (!is_file($viewPath) && is_file($altAdminView)) {
    $viewPath = $altAdminView;
}

// Inisialisasi pesan error
$error_msg = '';

// 1. Tangkap error dari Session (biasanya dari proses POST login)
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}
// 2. Tangkap error dari URL (Sesuai screenshot kamu yang pakai ?err=...)
else if (isset($_GET['err'])) {
    $error_msg = $_GET['err'];
}
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
            PREMIUM DESIGN SYSTEM & RESET
           ========================================= */
        :root {
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-200: #c7d2fe;
            --primary-300: #a5b4fc;
            --primary-400: #818cf8;
            --primary-500: #6366f1;
            --primary-600: #4f46e5;
            --primary-700: #4338ca;
            --primary-glow: rgba(99, 102, 241, 0.4);

            --surface-50: #f8fafc;
            --surface-100: #f1f5f9;
            --surface-200: #e2e8f0;
            --surface-300: #cbd5e1;

            --text-100: #0f172a;
            --text-200: #1e293b;
            --text-300: #334155;
            --text-400: #475569;
            --text-500: #64748b;

            --bg-body: #f8fafc;
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --radius-2xl: 2rem;

            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-glow: 0 0 20px var(--primary-glow);

            --transition-bounce: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-400);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }

        /* --- BACKGROUND MOTIF --- */
        .bg-motif {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: -1;
            background-color: var(--bg-body);
            overflow: hidden;
        }

        /* Dot Grid */
        .bg-motif::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--surface-300) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.4;
            mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 40%, transparent 100%);
        }

        /* Glowing Blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: floatBlob 20s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
        }

        .blob-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: linear-gradient(to right, var(--primary-300), var(--primary-200));
        }

        .blob-2 {
            top: 40%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: linear-gradient(to left, var(--primary-100), #eef2ff);
            animation-delay: -5s;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(50px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-30px, 30px) scale(0.9);
            }

            100% {
                transform: translate(0, 0) scale(1);
            }
        }

        h1,
        h2,
        h3,
        h4 {
            color: var(--text-100);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        a {
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 10;
        }

        /* --- BUTTONS --- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition-bounce);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            color: white;
            box-shadow: 0 10px 20px -5px var(--primary-glow);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 25px -5px var(--primary-glow);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border-color: var(--primary-200);
            color: var(--primary-600);
        }

        .btn-outline:hover {
            background: white;
            border-color: var(--primary-400);
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        /* --- HERO SECTION --- */
        .hero {
            padding: 160px 0 120px;
            text-align: center;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: white;
            border: 1px solid var(--surface-200);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary-600);
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .hero h1 {
            font-size: 4.5rem;
            margin-bottom: 24px;
            background: linear-gradient(to right, var(--text-100), var(--primary-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.05));
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-400);
            max-width: 650px;
            margin: 0 auto 40px;
            font-weight: 500;
        }

        /* --- GLASS COMPONENT SYSTEM --- */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg), inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            transition: var(--transition-bounce);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.75rem;
            margin-bottom: 12px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary-500);
            border-radius: 2px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--text-400);
            max-width: 500px;
            margin: 0 auto;
        }

        /* --- FEATURES GRID --- */
        .features {
            padding: 100px 0;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
        }

        .feature-card {
            padding: 40px 32px;
            text-align: left;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, var(--primary-50), transparent);
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-200);
            box-shadow: var(--shadow-xl);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrapper {
            width: 64px;
            height: 64px;
            background: white;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
            color: var(--primary-600);
            border: 1px solid var(--surface-100);
            position: relative;
            z-index: 2;
        }

        .feature-card h3 {
            font-size: 1.35rem;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .feature-card p {
            font-size: 1.05rem;
            color: var(--text-400);
            position: relative;
            z-index: 2;
        }

        /* --- TEAM SECTION --- */
        .about-section {
            padding: 100px 0;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 32px;
        }

        .student-card {
            padding: 32px 24px;
            text-align: center;
            border-width: 2px;
            /* Thicker border for glass effect */
        }

        .student-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-300);
            box-shadow: 0 20px 40px -10px var(--primary-glow);
        }

        .student-avatar {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-300), var(--primary-600));
            color: white;
            font-size: 1.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
            transition: var(--transition-bounce);
        }

        .student-card:hover .student-avatar {
            transform: scale(1.1) rotate(5deg);
        }

        .student-nis {
            display: inline-block;
            background: var(--surface-100);
            color: var(--text-400);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 12px;
            border: 1px solid var(--surface-200);
        }

        .student-role {
            font-size: 0.9rem;
            color: var(--primary-600);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- TERMS SECTION --- */
        .terms-section {
            padding: 100px 0;
        }

        .terms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .term-card {
            padding: 24px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .term-card:hover {
            transform: scale(1.02);
            border-color: var(--primary-200);
        }

        .term-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--surface-50), white);
            border: 1px solid var(--surface-200);
            border-radius: var(--radius-md);
            color: var(--primary-600);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }

        /* --- CONTACT SECTION --- */
        .contact-section {
            padding: 100px 0 140px;
        }

        .contact-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 40px;
            text-align: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.6));
        }

        .contact-wrapper h2 {
            font-size: 2.5rem;
            margin-bottom: 16px;
        }

        .contact-wrapper .btn-whatsapp {
            background: #25D366;
            color: white;
            padding: 18px 40px;
            font-size: 1.15rem;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
            margin: 32px 0;
        }

        .contact-wrapper .btn-whatsapp:hover {
            background: #20C35B;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(37, 211, 102, 0.5);
        }

        /* --- ANIMATIONS --- */
        .reveal {
            opacity: 0;
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .fade-up {
            transform: translateY(50px);
        }

        .fade-in {
            transform: scale(0.95);
        }

        .reveal.active {
            opacity: 1;
            transform: translate(0, 0) scale(1);
        }

        .delay-100 {
            transition-delay: 0.1s;
        }

        .delay-200 {
            transition-delay: 0.2s;
        }

        .delay-300 {
            transition-delay: 0.3s;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title h2 {
                font-size: 2.25rem;
            }

            .contact-wrapper {
                padding: 40px 24px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .hero .btn {
                width: auto;
            }

            /* Keep hero buttons inline if possible */
        }
    </style>
</head>

<body>

    <div class="bg-motif">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <?php
    $headerPath = __DIR__ . '/../app/Views/partials/header.php';
    if (is_file($headerPath)) include $headerPath;
    ?>

    <main>
        <?php if ($page === 'home'): ?>
            <section class="hero">
                <div class="container">
                    <div class="reveal fade-up">
                        <div class="hero-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            Sistem Manajemen Modern
                        </div>
                    </div>
                    <h1 class="reveal fade-up delay-100">Kas XI RPL 1</h1>
                    <p class="reveal fade-up delay-200">Platform manajemen keuangan kelas yang dirancang dengan antarmuka elegan, transparan, dan akuntabel untuk kemudahan pengurus dan siswa.</p>
                    <div class="reveal fade-up delay-300" style="display:flex; gap:16px; justify-content:center; flex-wrap: wrap;">
                        <?php if (!isset($_SESSION['nis'])): ?>
                            <a href="?page=login" class="btn btn-primary">
                                Mulai Jelajahi
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                            <a href="#features" class="btn btn-outline">Fitur Unggulan</a>
                        <?php else: ?>
                            <a href="?page=dashboard" class="btn btn-primary">Buka Dashboard</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section id="features" class="features">
                <div class="container">
                    <div class="section-title reveal fade-up">
                        <h2>Fitur & Keunggulan</h2>
                        <p>Sistem ini dirancang khusus untuk memenuhi kebutuhan administrasi kelas yang modern.</p>
                    </div>
                    <div class="feature-grid">
                        <div class="feature-card glass-card reveal fade-up">
                            <div class="feature-icon-wrapper">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                                    <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                                </svg>
                            </div>
                            <h3>Transparansi Penuh</h3>
                            <p>Semua data visualisasi grafik, pemasukan, dan pengeluaran disusun dengan rapi secara digital dan otomatis.</p>
                        </div>
                        <div class="feature-card glass-card reveal fade-up delay-100">
                            <div class="feature-icon-wrapper">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3>Database Terpusat</h3>
                            <p>Seluruh daftar siswa, profil rekening, dan laporan iuran bulanan langsung terarsip dalam satu tempat yang aman.</p>
                        </div>
                        <div class="feature-card glass-card reveal fade-up delay-200">
                            <div class="feature-icon-wrapper">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <h3>Akses Aman</h3>
                            <p>Pembatasan hak akses via Login khusus, memastikan kerahasiaan data dan mencegah modifikasi tanpa izin.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="about" class="about-section">
                <div class="container">
                    <div class="section-title reveal fade-up">
                        <h2>Anggota Pengurus</h2>
                        <p>Dibalik layar kekuatan kas kelas XI RPL 1.</p>
                    </div>
                    <div class="student-grid">
                        <?php
                        $conn = Database::getInstance()->getConnection();
                        $querySiswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
                        if ($querySiswa && mysqli_num_rows($querySiswa) > 0):
                            $delayCounter = 0;
                            while ($row = mysqli_fetch_assoc($querySiswa)):
                                $initials = '';
                                $parts = explode(' ', $row['nama']);
                                for ($i = 0; $i < min(2, count($parts)); $i++) {
                                    $initials .= strtoupper(substr($parts[$i], 0, 1));
                                }
                                $delayClass = 'delay-' . (($delayCounter % 3) * 100);
                                $delayCounter++;
                        ?>
                                <div class="student-card glass-card reveal fade-in <?= $delayClass ?>">
                                    <div class="student-avatar"><?= $initials ?></div>
                                    <h3 class="student-name"><?= htmlspecialchars($row['nama']) ?></h3>
                                    <span class="student-nis">NIS: <?= htmlspecialchars($row['nis']) ?></span>
                                    <br>
                                    <span class="student-role">Software Engineer</span>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">Belum ada data siswa.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section id="terms" class="terms-section">
                <div class="container">
                    <div class="section-title reveal fade-up">
                        <h2>Syarat & Ketentuan</h2>
                        <p>Komitmen bersama untuk kelancaran kas kelas.</p>
                    </div>
                    <div class="terms-grid">
                        <div class="term-card glass-card reveal fade-up">
                            <div class="term-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="term-content">
                                <h3>Bayar Tepat Waktu</h3>
                                <p>Setiap siswa diwajibkan melunaskan beban kas bulanannya sebelum tenggat waktu tiba.</p>
                            </div>
                        </div>
                        <div class="term-card glass-card reveal fade-up delay-100">
                            <div class="term-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="term-content">
                                <h3>Bebas Cetak Laporan</h3>
                                <p>Silakan bebas eksport atau melihat laporan mutasi sebagai jaminan transparansi pengurus.</p>
                            </div>
                        </div>
                        <div class="term-card glass-card reveal fade-up delay-200">
                            <div class="term-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <div class="term-content">
                                <h3>Dana Kolektif</h3>
                                <p>Sisa kas yang terakumulasi sepenuhnya tidak dapat direfund secara individual.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="contact-section">
                <div class="container">
                    <div class="contact-wrapper glass-card reveal fade-up">
                        <h2>Perlu Bantuan?</h2>
                        <p style="font-size: 1.15rem; color: var(--text-500); margin-bottom: 20px;">Dapatkan dukungan komprehensif terkait kesulitan login atau kendala sistem.</p>

                        <a href="https://wa.me/6289661916855?text=Halo%20pengurus%20kas%20XI%20RPL%201%2C%20saya%20butuh%20bantuan%20terkait%20kas%20kelas." target="_blank" class="btn btn-whatsapp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            Hubungi Administrator
                        </a>

                        <p style="font-size: 0.95rem; color: var(--text-400);">
                            Atau via email di: <a href="mailto:bendahara.rpl1@gmail.com" style="color: var(--primary-600); font-weight: 700;">bendahara.rpl1@gmail.com</a>
                        </p>
                    </div>
                </div>
            </section>

        <?php else:
            if (is_file($viewPath)) include $viewPath;
        endif; ?>
    </main>

    <?php
    // LOAD FOOTER TERPISAH DARI FOLDER PARTIALS
    $footerPath = __DIR__ . '/../app/Views/partials/footer.php';
    if (is_file($footerPath)) include $footerPath;
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px"
            };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
</body>

</html>