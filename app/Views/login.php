<meta name="viewport" content="width=device-width, initial-scale=1.0"> <style>
/* === GLOBAL RESET & VARIABLES === */
* {
    box-sizing: border-box; /* Mencegah elemen melebar keluar container */
}

body {
    margin: 0;
    padding: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f8fafc;
    color: #1e293b;
}

/* === LAYOUT UTAMA === */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px; /* Memberi jarak aman dari tepi layar HP */
}

.form-box {
    background: #fff;
    border-radius: 24px; /* Lebih bulat sedikit agar modern */
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
    padding: 40px;
    max-width: 420px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* === HEADER === */
.auth-header h2 {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0 0 8px 0;
    text-align: center;
    color: #0f172a;
    line-height: 1.2;
}
.auth-header p {
    color: #64748b;
    font-size: 1rem;
    text-align: center;
    margin: 0 0 24px 0;
    line-height: 1.5;
}

/* === FORM ELEMENTS === */
.form-group {
    margin-bottom: 4px;
}

.form-group label {
    font-weight: 600;
    font-size: 0.95rem;
    color: #334155;
    margin-bottom: 8px;
    display: block;
}

/* Input Styles */
.input-wrapper {
    position: relative;
    width: 100%;
}

.form-group input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    background: #f8fafc;
    transition: all 0.2s ease;
    outline: none;
    color: #1e293b;
}

/* Khusus input password agar teks tidak tertabrak ikon mata */
input[type="password"], input.password-shown {
    padding-right: 45px; 
}

.form-group input:focus {
    border-color: #4f46e5;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

/* Tombol Mata Password */
.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}
.toggle-password:hover {
    color: #4f46e5;
}

/* === BUTTON === */
.auth-submit {
    margin-top: 8px;
}
.auth-submit .btn-primary {
    width: 100%;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 12px;
    padding: 14px 0;
    background: #4f46e5;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);
}
.auth-submit .btn-primary:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
}
.auth-submit .btn-primary:active {
    transform: translateY(0);
}

/* === FOOTER LINKS === */
.auth-help {
    text-align: center;
    color: #64748b;
    font-size: 0.95rem;
    margin-top: 16px;
}
.auth-help a {
    color: #4f46e5;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
}
.auth-help a:hover {
    color: #3730a3;
    text-decoration: underline;
}

/* === ERROR BOX === */
.error-box {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #ef4444;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

/* === MOBILE RESPONSIVE TWEAKS === */
@media (max-width: 640px) {
    .auth-page {
        padding: 16px; /* Jarak aman di HP */
        align-items: center; /* Center vertikal */
    }
    .form-box {
        padding: 32px 24px; /* Padding dalam jangan terlalu kecil (tadi 8px, sekarang 24px) */
        border-radius: 20px;
    }
    .auth-header h2 {
        font-size: 1.5rem;
    }
    .form-group input {
        font-size: 16px; /* Mencegah auto-zoom di iPhone */
        padding: 12px 14px;
    }
}
</style>

<?php
// === LOGIKA BACKEND (TIDAK BERUBAH) ===
if (session_status() === PHP_SESSION_NONE) session_start();
// require_once __DIR__ . '/../../config/config.php'; // Uncomment jika dipakai
// require_once __DIR__ . '/../Models/Database.php'; // Uncomment jika dipakai

$error_msg = $_GET['err'] ?? ($_SESSION['error_msg'] ?? '');
unset($_SESSION['error_msg']);

if (isset($_SESSION['user_id'])) {
    header("Location: ?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($nis) || empty($password)) {
        $error_msg = "NIS dan Password wajib diisi.";
    } elseif (!preg_match('/^[0-9]+$/', $nis)) {
        $error_msg = "NIS hanya boleh berisi angka.";
    } else {
        // $koneksi = Database::getInstance()->getConnection();
        // $stmt = mysqli_prepare($koneksi, "SELECT id, nama, password, role FROM users WHERE nis = ?");
        // mysqli_stmt_bind_param($stmt, 's', $nis);
        // mysqli_stmt_execute($stmt);
        // $result = mysqli_stmt_get_result($stmt);
        // $user = mysqli_fetch_assoc($result);
        // if ($user && password_verify($password, $user['password'])) {
        //     $_SESSION['user_id'] = $user['id'];
        //     $_SESSION['nama'] = $user['nama'];
        //     $_SESSION['nis'] = $nis;
        //     $_SESSION['role'] = $user['role'];
        //     header("Location: ?page=dashboard");
        //     exit;
        // } else {
        //     $error_msg = "NIS atau Password salah.";
        // }
        // mysqli_stmt_close($stmt);
    }
}
?>

<div class="auth-page">
    <div class="form-box">
        <div class="auth-header">
            <h2>Selamat Datang</h2>
            <p>Masuk untuk mengelola keuangan kelas</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="error-box">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nis">NIS (Nomor Induk Siswa)</label>
                <div class="input-wrapper">
                    <input type="text" id="nis" name="nis" placeholder="Masukkan NIS Anda" pattern="[0-9]+" title="Hanya angka" required value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="password-input" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Lihat Password">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="auth-submit">
                <button type="submit" class="btn-primary">Masuk Dashboard</button>
            </div>
        </form>

        <div class="auth-help">
            Belum punya akun? <a href="?page=register">Daftar Sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('svg');
    
    if (input.type === "password") {
        input.type = "text";
        input.classList.add('password-shown');
        btn.style.color = "#4f46e5"; 
        // Ganti icon jadi 'eye-off' (opsional, disini ganti warna saja)
    } else {
        input.type = "password";
        input.classList.remove('password-shown');
        btn.style.color = "#94a3b8"; 
    }
}
</script>