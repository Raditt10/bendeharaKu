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
$id_siswa = $_GET['id'] ?? null;

// Validasi ID
if (!$id_siswa) {
    echo "<script>alert('ID Siswa tidak ditemukan!'); window.location='?page=students';</script>";
    exit;
}

// Ambil Data Lama
$stmt = $conn->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
$stmt->bind_param("i", $id_siswa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data siswa tidak ditemukan.'); window.location='?page=students';</script>";
    exit;
}
$data = $result->fetch_assoc();

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kontak = trim($_POST['kontak'] ?? '');

    // Ubah 08xx jadi 628xx
    if (substr($kontak, 0, 1) === '0') {
        $kontak = '62' . substr($kontak, 1);
    }

    if (empty($nis) || empty($nama)) {
        $error = 'NIS dan Nama Siswa wajib diisi.';
    } else {
        // Cek duplikasi NIS (kecuali punya sendiri)
        $cekStmt = $conn->prepare("SELECT id_siswa FROM siswa WHERE nis = ? AND id_siswa != ?");
        $cekStmt->bind_param("si", $nis, $id_siswa);
        $cekStmt->execute();
        
        if ($cekStmt->get_result()->num_rows > 0) {
            $error = "NIS '$nis' sudah digunakan siswa lain.";
        } else {
            $updateStmt = $conn->prepare("UPDATE siswa SET nis = ?, nama = ?, kontak_orangtua = ? WHERE id_siswa = ?");
            $updateStmt->bind_param("sssi", $nis, $nama, $kontak, $id_siswa);
            
            if ($updateStmt->execute()) {
                $_SESSION['success_msg'] = 'Data siswa berhasil diperbarui.';
                echo "<script>window.location='?page=students';</script>";
                exit;
            } else {
                $error = 'Gagal update: ' . $conn->error;
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

    /* CARD STYLES - LEBIH LEBAR */
    .card { 
        background: #fff; 
        border-radius: 20px; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.12); 
        border: 1px solid #e2e8f0; 
        padding: 48px 40px 40px 40px; /* Padding dalam diperbesar */
        overflow: hidden; 
        margin-bottom: 32px;
    }
    
    .card-header { 
        padding: 0 0 24px 0; 
        border-bottom: 1px solid #f1f5f9; 
        margin-bottom: 24px; 
    }
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
    
    /* Indigo Focus for Student */
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
    
    /* Indigo Button for Primary */
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
                <h2>Edit Data Siswa</h2>
                <p>Perbarui informasi siswa kelas XI RPL 1.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="formEditStudent">
                
                <div class="form-group">
                    <label class="form-label">NIS / NISN</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </span>
                        <input type="number" name="nis" class="form-input with-icon" placeholder="Contoh: 232410..." required value="<?= htmlspecialchars($data['nis']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </span>
                        <input type="text" name="nama" class="form-input with-icon" placeholder="Nama Siswa" required style="text-transform: capitalize;" value="<?= htmlspecialchars($data['nama']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">No. HP / WhatsApp Orang Tua</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </span>
                        <input type="number" name="kontak" class="form-input with-icon" placeholder="08..." value="<?= htmlspecialchars($data['kontak_orangtua']) ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="?page=students" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>