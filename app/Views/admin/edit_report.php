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
        $_SESSION['success_msg'] = 'Data iuran berhasil diperbarui.';
        echo "<script>window.location='?page=report';</script>";
        exit;
    } else {
        $error = 'Gagal update data: ' . $conn->error;
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container" style="max-width: 720px; margin: 0 auto; padding-bottom: 80px;">
    
    <div style="margin-bottom: 20px;">
        <a href="?page=report" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; transition: color 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Edit Status Pembayaran</h2>
                <div class="student-info-box">
                    <span class="student-name"><?= htmlspecialchars($data['nama_siswa']) ?></span>
                    <span class="student-nis"><?= htmlspecialchars($data['nis']) ?></span>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div class="form-group">
                    <label class="form-label">Bulan Tagihan</label>
                    <input type="text" name="bulan" class="form-input" value="<?= htmlspecialchars($data['bulan']) ?>" required readonly style="background:#f1f5f9; color:#64748b; cursor:not-allowed;">
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
                                <span class="payment-label">Minggu ke-<?= $i ?></span>
                            </label>
                            <span class="status-badge <?= $isLunas ? 'paid' : '' ?>" id="badge_minggu_<?= $i ?>">
                                <?= $isLunas ? 'LUNAS' : 'BELUM' ?>
                            </span>
                        </div>
                        
                        <div class="payment-date" id="date_wrapper_<?= $i ?>" style="<?= !$isLunas ? 'opacity:0.5; pointer-events:none;' : '' ?>">
                            <label>Tanggal Bayar</label>
                            <div class="custom-datepicker-wrapper">
                                <input type="text" id="display_date_<?= $i ?>" class="form-input sm date-trigger" 
                                       placeholder="Pilih Tanggal" readonly value="<?= $tglDisplay ?>"
                                       onclick="openDatepicker(<?= $i ?>)">
                                <input type="hidden" name="tgl_bayar_minggu_<?= $i ?>" id="real_date_<?= $i ?>" value="<?= $tglDB ?>">
                                <span class="calendar-icon">📅</span>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="form-actions">
                    <a href="?page=report" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

<style>
/* Hilangkan efek gelap/blur/overlay pada datepicker/modal */
.flatpickr-calendar.open ~ .flatpickr-overlay,
.datepicker-modal,
.datepicker-backdrop,
.datepicker-overlay,
.flatpickr-overlay,
.flatpickr-calendar.open:before,
.flatpickr-calendar.open:after,
.flatpickr-calendar:before,
.flatpickr-calendar:after,
.react-datepicker__portal,
.react-datepicker__overlay,
.ui-datepicker-cover,
.ui-datepicker-bg,
.modal-backdrop,
.modal-overlay,
.picker-modal-overlay,
.picker-backdrop,
.picker-overlay {
    background: none !important;
    backdrop-filter: none !important;
    opacity: 0 !important;
    pointer-events: none !important;
    box-shadow: none !important;
    filter: none !important;
    transition: none !important;
}
    /* GLOBAL RESET */
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }

    /* CARD & LAYOUT */
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
    .card-header { padding: 24px; border-bottom: 1px solid #f1f5f9; background: #fff; }
    .card-header h2 { margin: 0 0 12px 0; font-size: 1.25rem; color: #1e293b; font-weight: 800; }
    .student-info-box { background: #f0f9ff; padding: 12px 16px; border-radius: 10px; border-left: 4px solid #0ea5e9; }
    .student-name { display: block; font-weight: 700; font-size: 1.05rem; color: #0c4a6e; }
    .student-nis { display: block; font-size: 0.9rem; color: #64748b; margin-top: 2px; }

    /* FORM */
    .form-group { margin-bottom: 24px; padding: 0 24px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.95rem; }
    .form-input { width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 1rem; background: #fff; transition: all 0.2s; }
    .form-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
    .form-input.sm { padding: 8px 12px; font-size: 0.9rem; cursor: pointer; }

    /* GRID & CHECKBOX */
    .payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 0 24px 24px 24px; }
    .payment-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; transition: all 0.2s; background: #fff; }
    .payment-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    
    .custom-checkbox { display: flex; align-items: center; cursor: pointer; user-select: none; gap: 10px; }
    .custom-checkbox input { display: none; }
    .checkmark { width: 20px; height: 20px; background-color: #fff; border: 2px solid #cbd5e1; border-radius: 6px; position: relative; transition: all 0.2s; }
    .custom-checkbox input:checked ~ .checkmark { background-color: #6366f1; border-color: #6366f1; }
    .checkmark:after { content: ""; position: absolute; display: none; left: 6px; top: 2px; width: 5px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .custom-checkbox input:checked ~ .checkmark:after { display: block; }
    .payment-label { font-weight: 700; color: #334155; font-size: 0.95rem; }

    .status-badge { font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-badge.paid { background: #dcfce7; color: #166534; border-color: #bbf7d0; }

    .custom-datepicker-wrapper { position: relative; }
    .calendar-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; opacity: 0.6; }

    /* DATEPICKER STYLES */
    .datepicker-overlay { 
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0, 0, 0, 0.5); /* Gelap biasa, NO BLUR */
        z-index: 998; 
    }
    
    .datepicker-popup { 
        display: none; position: absolute; z-index: 999; 
        background: #fff; width: 300px; padding: 16px; border-radius: 16px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
    }

    /* HEADER DATEPICKER */
    .dp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .dp-header button { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #6366f1; font-weight: bold; }
    .dp-header span { font-weight: 700; color: #1e293b; }
    
    /* GRID TANGGAL */
    .dp-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
    .dp-day-name { font-size: 0.8rem; color: #94a3b8; font-weight: 600; margin-bottom: 4px; }
    .dp-day { padding: 8px 0; border-radius: 8px; cursor: pointer; font-size: 0.9rem; color: #334155; }
    .dp-day:hover { background: #f1f5f9; color: #6366f1; }
    .dp-day.selected { background: #6366f1; color: #fff; font-weight: 700; }
    .dp-day.today { border: 1px solid #6366f1; color: #6366f1; }
    
    .dp-footer { margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; text-align: right; }
    .dp-btn-today { background: none; border: none; color: #6366f1; font-weight: 600; cursor: pointer; }

    /* MOBILE SPECIFIC: BOTTOM SHEET STYLE */
    @media (max-width: 600px) {
        .payment-grid { grid-template-columns: 1fr; }
        
        .datepicker-popup { 
            position: fixed; 
            bottom: 0; left: 0; right: 0; top: auto; /* Paksa di bawah */
            width: 100%; 
            width: 100%; /* Full width */
            border-radius: 24px 24px 0 0; /* Rounded atas saja */
            padding: 24px;
            padding-bottom: 40px; /* Safe area */
            transform: translateY(100%);
            animation: slideUp 0.3s forwards;
        }

        .bottom-sheet-handle {
            width: 40px; height: 5px; background: #e2e8f0; border-radius: 10px;
            margin: -10px auto 20px auto; /* Center handle */
        }
    }

    @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

    /* BUTTONS */
    .form-actions { display: flex; gap: 12px; padding: 24px; border-top: 1px solid #f1f5f9; background: #fcfcfc; }
    .btn { flex: 1; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center; border:none; }
    .btn-secondary { background: #f1f5f9; color: #64748b; }
    .btn-primary { background: #6366f1; color: #fff; }
    .alert-error { background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 10px; margin: 24px; display: flex; gap:10px; }
</style>

<script>
// DATEPICKER LOGIC
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
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

    if(window.innerWidth > 600) {
        // Desktop: Floating Popup
        const rect = displayInput.getBoundingClientRect();
        popup.style.position = 'absolute';
        popup.style.top = (window.scrollY + rect.bottom + 5) + 'px';
        popup.style.left = rect.left + 'px';
    } else {
        // Mobile: Fixed Bottom Sheet (CSS handles position)
        popup.style.position = 'fixed';
        popup.style.bottom = '0';
    }
    
    overlay.style.display = 'block';
    popup.style.display = 'block';
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
            <button type="button" onclick="changeMonth(-1)"><</button>
            <span>${monthNames[currentMonth]} ${currentYear}</span>
            <button type="button" onclick="changeMonth(1)">></button>
        </div>
        <div class="dp-grid">
            <div class="dp-day-name">Min</div><div class="dp-day-name">Sen</div><div class="dp-day-name">Sel</div><div class="dp-day-name">Rab</div>
            <div class="dp-day-name">Kam</div><div class="dp-day-name">Jum</div><div class="dp-day-name">Sab</div>
    `;

    for(let i=0; i<firstDay; i++) html += `<div></div>`;

    for(let d=1; d<=lastDate; d++) {
        let dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        let classes = 'dp-day';
        if(dateStr === selectedDate) classes += ' selected';
        if(d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) classes += ' today';
        html += `<div class="${classes}" onclick="selectDate('${dateStr}')">${d}</div>`;
    }
    
    html += `</div><div class="dp-footer"><button type="button" class="dp-btn-today" onclick="selectDate('${new Date().toISOString().split('T')[0]}')">Hari Ini</button></div>`;
    content.innerHTML = html;
}

function changeMonth(dir) {
    currentMonth += dir;
    if(currentMonth < 0) { currentMonth = 11; currentYear--; }
    if(currentMonth > 11) { currentMonth = 0; currentYear++; }
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
        if(!realInput.value) {
            selectDateForToggle(new Date().toISOString().split('T')[0], id);
        }
    } else {
        wrapper.style.opacity = '0.5';
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