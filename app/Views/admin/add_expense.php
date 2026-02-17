<?php
// --- LOGIKA PHP (BACKEND) ---
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// 1. Cek Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=expenses';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// 2. Proses Simpan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data & Sanitasi
    $tanggal = mysqli_real_escape_string($conn, trim($_POST['tanggal'] ?? ''));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $jumlah = mysqli_real_escape_string($conn, str_replace('.', '', $_POST['jumlah'] ?? '0')); // Hapus titik ribuan
    $bukti_foto = null;

    // Handle upload file
    if (isset($_FILES['bukti_struk']) && $_FILES['bukti_struk']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['bukti_struk'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];
        
        if (in_array($ext, $allowed) && $file['size'] <= 5*1024*1024) {
            $newName = 'struk_' . date('YmdHis') . '_' . rand(1000,9999) . '.' . $ext;
            $uploadDir = dirname(__DIR__, 3) . '/public/uploads/';
            
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                $bukti_foto = $newName;
            }
        }
    }

    // Validasi & Insert
    if (empty($tanggal) || $jumlah <= 0) {
        $error = 'Tanggal wajib diisi dan Nominal harus lebih dari 0.';
    } else {
        $tahun = date('Y', strtotime($tanggal));
        $sql = "INSERT INTO pengeluaran (tanggal, tahun, jumlah, keterangan, bukti_foto) VALUES ('$tanggal', '$tahun', '$jumlah', '$keterangan', " . ($bukti_foto ? "'$bukti_foto'" : "NULL") . ")";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_msg'] = 'Data pengeluaran berhasil disimpan.';
            echo "<script>window.location='?page=expenses';</script>";
            exit;
        } else {
            $error = 'Terjadi kesalahan sistem: ' . mysqli_error($conn);
        }
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="container" style="max-width: 600px; margin: 0 auto; padding-bottom: 80px;">
    
    <div style="margin-bottom: 20px;">
        <a href="?page=expenses" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; transition: color 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Catat Pengeluaran</h2>
                <p>Input transaksi uang keluar kas kelas.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="formExpense" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label class="form-label">Tanggal Transaksi</label>
                    <div class="custom-datepicker" id="customDatepicker">
                        <input type="text" id="tanggal_display" class="form-input" placeholder="Pilih tanggal" readonly required autocomplete="off">
                        <input type="hidden" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                        
                        <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); pointer-events:none; color:#ef4444;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>

                        <div class="datepicker-popup" id="datepickerPopup" style="display:none;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal Keluar (Rp)</label>
                    <div class="input-group-rupiah">
                        <span class="prefix">Rp</span>
                        <input type="text" name="jumlah" id="jumlah_display" class="form-input input-no-border" placeholder="0" autocomplete="off" required style="font-size: 1.2rem; font-weight: 700; color: #ef4444;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Keperluan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="form-input" placeholder="Contoh: Beli spidol, Fotocopy materi..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Bukti Struk <span style="font-weight:400; color:#94a3b8; font-size:0.85em;">(Opsional)</span></label>
                    <div id="customUploadBox" class="custom-upload-box" onclick="document.getElementById('bukti_struk').click();">
                        <div id="uploadPlaceholder">
                            <div class="upload-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                            <span style="font-weight:600; color:#1e293b;">Ketuk untuk upload foto</span>
                            <span style="display:block; font-size:0.85rem; color:#94a3b8; margin-top:4px;">JPG/PNG, Max 5MB</span>
                        </div>
                        <img id="previewImg" src="#" alt="Preview" class="img-preview" />
                    </div>
                    <input type="file" name="bukti_struk" id="bukti_struk" accept="image/jpeg,image/png" style="display:none;" onchange="previewStruk(event)">
                    
                    <button type="button" id="removeImgBtn" style="display:none; margin-top:8px; border:none; background:none; color:#ef4444; font-size:0.9rem; cursor:pointer;" onclick="removeImage()">
                        &times; Hapus Foto
                    </button>
                </div>

                <div class="form-actions">
                    <a href="?page=expenses" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="submit" class="btn btn-danger">
                        Simpan Pengeluaran
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="datepicker-overlay" id="datepickerOverlay" style="display:none;"></div>

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

    .form-input {
        width: 100%; padding: 14px 16px;
        border: 1px solid #cbd5e1; border-radius: 12px;
        font-size: 1rem; color: #0f172a; background: #fff;
        transition: all 0.2s; font-family: inherit;
    }
    .form-input:focus { border-color: #ef4444; outline: none; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1); }
    
    /* Input Rupiah (Red Theme) */
    .input-group-rupiah {
        display: flex; align-items: center;
        border: 1px solid #cbd5e1; border-radius: 12px;
        background: #fff; overflow: hidden;
        transition: all 0.2s;
    }
    .input-group-rupiah:focus-within { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1); }
    .input-group-rupiah .prefix {
        background: #fef2f2; color: #ef4444; font-weight: 700;
        padding: 14px 16px; border-right: 1px solid #fee2e2;
    }
    .input-group-rupiah .input-no-border { border: none; box-shadow: none; background: transparent; }

    /* Upload Box */
    .custom-upload-box {
        border: 2px dashed #cbd5e1; background: #f8fafc;
        border-radius: 12px; padding: 30px;
        text-align: center; cursor: pointer;
        transition: all 0.2s; position: relative;
    }
    .custom-upload-box:hover { border-color: #ef4444; background: #fef2f2; }
    .upload-icon {
        width: 48px; height: 48px; background: #fee2e2;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px auto; color: #ef4444;
    }
    .img-preview { display: none; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

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
    
    /* Red Button for Expense */
    .btn-danger { background: #ef4444; color: #fff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }
    .btn-danger:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(239, 68, 68, 0.35); }

    /* Alert */
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
        padding: 12px 16px; border-radius: 10px; margin: 24px;
        display: flex; align-items: center; gap: 10px; font-size: 0.9rem;
    }

    /* --- DATEPICKER STYLES (RED THEME) --- */
    .custom-datepicker { position: relative; width: 100%; }
    #tanggal_display { cursor: pointer; background: #fff; padding-right: 40px; }
    
    .datepicker-popup {
        position: absolute; top: 110%; left: 0; z-index: 20;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
        box-shadow: 0 10px 40px -5px rgba(0,0,0,0.15);
        padding: 20px; width: 320px;
        animation: fadeInDropdown 0.2s; display: none;
    }
    .datepicker-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .datepicker-header button { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; color: #334155; cursor: pointer; font-weight: bold; }
    .datepicker-header button:hover { background: #e2e8f0; }
    .datepicker-header span { font-weight: 700; color: #1e293b; font-size: 1rem; }
    
    .datepicker-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
    .datepicker-weekday { text-align: center; font-size: 0.8rem; font-weight: 600; color: #94a3b8; margin-bottom: 4px; }
    .datepicker-day { 
        text-align: center; height: 36px; display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; border-radius: 8px; cursor: pointer; color: #334155; transition: all 0.15s;
    }
    .datepicker-day:hover { background: #fef2f2; color: #ef4444; }
    .datepicker-day.today { border: 1px solid #ef4444; color: #ef4444; font-weight: 600; }
    .datepicker-day.selected { background: #ef4444; color: #fff; font-weight: 700; box-shadow: 0 4px 10px rgba(239,68,68,0.3); border: none; }
    .datepicker-day.disabled { color: #e2e8f0; pointer-events: none; }
    
    .datepicker-footer { display: flex; justify-content: space-between; margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9; }
    .datepicker-footer button { background: none; border: none; color: #ef4444; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
    
    .datepicker-overlay { display: none; position: fixed; z-index: 19; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); }

    /* MOBILE DATEPICKER (BOTTOM SHEET) */
    @media (max-width: 600px) {
        .datepicker-popup {
            position: fixed; left: 0; right: 0; bottom: 0; top: auto;
            width: 100%; border-radius: 24px 24px 0 0;
            padding: 24px; animation: slideUpSheet 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .datepicker-overlay { display: block; }
    }
    
    @keyframes fadeInDropdown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUpSheet { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>

<script>
// 1. DATEPICKER LOGIC (REVISED FOR RED THEME)
document.addEventListener('DOMContentLoaded', function() {
    const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const input = document.getElementById('tanggal_display');
    const hidden = document.getElementById('tanggal');
    const popup = document.getElementById('datepickerPopup');
    const overlay = document.getElementById('datepickerOverlay');
    
    let selected = hidden.value ? new Date(hidden.value) : new Date();
    let viewMonth = selected.getMonth();
    let viewYear = selected.getFullYear();

    function pad(n) { return n < 10 ? '0'+n : n; }
    function formatDate(d) { return pad(d.getDate())+' '+monthNames[d.getMonth()]+' '+d.getFullYear(); }
    function formatValue(d) { return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }

    function render() {
        let d = new Date(viewYear, viewMonth, 1);
        let firstDay = d.getDay();
        let lastDate = new Date(viewYear, viewMonth+1, 0).getDate();
        let today = new Date();
        
        let html = `
            <div class="datepicker-header">
                <button type="button" id="prevMonth"><</button>
                <span>${monthNames[viewMonth]} ${viewYear}</span>
                <button type="button" id="nextMonth">></button>
            </div>
            <div class="datepicker-grid">`;
            
        for(let i=0;i<7;i++) html += `<div class="datepicker-weekday">${dayNames[i]}</div>`;
        
        let day = 1;
        // 42 cells grid (6 rows)
        for(let i=0;i<42;i++) {
            if(i < firstDay || day > lastDate) {
                html += '<div class="datepicker-day disabled"></div>';
            } else {
                let dateObj = new Date(viewYear, viewMonth, day);
                let isToday = dateObj.toDateString() === today.toDateString();
                let isSelected = selected && dateObj.toDateString() === selected.toDateString();
                
                let classes = 'datepicker-day';
                if(isToday) classes += ' today';
                if(isSelected) classes += ' selected';
                
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
        document.getElementById('prevMonth').onclick = (e) => { e.stopPropagation(); viewMonth--; if(viewMonth<0){viewMonth=11;viewYear--;} render(); };
        document.getElementById('nextMonth').onclick = (e) => { e.stopPropagation(); viewMonth++; if(viewMonth>11){viewMonth=0;viewYear++;} render(); };
        document.getElementById('todayDate').onclick = () => { let d=new Date(); updateDate(d); };
        
        popup.querySelectorAll('.datepicker-day:not(.disabled)').forEach(el => {
            el.onclick = function() {
                let parts = this.getAttribute('data-date').split('-');
                let d = new Date(parts[0], parseInt(parts[1])-1, parts[2]);
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
        if(window.innerWidth <= 600) overlay.style.display = 'block';
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
    if(hidden.value) {
        let parts = hidden.value.split('-');
        let d = new Date(parts[0], parseInt(parts[1])-1, parts[2]);
        input.value = formatDate(d);
    }
});

// 2. FORMAT RUPIAH RIBUAN
const rupiahInput = document.getElementById('jumlah_display');
if(rupiahInput){
    rupiahInput.addEventListener('keyup', function(e){
        let val = this.value.replace(/[^0-9]/g, '');
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
}

// 3. PREVIEW GAMBAR
function previewStruk(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const removeBtn = document.getElementById('removeImgBtn');
    
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB!');
            removeImage();
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

function removeImage() {
    const input = document.getElementById('bukti_struk');
    const preview = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const removeBtn = document.getElementById('removeImgBtn');
    
    input.value = '';
    preview.src = '#';
    preview.style.display = 'none';
    placeholder.style.display = 'block';
    removeBtn.style.display = 'none';
}
</script>