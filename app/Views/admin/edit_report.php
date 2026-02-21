<?php
// --- LOGIKA PHP (BACKEND) ---
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// 1. Cek Role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=report';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';
$id_iuran = $_GET['id'] ?? $_GET['id_iuran'] ?? null;

// 2. Cek ID
if (!$id_iuran) {
    echo "<script>alert('ID Iuran tidak ditemukan!'); window.location='?page=report';</script>";
    exit;
}

// 3. Ambil Data
$stmt = $conn->prepare("SELECT i.*, s.nama AS nama_siswa, s.nis 
                        FROM iuran i 
                        JOIN siswa s ON i.id_siswa = s.id_siswa 
                        WHERE i.id_iuran = ?");
$stmt->bind_param("i", $id_iuran);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='?page=report';</script>";
    exit;
}
$data = $result->fetch_assoc();

// 4. Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan = trim($_POST['bulan'] ?? '');
    $minggu_checked = $_POST['minggu'] ?? [];

    $updateQuery = "UPDATE iuran SET bulan = ?, ";
    $params = [$bulan];
    $types = "s";

    for ($i = 1; $i <= 4; $i++) {
        $is_paid = in_array((string)$i, $minggu_checked);
        $status_minggu = $is_paid ? 'lunas' : 'belum';
        $tgl_bayar = !empty($_POST["tgl_bayar_minggu_$i"]) ? $_POST["tgl_bayar_minggu_$i"] : null;

        if (!$is_paid) {
            $tgl_bayar = null;
        } elseif ($is_paid && empty($tgl_bayar)) {
            $tgl_bayar = date('Y-m-d');
        }

        $updateQuery .= "minggu_$i = ?, tgl_bayar_minggu_$i = ?, ";
        $params[] = $status_minggu;
        $params[] = $tgl_bayar;
        $types .= "ss";
    }

    $updateQuery = rtrim($updateQuery, ", ") . " WHERE id_iuran = ?";
    $params[] = $id_iuran;
    $types .= "i";

    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param($types, ...$params);

    if ($stmtUpdate->execute()) {
        $_SESSION['success_msg'] = 'Data pembayaran iuran berhasil diperbarui.';
        echo "<script>window.location='?page=report';</script>";
        exit;
    } else {
        $error = 'Gagal update data: ' . $conn->error;
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* Hilangkan efek overlay bawaan jika ada bentrok */
    .datepicker-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 998;
        transition: all 0.3s ease;
    }

    .datepicker-popup {
        display: none;
        position: absolute;
        z-index: 999;
        background: #ffffff;
        width: 320px;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
        border: 1px solid #e2e8f0;
        animation: popupScale 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes popupScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* HEADER DATEPICKER */
    .dp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .dp-header button {
        background: #f1f5f9;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        color: #4f46e5;
        font-weight: bold;
        font-size: 1.1rem;
        transition: all 0.2s;
    }

    .dp-header button:hover {
        background: #e0e7ff;
        color: #4338ca;
    }

    .dp-header span {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.05rem;
    }

    /* GRID TANGGAL */
    .dp-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        text-align: center;
    }

    .dp-day-name {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .dp-day {
        padding: 10px 0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s;
        font-weight: 500;
    }

    .dp-day:hover {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .dp-day.selected {
        background: #4f46e5;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    .dp-day.today {
        border: 1.5px solid #4f46e5;
        color: #4f46e5;
    }

    .dp-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px dashed #e2e8f0;
        text-align: center;
    }

    .dp-btn-today {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 20px;
        color: #0f172a;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }

    .dp-btn-today:hover {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }

    @media (max-width: 600px) {
        .datepicker-popup {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: auto !important;
            width: 100%;
            border-radius: 28px 28px 0 0;
            padding: 24px 24px 40px;
            transform: translateY(100%);
            animation: slideUpSheet 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .bottom-sheet-handle {
            width: 48px;
            height: 6px;
            background: #cbd5e1;
            border-radius: 10px;
            margin: -10px auto 24px auto;
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

    /* GLOBAL RESET */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
    }

    /* LAYOUT WRAPPER */
    .form-page-wrapper {
        padding: 40px 20px 80px;
        max-width: 750px;
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

    /* BACK BUTTON */
    .btn-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 24px;
        transition: color 0.2s ease;
        font-size: 0.95rem;
    }

    .btn-back-link:hover {
        color: #4f46e5;
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
        height: 140px;
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
        margin-top: -60px;
        position: relative;
        z-index: 2;
    }

    .icon-header-wrapper {
        width: 110px;
        height: 110px;
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
        margin: 0 0 16px 0;
        text-align: center;
        letter-spacing: -0.02em;
    }

    .student-info-box {
        background: #f8fafc;
        padding: 16px 20px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        text-align: center;
        margin-bottom: 32px;
    }

    .student-name {
        display: block;
        font-weight: 800;
        font-size: 1.3rem;
        color: #1e293b;
        letter-spacing: -0.01em;
    }

    .student-nis {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.95rem;
        color: #64748b;
        margin-top: 6px;
        font-weight: 600;
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

    .input-icon-wrapper {
        position: relative;
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

    .form-input[readonly] {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    /* GRID & CHECKBOX */
    .payment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 32px;
    }

    .payment-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .payment-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.05);
    }

    .payment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .custom-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        gap: 12px;
    }

    .custom-checkbox input {
        display: none;
    }

    .checkmark {
        width: 24px;
        height: 24px;
        background-color: #f8fafc;
        border: 2px solid #cbd5e1;
        border-radius: 8px;
        position: relative;
        transition: all 0.2s;
    }

    .custom-checkbox input:checked~.checkmark {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 7px;
        top: 3px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
    }

    .custom-checkbox input:checked~.checkmark:after {
        display: block;
    }

    .payment-label {
        font-weight: 800;
        color: #1e293b;
        font-size: 1.05rem;
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
        letter-spacing: 0.05em;
    }

    .status-badge.paid {
        background: #ecfdf5;
        color: #10b981;
        border-color: #a7f3d0;
    }

    .payment-date {
        margin-top: 12px;
        padding-top: 16px;
        border-top: 1px dashed #e2e8f0;
        transition: opacity 0.3s;
    }

    .payment-date label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }

    .custom-datepicker-wrapper {
        position: relative;
    }

    .custom-datepicker-wrapper .form-input {
        padding: 14px 44px 14px 16px;
        font-size: 0.95rem;
        background: #fff;
        cursor: pointer;
    }

    .custom-datepicker-wrapper .form-input:hover {
        border-color: #94a3b8;
    }

    .calendar-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #4f46e5;
        font-size: 1.2rem;
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
        padding: 16px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        border: none;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .btn-cancel {
        background: #ffffff;
        color: #334155;
        border: 1.5px solid #cbd5e1;
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
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-left: 5px solid #ef4444;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
        font-weight: 600;
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

        .payment-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column-reverse;
            gap: 12px;
        }

        .btn-action {
            width: 100%;
        }
    }
</style>

<div class="form-page-wrapper">

    <div class="premium-card">
        <div class="card-cover"></div>

        <div class="card-body">
            <div class="icon-header-wrapper">
                <div class="icon-header-inner">
                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
            </div>

            <h2 class="form-title">Edit Status Pembayaran</h2>

            <div class="student-info-box">
                <span class="student-name"><?= htmlspecialchars($data['nama_siswa']) ?></span>
                <span class="student-nis">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    NIS: <?= htmlspecialchars($data['nis']) ?>
                </span>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label class="form-label">Bulan Tagihan Kas</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                        <input type="text" name="bulan" class="form-input" value="<?= htmlspecialchars($data['bulan']) ?>" required readonly>
                    </div>
                </div>

                <div class="payment-grid">
                    <?php for ($i = 1; $i <= 4; $i++):
                        $isLunas = $data["minggu_$i"] === 'lunas';
                        $tglDB = $data["tgl_bayar_minggu_$i"];
                        $tglDisplay = $tglDB ? date('d F Y', strtotime($tglDB)) : '';
                    ?>
                        <div class="payment-card">
                            <div class="payment-header">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="minggu[]" value="<?= $i ?>" id="cb_minggu_<?= $i ?>" <?= $isLunas ? 'checked' : '' ?> onchange="toggleDatepicker(<?= $i ?>)">
                                    <span class="checkmark"></span>
                                    <span class="payment-label">Minggu <?= $i ?></span>
                                </label>
                                <span class="status-badge <?= $isLunas ? 'paid' : '' ?>" id="badge_minggu_<?= $i ?>">
                                    <?= $isLunas ? 'LUNAS' : 'BELUM' ?>
                                </span>
                            </div>

                            <div class="payment-date" id="date_wrapper_<?= $i ?>" style="<?= !$isLunas ? 'opacity:0.3; pointer-events:none;' : '' ?>">
                                <label>Tanggal Pembayaran</label>
                                <div class="custom-datepicker-wrapper">
                                    <input type="text" id="display_date_<?= $i ?>" class="form-input date-trigger"
                                        placeholder="Pilih Tanggal Pembayaran" readonly value="<?= $tglDisplay ?>"
                                        onclick="openDatepicker(<?= $i ?>)">
                                    <input type="hidden" name="tgl_bayar_minggu_<?= $i ?>" id="real_date_<?= $i ?>" value="<?= $tglDB ?>">
                                    <span class="calendar-icon">📅</span>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="form-actions">
                    <a href="?page=report" class="btn-action btn-cancel">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Batalkan
                    </a>
                    <button type="submit" class="btn-action btn-save">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Laporan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="datepicker-overlay" id="dpOverlay" onclick="closeDatepicker()"></div>
<div class="datepicker-popup" id="dpPopup">
    <div class="bottom-sheet-handle"></div>
    <div id="dpContent"></div>
</div>

<script>
    // DATEPICKER LOGIC
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let currentTargetId = null;
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    function openDatepicker(id) {
        currentTargetId = id;
        const realInput = document.getElementById('real_date_' + id);
        let date = realInput.value ? new Date(realInput.value) : new Date();

        currentYear = date.getFullYear();
        currentMonth = date.getMonth();

        renderCalendar();

        // Positioning logic
        const displayInput = document.getElementById('display_date_' + id);
        const popup = document.getElementById('dpPopup');
        const overlay = document.getElementById('dpOverlay');

        // Reset inline styles first
        popup.style.top = '';
        popup.style.left = '';
        popup.style.transform = '';
        popup.style.position = '';

        if (window.innerWidth > 600) {
            // Desktop: Floating Popup
            const rect = displayInput.getBoundingClientRect();
            popup.style.position = 'absolute';
            popup.style.top = (window.scrollY + rect.bottom + 10) + 'px';
            popup.style.left = rect.left + 'px';
        } else {
            // Mobile: Fixed Bottom Sheet (CSS handles position but we trigger animation)
            popup.style.position = 'fixed';
            popup.style.bottom = '0';
        }

        overlay.style.display = 'block';
        setTimeout(() => popup.style.display = 'block', 10);
    }

    function closeDatepicker() {
        document.getElementById('dpOverlay').style.display = 'none';
        document.getElementById('dpPopup').style.display = 'none';
    }

    function renderCalendar() {
        const content = document.getElementById('dpContent');
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        const realInput = document.getElementById('real_date_' + currentTargetId);
        const selectedDate = realInput.value;

        let html = `
        <div class="dp-header">
            <button type="button" onclick="changeMonth(-1)"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
            <span>${monthNames[currentMonth]} ${currentYear}</span>
            <button type="button" onclick="changeMonth(1)"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
        </div>
        <div class="dp-grid">
            <div class="dp-day-name">Min</div><div class="dp-day-name">Sen</div><div class="dp-day-name">Sel</div><div class="dp-day-name">Rab</div>
            <div class="dp-day-name">Kam</div><div class="dp-day-name">Jum</div><div class="dp-day-name">Sab</div>
    `;

        for (let i = 0; i < firstDay; i++) html += `<div></div>`;

        for (let d = 1; d <= lastDate; d++) {
            let dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            let classes = 'dp-day';
            if (dateStr === selectedDate) classes += ' selected';
            if (d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) classes += ' today';
            html += `<div class="${classes}" onclick="selectDate('${dateStr}')">${d}</div>`;
        }

        html += `</div><div class="dp-footer"><button type="button" class="dp-btn-today" onclick="selectDate('${new Date().toISOString().split('T')[0]}')">Set ke Hari Ini</button></div>`;
        content.innerHTML = html;
    }

    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    }

    function selectDate(dateStr) {
        const displayInput = document.getElementById('display_date_' + currentTargetId);
        const realInput = document.getElementById('real_date_' + currentTargetId);

        realInput.value = dateStr;
        const d = new Date(dateStr);
        displayInput.value = `${String(d.getDate()).padStart(2,'0')} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;

        closeDatepicker();
    }

    // TOGGLE CHECKBOX
    function toggleDatepicker(id) {
        const checkbox = document.getElementById('cb_minggu_' + id);
        const wrapper = document.getElementById('date_wrapper_' + id);
        const badge = document.getElementById('badge_minggu_' + id);
        const realInput = document.getElementById('real_date_' + id);
        const displayInput = document.getElementById('display_date_' + id);

        if (checkbox.checked) {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
            badge.textContent = 'LUNAS';
            badge.classList.add('paid');
            if (!realInput.value) {
                selectDateForToggle(new Date().toISOString().split('T')[0], id);
            }
        } else {
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            badge.textContent = 'BELUM';
            badge.classList.remove('paid');
            realInput.value = '';
            displayInput.value = '';
        }
    }

    function selectDateForToggle(dateStr, id) {
        const displayInput = document.getElementById('display_date_' + id);
        const realInput = document.getElementById('real_date_' + id);
        realInput.value = dateStr;
        const d = new Date(dateStr);
        displayInput.value = `${String(d.getDate()).padStart(2,'0')} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
    }
</script>