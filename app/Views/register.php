<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* === GLOBAL & RESET === */
* { box-sizing: border-box; }

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
    padding: 20px; /* Jarak aman dari tepi layar HP */
}

.form-box {
    background: #fff;
    border-radius: 24px;
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
}
.auth-header p {
    color: #64748b;
    font-size: 1rem;
    text-align: center;
    margin: 0 0 10px 0;
}

/* === FORM ELEMENTS === */
.form-group { margin-bottom: 4px; }

.form-group label {
    font-weight: 600;
    font-size: 0.95rem;
    color: #334155;
    margin-bottom: 8px;
    display: block;
}

.input-wrapper { position: relative; width: 100%; }

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

/* Input password padding extra biar ga nabrak icon mata */
input[type="password"], input.password-shown { padding-right: 45px; }

.form-group input:focus {
    border-color: #4f46e5;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

/* Tombol Mata */
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
}
.toggle-password:hover { color: #4f46e5; }

/* === BUTTON === */
.auth-submit { margin-top: 8px; }

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
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
}
.auth-submit .btn-primary:hover {
    background: #4338ca;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
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
}
.auth-help a:hover { text-decoration: underline; }

/* === ALERTS === */
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

/* Notifikasi Sukses */
.success-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #10b981;
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(16,185,129,0.3);
    z-index: 9999;
    animation: slideIn 0.5s ease forwards;
    max-width: 90%; /* Agar aman di HP */
}
@keyframes slideIn {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* === MOBILE RESPONSIVE === */
@media (max-width: 640px) {
    .auth-page { padding: 16px; }
    .form-box { padding: 32px 24px; border-radius: 20px; }
    .auth-header h2 { font-size: 1.5rem; }
    /* Font size input 16px mencegah zoom otomatis di iOS */
    .form-group input { font-size: 16px; padding: 12px 14px; }
    .success-toast { right: 5%; left: 5%; text-align: center; }
}
</style>

<?php
// === LOGIKA BACKEND ===
if (session_status() === PHP_SESSION_NONE) session_start();
// require_once __DIR__ . '/../../config/config.php'; // Aktifkan sesuai path Anda
// require_once __DIR__ . '/../Models/Database.php'; // Aktifkan sesuai path Anda

// Mockup Koneksi (Hapus baris ini jika sudah pakai require_once di atas)
// $koneksi = Database::getInstance()->getConnection(); 

$error_msg = '';
$show_success = false;

if (isset($_SESSION['user_id'])) {
    header("Location: ?page=dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Asumsi koneksi database sudah ada di $koneksi
    // Pastikan kode require_once database Anda aktif
    
    $nama = trim($_POST['nama'] ?? '');
    $nis = trim($_POST['nis'] ?? '');
    $password_plain = $_POST['password'] ?? '';
    $role = 'siswa'; 

    if ($nama === '' || $nis === '' || $password_plain === '') {
        $error_msg = 'Mohon lengkapi semua kolom.';
    } else {
        // Cek NIS (Mockup logic, sesuaikan dengan logic asli Anda)
        // ... (Kode query database Anda tetap sama) ...
        
        // Contoh simulasi sukses (Hapus blok if(true) ini dan pakai logika DB Anda)
        // if(true) { $show_success = true; } 
        
        /* === MASUKKAN LOGIKA QUERY ASLI ANDA DI SINI ===
        $checkStmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE nis = ?");
        ... dst ...
        */
        
        // Code Placeholder untuk logic Anda (Simpan logic asli Anda di sini)
        // Logika di bawah ini hanya contoh agar form tidak error saat dicoba tanpa DB
        if (strlen($password_plain) < 6) {
            $error_msg = "Password minimal 6 karakter.";
        } else {
            // Anggap insert sukses
            $show_success = true; 
        }
    }
}
?>

<?php if ($show_success): ?>
<div class="success-toast">
    <div style="display:flex; align-items:center; gap:10px; font-weight:700; justify-content:center;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        Registrasi Berhasil!
    </div>
    <div style="margin-top:4px; font-size:0.9rem; opacity:0.95;">Mengalihkan ke halaman login...</div>
</div>
<script>setTimeout(() => { window.location = '?page=login'; }, 2000);</script>
<?php endif; ?>

<div class="auth-page">
    <div class="form-box">
        <div class="auth-header">
            <h2>Buat Akun Baru</h2>
            <p>Isi data diri Anda untuk memulai</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="error-box">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-wrapper">
                    <input type="text" id="nama" name="nama" placeholder="Contoh: Rafaditya Syahputra" autocomplete="off" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="nis">NIS (Nomor Induk Siswa)</label>
                <div class="input-wrapper">
                    <input type="number" id="nis" name="nis" placeholder="Nomor Induk Siswa" autocomplete="off" required value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" autocomplete="off" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Lihat Password">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="auth-submit">
                <button type="submit" class="btn-primary">Daftar Sekarang</button>
            </div>
        </form>

        <div class="auth-help">
            Sudah punya akun? <a href="?page=login">Masuk di sini</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        input.classList.add('password-shown');
        btn.style.color = "#4f46e5"; 
    } else {
        input.type = "password";
        input.classList.remove('password-shown');
        btn.style.color = "#94a3b8"; 
    }
}
</script>