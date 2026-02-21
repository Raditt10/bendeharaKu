<?php
// ==========================================
// 1. CONFIG & SESSION & LOGIC (BACKEND)
// ==========================================
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);

// Load Config & Database
if (file_exists($basePath . '/config/config.php')) require_once $basePath . '/config/config.php';
if (file_exists($basePath . '/app/Models/Database.php')) require_once $basePath . '/app/Models/Database.php';

// Validasi Role (Admin Only)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // header('Location: ?page=expenses'); exit; 
}

$conn = Database::getInstance()->getConnection();
$error_msg = '';

// ==========================================
// 2. GET DATA (AMBIL DATA LAMA)
// ==========================================
$id_pengeluaran = isset($_GET['id_pengeluaran']) ? mysqli_real_escape_string($conn, $_GET['id_pengeluaran']) : '';

if (empty($id_pengeluaran)) {
    echo "<script>alert('ID tidak ditemukan'); window.location='?page=expenses';</script>";
    exit;
}

$query = "SELECT * FROM pengeluaran WHERE id_pengeluaran = '$id_pengeluaran'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data tidak ditemukan'); window.location='?page=expenses';</script>";
    exit;
}

$data = mysqli_fetch_assoc($result);

// ==========================================
// 3. PROCESS UPDATE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    // Hapus titik dari format rupiah sebelum simpan ke DB
    $jumlah = mysqli_real_escape_string($conn, str_replace('.', '', $_POST['jumlah']));
    $tahun = date('Y', strtotime($tanggal));

    $bukti_foto = $data['bukti_foto']; // Default pakai foto lama

    // Logic Upload Foto Baru
    if (isset($_FILES['bukti_struk']) && $_FILES['bukti_struk']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['bukti_struk'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $newName = 'struk_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $uploadDir = $basePath . '/public/uploads/';

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                // Hapus foto lama jika ada
                if (!empty($data['bukti_foto']) && file_exists($uploadDir . $data['bukti_foto'])) {
                    unlink($uploadDir . $data['bukti_foto']);
                }
                $bukti_foto = $newName;
            }
        } else {
            $error_msg = "Format file salah atau ukuran terlalu besar (Max 5MB).";
        }
    }

    if (empty($error_msg)) {
        $updateSql = "UPDATE pengeluaran SET 
                      tanggal='$tanggal', 
                      tahun='$tahun', 
                      jumlah='$jumlah', 
                      keterangan='$keterangan', 
                      bukti_foto='$bukti_foto' 
                      WHERE id_pengeluaran='$id_pengeluaran'";

        if (mysqli_query($conn, $updateSql)) {
            $_SESSION['success_msg'] = 'Data pengeluaran berhasil diperbarui.';
            echo "<script>window.location.href='?page=expenses';</script>";
            exit;
        } else {
            $error_msg = "Gagal update database: " . mysqli_error($conn);
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
            background: radial-gradient(circle at top right, #ef4444, #b91c1c, #7f1d1d);
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
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-header-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #fef2f2, #fecaca);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
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
            color: #ef4444;
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
            border-color: #ef4444;
            outline: none;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        textarea.form-input {
            padding: 16px;
            min-height: 100px;
            resize: vertical;
        }

        /* Input Rupiah (Red Theme) */
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
            border-color: #ef4444;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-group-rupiah .prefix {
            background: #fef2f2;
            color: #ef4444;
            font-weight: 800;
            padding: 0 20px;
            border-right: 1.5px solid #fecaca;
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

        /* Upload Box */
        .custom-upload-box {
            border: 2px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .custom-upload-box:hover {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
            color: #ef4444;
        }

        .img-preview {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .remove-img-btn {
            display: none;
            margin-top: 12px;
            border: none;
            background: none;
            color: #ef4444;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s;
        }

        .remove-img-btn:hover {
            color: #b91c1c;
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
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
        }

        .btn-save:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
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

        /* --- DATEPICKER STYLES (RED THEME) --- */
        .custom-datepicker {
            position: relative;
            width: 100%;
        }

        #tanggal_display {
            cursor: pointer;
            background: #fff;
            padding-right: 40px;
        }

        .datepicker-popup {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            z-index: 20;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 40px -5px rgba(0, 0, 0, 0.15);
            padding: 20px;
            width: 320px;
            animation: fadeInDropdown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: none;
        }

        .datepicker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .datepicker-header button {
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: #334155;
            cursor: pointer;
            font-weight: bold;
        }

        .datepicker-header button:hover {
            background: #e2e8f0;
        }

        .datepicker-header span {
            font-weight: 700;
            color: #1e293b;
            font-size: 1rem;
        }

        .datepicker-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .datepicker-weekday {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .datepicker-day {
            text-align: center;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            border-radius: 8px;
            cursor: pointer;
            color: #334155;
            transition: all 0.15s;
        }

        .datepicker-day:hover {
            background: #fef2f2;
            color: #ef4444;
        }

        .datepicker-day.today {
            border: 1px solid #ef4444;
            color: #ef4444;
            font-weight: 600;
        }

        .datepicker-day.selected {
            background: #ef4444;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
            border: none;
        }

        .datepicker-day.disabled {
            color: #e2e8f0;
            pointer-events: none;
        }

        .datepicker-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .datepicker-footer button {
            background: none;
            border: none;
            color: #ef4444;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .datepicker-overlay {
            display: none;
            position: fixed;
            z-index: 19;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
        }

        /* MOBILE DATEPICKER (BOTTOM SHEET) */
        @media (max-width: 600px) {
            .datepicker-popup {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                top: auto;
                width: 100%;
                border-radius: 24px 24px 0 0;
                padding: 24px;
                animation: slideUpSheet 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            .datepicker-overlay {
                display: block;
            }
        }

        @keyframes fadeInDropdown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUpSheet {
            from {
                transform: translateY(100%);
            }

            to {
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
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <polyline points="19 12 12 19 5 12"></polyline>
                            <path d="M16 11h.01"></path>
                            <path d="M8 17h.01"></path>
                        </svg>
                    </div>
                </div>

                <h2 class="form-title">Edit Pengeluaran</h2>
                <p class="form-subtitle">Perbarui detail transaksi pengeluaran kas.</p>

                <?php if ($error_msg): ?>
                    <div class="alert alert-error">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?= $error_msg ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="formExpense" enctype="multipart/form-data">

                    <div class="form-group">
                        <label class="form-label">Tanggal Transaksi</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <div class="custom-datepicker" id="customDatepicker">
                                <input type="text" id="tanggal_display" class="form-input" placeholder="Pilih tanggal" readonly required autocomplete="off">
                                <input type="hidden" name="tanggal" id="tanggal" value="<?= $data['tanggal'] ?>">

                                <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>

                                <div class="datepicker-popup" id="datepickerPopup" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nominal Keluar (Rp)</label>
                        <div class="input-group-rupiah">
                            <span class="prefix">Rp</span>
                            <input type="text" id="jumlah_display" class="form-input input-no-border"
                                placeholder="0" autocomplete="off" required
                                style="font-size: 1.25rem; font-weight: 800; color: #ef4444;"
                                value="<?= number_format($data['jumlah'], 0, ',', '.') ?>">
                            <input type="hidden" name="jumlah" id="jumlah_real" value="<?= $data['jumlah'] ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan / Keperluan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-input" required
                            placeholder="Contoh: Beli spidol, Fotocopy materi..."><?= htmlspecialchars($data['keterangan']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bukti Struk <span style="font-weight:500; color:#94a3b8; font-size:0.9em;">(Opsional)</span></label>
                        <div id="customUploadBox" class="custom-upload-box" onclick="document.getElementById('bukti_struk').click();">

                            <div id="uploadPlaceholder" style="display: <?= !empty($data['bukti_foto']) ? 'none' : 'block' ?>;">
                                <div class="upload-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                </div>
                                <span style="font-weight:700; color:#1e293b; display:block; font-size:1.05rem;">Ketuk untuk ganti foto</span>
                                <span style="display:block; font-size:0.9rem; color:#64748b; margin-top:6px;">JPG/PNG, Maksimal 5MB</span>
                            </div>

                            <img id="previewImg"
                                src="<?= !empty($data['bukti_foto']) ? 'public/uploads/' . $data['bukti_foto'] : '#' ?>"
                                alt="Preview"
                                class="img-preview"
                                style="display: <?= !empty($data['bukti_foto']) ? 'block' : 'none' ?>;" />
                        </div>

                        <input type="file" name="bukti_struk" id="bukti_struk" accept="image/jpeg,image/png" style="display:none;" onchange="previewStruk(event)">

                        <button type="button" id="removeImgBtn" class="remove-img-btn"
                            style="display: <?= !empty($data['bukti_foto']) ? 'inline-block' : 'none' ?>;"
                            onclick="removeImage(event)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Hapus Foto Baru / Batalkan Upload
                        </button>
                        <!-- Catatan: Untuk keamanan, fitur hapus foto lama dari DB bisa butuh logic PHP tambahan. Ini visual hapus foto baru -->
                    </div>

                    <div class="form-actions">
                        <a href="?page=expenses" class="btn-action btn-cancel">
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
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="datepicker-overlay" id="datepickerOverlay" style="display:none;"></div>

    <script>
        // 1. DATEPICKER LOGIC (REVISED FOR RED THEME)
        document.addEventListener('DOMContentLoaded', function() {
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const input = document.getElementById('tanggal_display');
            const hidden = document.getElementById('tanggal');
            const popup = document.getElementById('datepickerPopup');
            const overlay = document.getElementById('datepickerOverlay');

            let selected = hidden.value ? new Date(hidden.value) : new Date();
            let viewMonth = selected.getMonth();
            let viewYear = selected.getFullYear();

            function pad(n) {
                return n < 10 ? '0' + n : n;
            }

            function formatDate(d) {
                return pad(d.getDate()) + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear();
            }

            function formatValue(d) {
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            }

            function render() {
                let d = new Date(viewYear, viewMonth, 1);
                let firstDay = d.getDay();
                let lastDate = new Date(viewYear, viewMonth + 1, 0).getDate();
                let today = new Date();

                let html = `
            <div class="datepicker-header">
                <button type="button" id="prevMonth"><</button>
                <span>${monthNames[viewMonth]} ${viewYear}</span>
                <button type="button" id="nextMonth">></button>
            </div>
            <div class="datepicker-grid">`;

                for (let i = 0; i < 7; i++) html += `<div class="datepicker-weekday">${dayNames[i]}</div>`;

                let day = 1;
                // 42 cells grid (6 rows)
                for (let i = 0; i < 42; i++) {
                    if (i < firstDay || day > lastDate) {
                        html += '<div class="datepicker-day disabled"></div>';
                    } else {
                        let dateObj = new Date(viewYear, viewMonth, day);
                        let isToday = dateObj.toDateString() === today.toDateString();
                        let isSelected = selected && dateObj.toDateString() === selected.toDateString();

                        let classes = 'datepicker-day';
                        if (isToday) classes += ' today';
                        if (isSelected) classes += ' selected';

                        html += `<div class="${classes}" data-date="${formatValue(dateObj)}">${day}</div>`;
                        day++;
                    }
                }
                html += `</div>
            <div class="datepicker-footer">
                <button type="button" id="todayDate">Hari Ini</button>
                <button type="button" onclick="document.getElementById('datepickerPopup').style.display='none'; document.getElementById('datepickerOverlay').style.display='none';" style="color:#64748b;">Tutup</button>
            </div>`;

                popup.innerHTML = html;

                // Re-attach listeners
                document.getElementById('prevMonth').onclick = (e) => {
                    e.stopPropagation();
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    render();
                };
                document.getElementById('nextMonth').onclick = (e) => {
                    e.stopPropagation();
                    viewMonth++;
                    if (viewMonth > 11) {
                        viewMonth = 0;
                        viewYear++;
                    }
                    render();
                };
                document.getElementById('todayDate').onclick = () => {
                    let d = new Date();
                    updateDate(d);
                };

                popup.querySelectorAll('.datepicker-day:not(.disabled)').forEach(el => {
                    el.onclick = function() {
                        let parts = this.getAttribute('data-date').split('-');
                        let d = new Date(parts[0], parseInt(parts[1]) - 1, parts[2]);
                        updateDate(d);
                    };
                });
            }

            function updateDate(d) {
                selected = d;
                viewMonth = d.getMonth();
                viewYear = d.getFullYear();
                hidden.value = formatValue(d);
                input.value = formatDate(d);
                hideDatepicker();
            }

            function showDatepicker() {
                popup.style.display = 'block';
                if (window.innerWidth <= 600) overlay.style.display = 'block';
                render();
            }

            function hideDatepicker() {
                popup.style.display = 'none';
                overlay.style.display = 'none';
            }

            // Init Logic
            input.addEventListener('click', showDatepicker);
            overlay.addEventListener('click', hideDatepicker);

            // Set Initial
            if (hidden.value) {
                let parts = hidden.value.split('-');
                let d = new Date(parts[0], parseInt(parts[1]) - 1, parts[2]);
                input.value = formatDate(d);
            }
        });

        // 2. FORMAT RUPIAH RIBUAN
        const displayInput = document.getElementById('jumlah_display');
        const realInput = document.getElementById('jumlah_real');

        displayInput.addEventListener('keyup', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            realInput.value = val;
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });

        // 3. PREVIEW GAMBAR
        function previewStruk(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('previewImg');
            const placeholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImgBtn');

            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB!');
                    removeImage(event);
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    removeBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage(event) {
            if (event) {
                event.stopPropagation();
            }
            const input = document.getElementById('bukti_struk');
            const preview = document.getElementById('previewImg');
            const placeholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImgBtn');

            input.value = '';

            <?php if (!empty($data['bukti_foto'])): ?>
                // If there is an existing database image, we revert to showing it,
                // or just let it fall back. We will just restore to base database state
                preview.src = 'public/uploads/<?= $data['bukti_foto'] ?>';
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                removeBtn.style.display = 'inline-block';
            <?php else: ?>
                preview.src = '#';
                preview.style.display = 'none';
                placeholder.style.display = 'block';
                removeBtn.style.display = 'none';
            <?php endif; ?>
        }
    </script>

</body>

</html>