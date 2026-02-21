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

        textarea.form-input {
            padding: 16px;
            min-height: 100px;
            resize: vertical;
        }

        /* Input Rupiah (Blue Theme) */
        .input-group-rupiah {
            display: flex;
            align-items: stretch;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .input-group-rupiah:focus-within {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .input-group-rupiah .prefix {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 800;
            padding: 0 20px;
            border-right: 1.5px solid #c7d2fe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .input-group-rupiah .input-no-border {
            border: none;
            box-shadow: none;
            background: transparent;
            padding-left: 16px;
            width: 100%;
        }

        .input-group-rupiah .input-no-border:focus {
            outline: none;
            background: transparent;
            box-shadow: none;
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
                            <line x1="12" y1="19" x2="12" y2="5"></line>
                            <polyline points="5 12 12 5 19 12"></polyline>
                            <path d="M16 11h.01"></path>
                            <path d="M8 17h.01"></path>
                        </svg>
                    </div>
                </div>

                <h2 class="form-title">Edit Pemasukan</h2>
                <p class="form-subtitle">Perbarui detail transaksi uang kas masuk.</p>

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

                <form method="POST" action="" id="formEditIncome">

                    <div class="form-group">
                        <label class="form-label">Bulan Transaksi</label>
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
                                <div class="custom-dropdown-selected has-value" id="bulanDropdownSelected">
                                    <span><?= htmlspecialchars($data['bulan']) ?></span>
                                    <svg class="dropdown-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
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
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nominal Masuk (Rp)</label>
                        <div class="input-group-rupiah">
                            <span class="prefix">Rp</span>
                            <input type="text" id="jumlah_display" class="form-input input-no-border"
                                placeholder="0" autocomplete="off" required
                                style="font-size: 1.25rem; font-weight: 800; color: #10b981;"
                                value="<?= number_format($data['jumlah'], 0, ',', '.') ?>">

                            <input type="hidden" name="jumlah" id="jumlah_real" value="<?= $data['jumlah'] ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan / Sumber Dana</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-input"
                            placeholder="Contoh: Uang Kas Wajib, Donasi, dll..." required><?= htmlspecialchars($data['keterangan']) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="?page=income" class="btn-action btn-cancel">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Batalkan
                        </a>
                        <button type="submit" class="btn-action btn-save">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
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
                if (isOpen) {
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

            displayInput.addEventListener('keyup', function(e) {
                let val = this.value.replace(/[^0-9]/g, ''); // Hanya angka
                realInput.value = val; // Simpan angka murni ke hidden input
                this.value = formatRupiah(val); // Tampilkan angka terformat
            });

            // 3. VALIDASI FORM
            const form = document.getElementById('formEditIncome');
            form.onsubmit = function(e) {
                if (!input.value) {
                    e.preventDefault();
                    alert('Silakan pilih Bulan terlebih dahulu!');
                    return false;
                }
                if (!realInput.value || realInput.value == 0) {
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