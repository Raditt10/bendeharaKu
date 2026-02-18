<?php
// --- LOGIKA PHP (BACKEND) ---
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// 1. Cek Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=report';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// Ambil daftar siswa untuk Dropdown
$siswaRes = mysqli_query($conn, "SELECT id_siswa, nis, nama FROM siswa ORDER BY nama ASC");

// Daftar Bulan Standar
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// 2. Proses Simpan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $bulan_input = trim($_POST['bulan'] ?? '');
    $tahun_input = trim($_POST['tahun'] ?? date('Y'));
    
    // Gabungkan Bulan dan Tahun (Sesuai format: "Januari 2026")
    $bulan_lengkap = $bulan_input . ' ' . $tahun_input;

    if ($id_siswa > 0 && $bulan_input !== '') {
        // Cek apakah data iuran untuk siswa ini di bulan tersebut SUDAH ADA?
        $cekStmt = $conn->prepare("SELECT id_iuran FROM iuran WHERE id_siswa = ? AND bulan = ?");
        $cekStmt->bind_param("is", $id_siswa, $bulan_lengkap);
        $cekStmt->execute();
        $cekResult = $cekStmt->get_result();
        
        if ($cekResult->num_rows > 0) {
            $error = 'Siswa ini sudah memiliki catatan iuran untuk bulan ' . htmlspecialchars($bulan_lengkap) . '.';
        } else {
            // Jika belum ada, masukkan data baru (Default: Belum lunas)
            $sql = "INSERT INTO iuran (id_siswa, bulan, minggu_1, minggu_2, minggu_3, minggu_4) VALUES (?, ?, 'belum','belum','belum','belum')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id_siswa, $bulan_lengkap);
            
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = 'Data iuran bulan ' . htmlspecialchars($bulan_lengkap) . ' berhasil dibuat.';
                echo "<script>window.location='?page=report';</script>";
                exit;
            } else {
                $error = 'Gagal menyimpan data iuran: ' . $conn->error;
            }
        }
    } else {
        $error = 'Silakan pilih siswa dan bulan dengan benar.';
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container" style="max-width: 600px; margin: 0 auto; padding-top: 40px; padding-bottom: 80px;">
    
    <div style="margin-bottom: 20px;">
        <a href="?page=report" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; transition: color 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke Laporan
        </a>
    </div>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Catat Bulan Iuran</h2>
                <p>Buat format pembayaran iuran per bulan untuk siswa.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div class="form-group">
                    <label class="form-label">Nama Siswa</label>
                    <div class="custom-select-wrapper">
                        <select name="id_siswa" class="form-input custom-select" required>
                            <option value="" disabled selected>-- Pilih Siswa --</option>
                            <?php while ($row = mysqli_fetch_assoc($siswaRes)): ?>
                                <option value="<?= $row['id_siswa'] ?>">
                                    <?= htmlspecialchars($row['nama']) ?> (NIS: <?= htmlspecialchars($row['nis']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="select-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Bulan Iuran</label>
                        <div class="custom-select-wrapper">
                            <select name="bulan" class="form-input custom-select" required>
                                <option value="" disabled selected>-- Pilih Bulan --</option>
                                <?php 
                                $currentMonth = date('n') - 1; // Index array (0-11)
                                foreach ($months as $idx => $month): 
                                    // Default terisi bulan saat ini
                                    $selected = ($idx == $currentMonth) ? 'selected' : '';
                                ?>
                                    <option value="<?= $month ?>" <?= $selected ?>><?= $month ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="select-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-input" value="<?= date('Y') ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="?page=report" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="submit" class="btn btn-primary">
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
    form { padding: 24px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.95rem; }

    .form-input {
        width: 100%; padding: 14px 16px;
        border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 1rem; color: #0f172a; background: #fff;
        transition: all 0.2s; font-family: inherit;
    }
    .form-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }

    /* Custom Select Styling */
    .custom-select-wrapper { position: relative; }
    .custom-select {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        padding-right: 45px; cursor: pointer; color: #1e293b;
    }
    .select-icon {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        pointer-events: none; color: #64748b; display: flex; align-items: center;
    }
    .custom-select:focus + .select-icon { color: #6366f1; }

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
    
    /* Indigo Button */
    .btn-primary { background: #6366f1; color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25); }
    .btn-primary:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35); }

    /* Alert Error */
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
        padding: 12px 16px; border-radius: 10px; margin: 24px 24px 0 24px;
        display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 500;
    }
</style>