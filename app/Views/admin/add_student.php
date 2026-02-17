<?php
// --- LOGIKA PHP (BACKEND) ---
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// 1. Cek Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=students';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// 2. Proses Simpan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data & Sanitasi
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis'] ?? ''));
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));
    
    // Ubah kontak 08xx jadi 628xx (Opsional, biar rapi di database)
    if (substr($kontak, 0, 1) === '0') {
        $kontak = '62' . substr($kontak, 1);
    }

    // Validasi Input
    if (empty($nis) || empty($nama)) {
        $error = 'NIS dan Nama Siswa wajib diisi.';
    } else {
        // Cek apakah NIS sudah ada?
        $cekQuery = "SELECT id_siswa FROM siswa WHERE nis = '$nis'";
        $cekResult = mysqli_query($conn, $cekQuery);
        
        if (mysqli_num_rows($cekResult) > 0) {
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

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container" style="max-width: 600px; margin: 0 auto; padding-bottom: 80px;">
    
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
                    <label class="form-label">NIS / NISN</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </span>
                        <input type="number" name="nis" class="form-input with-icon" placeholder="Contoh: 232410..." required autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </span>
                        <input type="text" name="nama" class="form-input with-icon" placeholder="Nama Siswa" required autocomplete="off" style="text-transform: capitalize;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">No. HP / WhatsApp Orang Tua</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </span>
                        <input type="number" name="kontak" class="form-input with-icon" placeholder="08..." autocomplete="off">
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

<style>
    /* GLOBAL RESET */
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }

    /* CARD STYLES */
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
    .card-header { padding: 24px; border-bottom: 1px solid #f1f5f9; background: #fff; }
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
    .form-input.with-icon { padding-left: 48px; } /* Space for icon */
    
    /* Indigo Focus for Student */
    .form-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }

    /* Buttons */
    .form-actions { display: flex; gap: 12px; margin-top: 32px; }
    .btn {
        flex: 1; padding: 14px; border-radius: 12px;
        font-weight: 700; font-size: 1rem; cursor: pointer;
        border: none; text-align: center; text-decoration: none;
        transition: all 0.2s;
    }
    .btn-secondary { background: #f1f5f9; color: #64748b; }
    .btn-secondary:hover { background: #e2e8f0; color: #334155; }
    
    /* Indigo Button for Primary */
    .btn-primary { background: #6366f1; color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25); }
    .btn-primary:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35); }

    /* Alert */
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
        padding: 12px 16px; border-radius: 10px; margin: 24px;
        display: flex; align-items: center; gap: 10px; font-size: 0.9rem;
    }
</style>