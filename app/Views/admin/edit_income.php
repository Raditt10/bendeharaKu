<?php
// ==========================================
// 1. CONFIG & SESSION & LOGIC (BACKEND)
// ==========================================
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);

require_once $basePath . '/config/config.php';
require_once $basePath . '/app/Models/Database.php';

// Validasi Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=income';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// Data Bulan untuk Dropdown
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// 2. AMBIL DATA YANG AKAN DIEDIT
$id_pemasukan = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id_pemasukan)) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    echo "<script>window.location='?page=income';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $id_pemasukan);
$query = "SELECT * FROM pemasukan WHERE id_pemasukan = '$id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_msg'] = 'Data tidak ditemukan.';
    echo "<script>window.location='?page=income';</script>";
    exit;
}

$data = mysqli_fetch_assoc($result);

// 3. PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data & Sanitasi
    $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan'] ?? ''));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    // Hapus titik ribuan sebelum simpan ke DB
    $jumlah = mysqli_real_escape_string($conn, str_replace('.', '', $_POST['jumlah'] ?? '0')); 

    // Validasi
    if (empty($bulan) || $jumlah <= 0) {
        $error = 'Silakan pilih Bulan dan Nominal harus lebih dari 0.';
    } else {
        // Query UPDATE
        $update = "UPDATE pemasukan SET bulan='$bulan', jumlah='$jumlah', keterangan='$keterangan' WHERE id_pemasukan='$id'";
        
        if (mysqli_query($conn, $update)) {
            $_SESSION['success_msg'] = 'Data pemasukan berhasil diperbarui!';
            echo "<script>window.location='?page=income';</script>";
            exit;
        } else {
            $error = 'Gagal update: ' . mysqli_error($conn);
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
        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; overflow: hidden; padding: 48px 40px 40px 40px; margin-bottom: 32px; }
        .card-header { padding: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 24px; }
        .card-header h2 { margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 800; letter-spacing: -0.02em; }
        .card-header p { margin: 4px 0 0 0; font-size: 0.9rem; color: #64748b; }

        /* FORM ELEMENTS */
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.95rem; }

        .form-input {
            width: 100%; padding: 14px 16px;
            border: 1px solid #cbd5e1; border-radius: 12px;
            font-size: 1rem; color: #0f172a; background: #fff;
            transition: all 0.2s; font-family: inherit;
        }
        .form-input:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        textarea.form-input { resize: vertical; min-height: 100px; }

        /* Input Rupiah (Blue Theme) */
        .input-group-rupiah {
            display: flex; align-items: center;
            border: 1px solid #cbd5e1; border-radius: 12px;
            background: #fff; overflow: hidden;
            transition: all 0.2s;
        }
        .input-group-rupiah:focus-within { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .input-group-rupiah .prefix {
            background: #eff6ff; color: #2563eb; font-weight: 700;
            padding: 14px 16px; border-right: 1px solid #dbeafe;
        }
        .input-group-rupiah .input-no-border { border: none; box-shadow: none; background: transparent; }

        /* Custom Dropdown */
        .custom-dropdown { position: relative; width: 100%; }
        .custom-dropdown-selected {
            width: 100%; padding: 14px 16px;
            border: 1px solid #cbd5e1; border-radius: 12px;
            background: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: space-between;
            font-weight: 500; color: #0f172a; transition: all 0.2s;
        }
        .custom-dropdown.open .custom-dropdown-selected { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .custom-dropdown-list {
            position: absolute; top: 110%; left: 0; right: 0;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            box-shadow: 0 10px 40px -5px rgba(0,0,0,0.15);
            z-index: 10; max-height: 240px; overflow-y: auto;
            display: none; animation: fadeIn 0.2s;
        }
        .custom-dropdown-item {
            padding: 12px 16px; cursor: pointer; color: #334155; font-weight: 500;
            border-bottom: 1px solid #f8fafc; transition: all 0.1s;
        }
        .custom-dropdown-item:hover { background: #eff6ff; color: #2563eb; }
        .custom-dropdown-item:last-child { border-bottom: none; }
        .custom-dropdown-item.selected { background: #2563eb; color: #fff; }

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
        
        /* Blue Button for Income */
        .btn-primary { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35); }

        /* Alert */
        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
            padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 10px; font-size: 0.9rem;
        }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
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
        <a href="?page=income" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; transition: color 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Edit Pemasukan</h2>
                <p>Perbarui detail transaksi uang kas masuk.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="formEditIncome">
                
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <div class="custom-dropdown" id="bulanDropdown">
                        <div class="custom-dropdown-selected" id="bulanDropdownSelected">
                            <span><?= htmlspecialchars($data['bulan']) ?></span>
                            <svg class="dropdown-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="custom-dropdown-list" id="bulanDropdownList">
                            <?php foreach ($months as $month): ?>
                                <div class="custom-dropdown-item <?= ($month == $data['bulan']) ? 'selected' : '' ?>" data-value="<?= $month ?>">
                                    <?= $month ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="bulan" id="bulanDropdownInput" value="<?= htmlspecialchars($data['bulan']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal Masuk (Rp)</label>
                    <div class="input-group-rupiah">
                        <span class="prefix">Rp</span>
                        <input type="text" id="jumlah_display" class="form-input input-no-border" 
                               placeholder="0" autocomplete="off" required 
                               style="font-size: 1.2rem; font-weight: 700; color: #10b981;"
                               value="<?= number_format($data['jumlah'], 0, ',', '.') ?>">
                        
                        <input type="hidden" name="jumlah" id="jumlah_real" value="<?= $data['jumlah'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Sumber Dana</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="form-input" 
                              placeholder="Contoh: Uang Kas Wajib, Donasi, dll..."><?= htmlspecialchars($data['keterangan']) ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="?page=income" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. DROPDOWN LOGIC
    const dropdown = document.getElementById('bulanDropdown');
    const selected = document.getElementById('bulanDropdownSelected');
    const list = document.getElementById('bulanDropdownList');
    const input = document.getElementById('bulanDropdownInput');
    const spanText = selected.querySelector('span');

    selected.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = dropdown.classList.contains('open');
        if(isOpen) {
            dropdown.classList.remove('open');
            list.style.display = 'none';
        } else {
            dropdown.classList.add('open');
            list.style.display = 'block';
        }
    });

    document.addEventListener('click', function() {
        dropdown.classList.remove('open');
        list.style.display = 'none';
    });

    list.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            const val = this.getAttribute('data-value');
            input.value = val;
            spanText.textContent = val; // Update Text
            
            // Visual feedback
            list.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
            
            // Close
            dropdown.classList.remove('open');
            list.style.display = 'none';
        });
    });

    // 2. FORMAT RUPIAH RIBUAN
    const displayInput = document.getElementById('jumlah_display');
    const realInput = document.getElementById('jumlah_real');
    
    // Fungsi untuk memformat angka dengan titik
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    displayInput.addEventListener('keyup', function(e){
        let val = this.value.replace(/[^0-9]/g, ''); // Hanya angka
        realInput.value = val; // Simpan angka murni ke hidden input
        this.value = formatRupiah(val); // Tampilkan angka terformat
    });

    // 3. VALIDASI FORM
    const form = document.getElementById('formEditIncome');
    form.onsubmit = function(e) {
        if(!input.value) {
            e.preventDefault();
            alert('Silakan pilih Bulan terlebih dahulu!');
            return false;
        }
        if(!realInput.value || realInput.value == 0) {
            e.preventDefault();
            alert('Nominal pemasukan tidak boleh kosong!');
            displayInput.focus();
            return false;
        }
    };
});
</script>

</body>
</html>