<?php
// ==========================================
// 1. CONFIG & SESSION & LOGIC (BACKEND)
// ==========================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// Cek Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=report';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// Ambil daftar siswa untuk Dropdown
$siswaRes = mysqli_query($conn, "SELECT id_siswa, nis, nama FROM siswa ORDER BY nama ASC");

// Simpan data siswa ke array dulu agar bisa di-loop di HTML
$students = [];
while($row = mysqli_fetch_assoc($siswaRes)) {
    $students[] = $row;
}

// Daftar Bulan Standar
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// 2. PROSES SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $bulan_input = trim($_POST['bulan'] ?? '');
    $tahun_input = trim($_POST['tahun'] ?? date('Y'));
    
    // Gabungkan Bulan dan Tahun (Format: "Januari 2026")
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
            // Jika belum ada, masukkan data baru
            $sql = "INSERT INTO iuran (id_siswa, bulan, minggu_1, minggu_2, minggu_3, minggu_4) VALUES (?, ?, 'belum','belum','belum','belum')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id_siswa, $bulan_lengkap);
            
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = 'Data iuran bulan ' . htmlspecialchars($bulan_lengkap) . ' berhasil dibuat.';
                echo "<script>window.location='?page=report';</script>";
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . $conn->error;
            }
        }
    } else {
        $error = 'Silakan pilih Siswa dan Bulan terlebih dahulu.';
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

    .form-input {
        width: 100%; padding: 14px 16px;
        border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 1rem; color: #0f172a; background: #fff;
        transition: all 0.2s; font-family: inherit;
    }
    .form-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }

    /* --- CUSTOM DROPDOWN STYLE (SAMA DENGAN EDIT INCOME) --- */
    .custom-dropdown { position: relative; width: 100%; }
    
    .custom-dropdown-selected {
        width: 100%; padding: 14px 16px;
        border: 1px solid #cbd5e1; border-radius: 12px;
        background: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: space-between;
        font-weight: 500; color: #64748b; /* Warna placeholder default */
        transition: all 0.2s;
    }
    .custom-dropdown-selected.has-value { color: #0f172a; font-weight: 600; }
    
    .custom-dropdown.open .custom-dropdown-selected { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
    
    .custom-dropdown-list {
        position: absolute; top: 110%; left: 0; right: 0;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        box-shadow: 0 10px 40px -5px rgba(0,0,0,0.15);
        z-index: 50; max-height: 240px; overflow-y: auto;
        display: none; animation: fadeIn 0.2s;
    }
    
    .custom-dropdown-item {
        padding: 12px 16px; cursor: pointer; color: #334155; font-weight: 500;
        border-bottom: 1px solid #f8fafc; transition: all 0.1s;
    }
    .custom-dropdown-item:hover { background: #eef2ff; color: #6366f1; }
    .custom-dropdown-item:last-child { border-bottom: none; }
    .custom-dropdown-item.selected { background: #6366f1; color: #fff; }

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
        padding: 12px 16px; border-radius: 10px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 500;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Responsive */
    @media (max-width: 480px) {
        .card { padding: 24px; }
        .form-actions { flex-direction: column-reverse; }
        .grid-row { grid-template-columns: 1fr !important; }
    }
    .grid-row { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
</style>
</head>
<body>

<div class="container" style="max-width: 720px; margin: 64px auto; padding: 0 20px 80px 20px;">
    
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

            <form method="POST" action="" id="formAddReport">
                
                <div class="form-group">
                    <label class="form-label">Nama Siswa</label>
                    <div class="custom-dropdown" id="siswaDropdown">
                        <div class="custom-dropdown-selected" id="siswaSelected">
                            <span>Pilih Siswa</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        <div class="custom-dropdown-list" id="siswaList">
                            <?php foreach($students as $s): ?>
                                <div class="custom-dropdown-item" data-value="<?= $s['id_siswa'] ?>">
                                    <?= htmlspecialchars($s['nama']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="id_siswa" id="siswaInput">
                    </div>
                </div>

                <div class="grid-row">
                    <div class="form-group">
                        <label class="form-label">Bulan Iuran</label>
                        <div class="custom-dropdown" id="bulanDropdown">
                            <div class="custom-dropdown-selected" id="bulanSelected">
                                <span>Pilih Bulan</span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div class="custom-dropdown-list" id="bulanList">
                                <?php 
                                $currentMonth = date('n') - 1; 
                                foreach ($months as $idx => $month): 
                                ?>
                                    <div class="custom-dropdown-item" data-value="<?= $month ?>">
                                        <?= $month ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="bulan" id="bulanInput">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- FUNGSI GENERAL UNTUK CUSTOM DROPDOWN ---
    function setupDropdown(dropdownId, selectedId, listId, inputId) {
        const dropdown = document.getElementById(dropdownId);
        const selected = document.getElementById(selectedId);
        const list = document.getElementById(listId);
        const input = document.getElementById(inputId);
        const spanText = selected.querySelector('span');

        // Toggle Open/Close
        selected.addEventListener('click', function(e) {
            e.stopPropagation();
            // Tutup dropdown lain jika ada yang terbuka
            document.querySelectorAll('.custom-dropdown').forEach(d => {
                if(d !== dropdown) {
                    d.classList.remove('open');
                    d.querySelector('.custom-dropdown-list').style.display = 'none';
                }
            });

            const isOpen = dropdown.classList.contains('open');
            if(isOpen) {
                dropdown.classList.remove('open');
                list.style.display = 'none';
            } else {
                dropdown.classList.add('open');
                list.style.display = 'block';
            }
        });

        // Pilih Item
        list.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = this.getAttribute('data-value');
                const text = this.textContent.trim();
                
                input.value = val;
                spanText.textContent = text;
                selected.classList.add('has-value'); // Ubah warna teks jadi hitam/gelap
                
                // Visual feedback
                list.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                
                // Close
                dropdown.classList.remove('open');
                list.style.display = 'none';
            });
        });
    }

    // Setup Dropdown Siswa
    setupDropdown('siswaDropdown', 'siswaSelected', 'siswaList', 'siswaInput');

    // Setup Dropdown Bulan
    setupDropdown('bulanDropdown', 'bulanSelected', 'bulanList', 'bulanInput');

    // Klik di luar menutup semua dropdown
    document.addEventListener('click', function() {
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            dropdown.classList.remove('open');
            dropdown.querySelector('.custom-dropdown-list').style.display = 'none';
        });
    });

    // Validasi Submit
    const form = document.getElementById('formAddReport');
    form.onsubmit = function(e) {
        const siswaVal = document.getElementById('siswaInput').value;
        const bulanVal = document.getElementById('bulanInput').value;

        if(!siswaVal) {
            alert('Harap pilih Siswa terlebih dahulu!');
            e.preventDefault();
            return false;
        }
        if(!bulanVal) {
            alert('Harap pilih Bulan terlebih dahulu!');
            e.preventDefault();
            return false;
        }
    };
});
</script>

</body>
</html>