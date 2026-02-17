<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../Models/Database.php';

// Validasi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=income');
    exit;
}

$error = $_SESSION['error_msg'] ?? null;
unset($_SESSION['error_msg']);

// Data Bulan
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$current_year = date('Y');

include_once __DIR__ . '/../partials/header.php';
?>

<div class="container">
    
    <?php 
        $back_href = '?page=income'; 
        $back_label = 'Kembali';
        include __DIR__ . '/../partials/back_button.php'; 
    ?>

    <div class="form-wrapper">
        <div class="card">
            <div class="card-header">
                <h2>Tambah Pemasukan</h2>
                <p class="text-muted">Catat transaksi pemasukan kas baru ke dalam sistem.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="?page=add_income" id="formIncome">
                
                <div class="form-row">
                    <div class="form-group col-half">
                        <label class="form-label" for="bulan_custom">Bulan</label>
                        <div class="custom-dropdown" id="bulanDropdown">
                            <div class="custom-dropdown-selected" id="bulanDropdownSelected">
                                Pilih Bulan
                                <span class="custom-dropdown-arrow">&#9662;</span>
                            </div>
                            <div class="custom-dropdown-list" id="bulanDropdownList" style="display:none;">
                                <?php foreach ($months as $month): ?>
                                    <div class="custom-dropdown-item" data-value="<?= $month ?>"><?= $month ?></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="bulan" id="bulanDropdownInput" value="">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="jumlah_display">Nominal (Rp)</label>
                    <div class="input-group input-group-inline">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="jumlah_display" class="form-control" placeholder="0" autocomplete="off" required style="flex:1;">
                        <input type="hidden" name="jumlah" id="jumlah_real">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Contoh: Iuran Wajib Januari..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="?page=income" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    /* CSS FORM INCOME SAJA (CSS Back Button sudah pindah) */
    
    /* Custom Dropdown Styling */
    .custom-dropdown {
        position: relative;
        min-width: 110px;
        user-select: none;
        width: 100%;
    }
    .custom-dropdown-selected {
        font-weight: 600;
        color: #2563eb;
        background: #fff;
        border: 2px solid #2563eb;
        border-radius: 8px;
        padding: 10px 36px 10px 16px;
        font-size: 1.08rem;
        cursor: pointer;
        transition: border-color 0.22s, box-shadow 0.22s;
        box-shadow: 0 2px 8px rgba(37,99,235,0.07);
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        box-sizing: border-box;
    }
    .custom-dropdown.open .custom-dropdown-selected,
    .custom-dropdown-selected:hover {
        border-color: #1d4ed8;
        background: #f3f6fd;
        box-shadow: 0 4px 16px rgba(37,99,235,0.13);
    }
    .custom-dropdown-arrow {
        margin-left: 10px;
        font-size: 1.1em;
        color: #2563eb;
        pointer-events: none;
    }
    .custom-dropdown-list {
        position: absolute;
        top: 110%;
        left: 0;
        right: 0;
        background: #fff;
        border: 2px solid #2563eb;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 8px 32px rgba(37,99,235,0.13);
        z-index: 10;
        max-height: 220px;
        overflow-y: auto;
        animation: fadeInDropdown 0.18s;
    }
    @keyframes fadeInDropdown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-dropdown-item {
        padding: 10px 18px;
        font-size: 1.05rem;
        color: #2563eb;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }
    .custom-dropdown-item.selected,
    .custom-dropdown-item:hover {
        background: #2563eb;
        color: #fff;
    }

    /* Input Nominal Styling */
    .input-group-inline {
        display: flex;
        align-items: center;
        gap: 0;
        background: #f9fafb;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        box-shadow: 0 1px 2px rgba(37,99,235,0.04);
        padding: 0;
        overflow: hidden;
        margin-top: 2px;
        width: 100%;
    }
    .input-group-inline .input-group-text {
        background: #f3f6fd;
        color: #2563eb;
        font-weight: 600;
        font-size: 1.08rem;
        border: none;
        padding: 0 16px;
        height: 44px;
        display: flex;
        align-items: center;
        border-radius: 8px 0 0 8px;
    }
    .input-group-inline .form-control {
        border: none;
        background: transparent;
        font-size: 1.08rem;
        height: 44px;
        padding: 0 12px;
        outline: none;
        box-shadow: none;
        border-radius: 0 8px 8px 0;
        width: 100%;
        flex: 1;
    }
    .input-group-inline .form-control:focus {
        background: #fff;
    }
    
    /* Textarea */
    .form-group textarea.form-control {
        width: 100%;
        min-height: 80px;
        font-size: 1rem;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        background: #f9fafb;
        box-shadow: 0 1px 2px rgba(37,99,235,0.04);
        resize: vertical;
        transition: border-color 0.2s;
        box-sizing: border-box;
        display: block;
    }
    .form-group textarea.form-control:focus {
        border-color: #2563eb;
        background: #fff;
    }
</style>

<script>
// --- LOGIC CUSTOM DROPDOWN ---
const bulanDropdown = document.getElementById('bulanDropdown');
const bulanSelected = document.getElementById('bulanDropdownSelected');
const bulanList = document.getElementById('bulanDropdownList');
const bulanInput = document.getElementById('bulanDropdownInput');

bulanSelected.onclick = function(e) {
    e.stopPropagation();
    bulanList.style.display = bulanList.style.display === 'block' ? 'none' : 'block';
    bulanDropdown.classList.toggle('open');
};

document.addEventListener('click', function() {
    bulanList.style.display = 'none';
    bulanDropdown.classList.remove('open');
});

bulanList.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
    item.onclick = function(e) {
        e.stopPropagation();
        let val = this.dataset.value;
        bulanInput.value = val;
        bulanSelected.innerHTML = val + ' <span class="custom-dropdown-arrow">&#9662;</span>';
        bulanList.style.display = 'none';
        bulanDropdown.classList.remove('open');
        
        bulanList.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('selected'));
        this.classList.add('selected');
    };
});

// --- LOGIC FORMAT RUPIAH & VALIDASI ---
const displayInput = document.getElementById('jumlah_display');
const realInput = document.getElementById('jumlah_real');
const form = document.getElementById('formIncome');

displayInput.addEventListener('keyup', function(e) {
    let val = this.value.replace(/[^0-9]/g, '');
    realInput.value = val;
    this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
});

form.onsubmit = function(e) {
    // Validasi Bulan
    if (!bulanInput.value) {
        e.preventDefault();
        bulanDropdown.classList.add('open');
        bulanList.style.display = 'block';
        alert("Silakan pilih Bulan terlebih dahulu.");
        return false;
    }
    
    // Validasi Nominal
    if(!realInput.value || realInput.value == 0) {
        e.preventDefault();
        alert("Mohon masukkan nominal yang valid.");
        displayInput.focus();
        return false;
    }
    return true;
};
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>