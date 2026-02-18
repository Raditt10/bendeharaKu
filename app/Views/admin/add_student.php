<?php
// ==========================================
// 1. CONFIG & SESSION & LOGIC (BACKEND)
// ==========================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// Validasi Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=students';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// 2. PROSES SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data & Sanitasi
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis'] ?? ''));
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));
    
    // Ubah format kontak 08xx jadi 628xx
    if (substr($kontak, 0, 1) === '0') {
        $kontak = '62' . substr($kontak, 1);
    }

    // Validasi Input
    if (empty($nis) || empty($nama)) {
        $error = 'NIS dan Nama Siswa wajib diisi.';
    } else {
        // Cek Duplikasi NIS
        $cekQuery = "SELECT id_siswa FROM siswa WHERE nis = '$nis'";
        if (mysqli_num_rows(mysqli_query($conn, $cekQuery)) > 0) {
            $error = "NIS '$nis' sudah terdaftar! Gunakan NIS lain.";
        } else {
            // Insert Data
            $sql = "INSERT INTO siswa (nis, nama, kontak_orangtua) VALUES ('$nis', '$nama', '$kontak')";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success_msg'] = 'Data siswa berhasil ditambahkan.';
                echo "<script>window.location='?page=students';</script>";
                exit;
            } else {
                $error = 'Terjadi kesalahan sistem: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* GLOBAL RESET */
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 0; }

    /* CARD STYLES */
    .card { 
        background: #fff; 
        border-radius: 20px; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.12); 
        border: 1px solid #e2e8f0; 
        padding: 48px 40px 40px 40px; 
        margin-bottom: 32px; 
    }
    
    .card-header { padding: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 24px; }
    .card-header h2 { margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 800; letter-spacing: -0.02em; }
    .card-header p { margin: 4px 0 0 0; font-size: 0.9rem; color: #64748b; }

    /* FORM ELEMENTS */
    .form-group { margin-bottom: 24px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.95rem; }

    /* Input with Icon */
    .input-icon-wrapper { position: relative; }
    .input-icon {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
        color: #6366f1; pointer-events: none; display: flex; align-items: center;
    }
    
    .form-input {
        width: 100%; padding: 14px 16px;
        border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 1rem; color: #0f172a; background: #fff;
        transition: all 0.2s; font-family: inherit;
    }
    .form-input.with-icon { padding-left: 48px; }
    
    /* Indigo Focus (Sesuai Gambar Referensi) */
    .form-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }

    /* Buttons */
    .form-actions { display: flex; gap: 12px; margin-top: 32px; }
    .btn {
        flex: 1; padding: 14px; border-radius: 12px;
        font-weight: 700; font-size: 1rem; cursor: pointer;
        border: none; text-align: center; text-decoration: none;
        transition: all 0.2s; display: inline-flex; justify-content: center; align-items: center;
    }
    .btn-secondary { background: #f1f5f9; color: #64748b; }
    .btn-secondary:hover { background: #e2e8f0; color: #334155; }
    
    /* Indigo Button (Mirip Gambar) */
    .btn-primary { background: #6366f1; color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25); }
    .btn-primary:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35); }

    /* Alert */
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
        padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 10px; font-size: 0.9rem;
    }
    
    /* Responsive */
    @media (max-width: 480px) {
        .card { padding: 24px; }
        .form-actions { flex-direction: column-reverse; }
    }
</style>
</head>
<body>

<div class="container" style="max-width: 720px; margin: 64px auto 64px auto; padding: 0 20px 80px 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="?page=students" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; transition: color 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Siswa Baru</h2>
                <p>Masukkan data identitas siswa kelas XI RPL 1.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="formStudent">
                
                <div class="form-group">
                    <label class="form-label">NIS (Nomor Induk Siswa)</label>
                    <div class="input-icon-wrapper">
                            <input type="number" name="nis" class="form-input" placeholder="Contoh: 232410..." required autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-icon-wrapper">
                            <input type="text" name="nama" class="form-input" placeholder="Nama Siswa" required autocomplete="off" style="text-transform: capitalize;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">No. HP / WhatsApp Orang Tua</label>
                    <div class="input-icon-wrapper">
                            <input type="number" name="kontak" class="form-input" placeholder="08..." autocomplete="off">
                    </div>
                    <small style="color:#94a3b8; font-size:0.85rem; margin-top:6px; display:block;">*Opsional, boleh dikosongkan.</small>
                </div>

                <div class="form-actions">
                    <a href="?page=students" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Data
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>