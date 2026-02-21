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
while ($row = mysqli_fetch_assoc($siswaRes)) {
    $students[] = $row;
}

// Daftar Bulan Standar
$months = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* LAYOUT WRAPPER */
        .form-page-wrapper {
            padding: 100px 20px 80px;
            max-width: 700px;
            margin: 0 auto;
            min-height: calc(100vh - 70px);
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* MAIN CARD */
        .premium-card {
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .card-cover {
            height: 120px;
            background: radial-gradient(circle at top right, #3b82f6, #4f46e5, #312e81);
            position: relative;
            overflow: hidden;
        }

        .card-cover::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%),
                radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            opacity: 0.3;
            animation: moveBg 20s linear infinite;
        }

        @keyframes moveBg {
            100% {
                transform: translateY(30px);
            }
        }

        .card-body {
            padding: 0 40px 40px;
            margin-top: -50px;
            position: relative;
            z-index: 2;
        }

        .icon-header-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: #ffffff;
            border-radius: 50%;
            padding: 6px;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-header-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #eef2ff, #c7d2fe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 8px 0;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            margin: 0 0 32px 0;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        /* Input with Icon */
        .input-icon-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            pointer-events: none;
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            font-size: 1.05rem;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #4f46e5;
            outline: none;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* --- CUSTOM DROPDOWN STYLE --- */
        .custom-dropdown {
            position: relative;
            width: 100%;
        }

        .custom-dropdown-selected {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 500;
            color: #64748b;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            box-sizing: border-box;
        }

        .custom-dropdown-selected .dropdown-arrow {
            transition: transform 0.3s ease;
            color: #64748b;
        }

        .custom-dropdown-selected.has-value {
            color: #0f172a;
            font-weight: 600;
        }

        .custom-dropdown.open .custom-dropdown-selected {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .custom-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
            color: #4f46e5;
        }

        .custom-dropdown-list {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 10px 40px -5px rgba(0, 0, 0, 0.15);
            z-index: 50;
            max-height: 240px;
            overflow-y: auto;
            display: none;
            animation: fadeInDown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .custom-dropdown-item {
            padding: 14px 16px;
            cursor: pointer;
            color: #334155;
            font-weight: 500;
            border-bottom: 1px solid #f8fafc;
            transition: all 0.15s;
            font-size: 1rem;
        }

        .custom-dropdown-item:hover {
            background: #eef2ff;
            color: #4f46e5;
            padding-left: 20px;
        }

        .custom-dropdown-item:last-child {
            border-bottom: none;
        }

        .custom-dropdown-item.selected {
            background: #4f46e5;
            color: #fff;
            padding-left: 20px;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 32px;
        }

        .btn-action {
            flex: 1;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-cancel:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-save {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        .btn-save:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-left: 5px solid #ef4444;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        .grid-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .form-page-wrapper {
                padding: 20px 16px 80px;
            }

            .premium-card {
                border-radius: 20px;
            }

            .card-body {
                padding: 0 24px 32px;
            }

            .form-actions {
                flex-direction: column-reverse;
                gap: 12px;
            }

            .btn-action {
                width: 100%;
                padding: 16px;
            }

            .grid-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>

    <div class="form-page-wrapper">

        <div class="premium-card">
            <div class="card-cover"></div>

            <div class="card-body">
                <div class="icon-header-wrapper">
                    <div class="icon-header-inner">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <path d="M7 6h10"></path>
                            <path d="M7 18h10"></path>
                        </svg>
                    </div>
                </div>

                <h2 class="form-title">Catat Bulan Iuran</h2>
                <p class="form-subtitle">Buat format pembayaran iuran per bulan untuk siswa.</p>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="formAddReport">

                    <div class="form-group">
                        <label class="form-label">Nama Siswa</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <div class="custom-dropdown" id="siswaDropdown">
                                <div class="custom-dropdown-selected" id="siswaSelected">
                                    <span>Pilih Siswa</span>
                                    <svg class="dropdown-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                <div class="custom-dropdown-list" id="siswaList">
                                    <?php foreach ($students as $s): ?>
                                        <div class="custom-dropdown-item" data-value="<?= $s['id_siswa'] ?>">
                                            <?= htmlspecialchars($s['nama']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="id_siswa" id="siswaInput">
                            </div>
                        </div>
                    </div>

                    <div class="grid-row">
                        <div class="form-group">
                            <label class="form-label">Bulan Iuran</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <div class="custom-dropdown" id="bulanDropdown">
                                    <div class="custom-dropdown-selected" id="bulanSelected">
                                        <span>Pilih Bulan</span>
                                        <svg class="dropdown-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
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
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tahun</label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                        <polyline points="17 6 23 6 23 12"></polyline>
                                    </svg>
                                </span>
                                <input type="number" name="tahun" class="form-input" value="<?= date('Y') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="?page=report" class="btn-action btn-cancel">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Batalkan
                        </a>
                        <button type="submit" name="submit" class="btn-action btn-save">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
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
                        if (d !== dropdown) {
                            d.classList.remove('open');
                            d.querySelector('.custom-dropdown-list').style.display = 'none';
                        }
                    });

                    const isOpen = dropdown.classList.contains('open');
                    if (isOpen) {
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

                if (!siswaVal) {
                    alert('Harap pilih Siswa terlebih dahulu!');
                    e.preventDefault();
                    return false;
                }
                if (!bulanVal) {
                    alert('Harap pilih Bulan terlebih dahulu!');
                    e.preventDefault();
                    return false;
                }
            };
        });
    </script>

</body>

</html>