<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Jika pemeriksaan hak akses dilakukan di controller, view cukup diasumsikan aman.

// Ambil pesan error dari session
$error = $_SESSION['error_msg'] ?? null;
$old_input = $_SESSION['old_input'] ?? []; // untuk flash old input
unset($_SESSION['error_msg'], $_SESSION['old_input']);

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Daftar bulan untuk dropdown
$months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Tahun sekarang hingga +2 tahun ke depan
$current_year = date('Y');
$years = range($current_year, $current_year + 2);
?>

<div class="container fade-in">
    <div class="form-wrapper">
        <div class="form-card">
            <div class="card-header">
                <h2>Tambah Pemasukan</h2>
                <p class="text-muted">Masukkan detail pemasukan kas kelas baru.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="?page=add_income" id="incomeForm">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Bulan & Tahun dalam satu baris -->
                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="bulan">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control" required aria-required="true">
                            <option value="" disabled selected>-- Pilih Bulan --</option>
                            <?php foreach ($months as $month): ?>
                                <option value="<?= $month ?>" <?= ($old_input['bulan'] ?? '') === $month ? 'selected' : '' ?>>
                                    <?= $month ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tahun">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control" required aria-required="true">
                            <option value="" disabled selected>-- Pilih Tahun --</option>
                            <?php foreach ($years as $year): ?>
                                <option value="<?= $year ?>" <?= ($old_input['tahun'] ?? '') == $year ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Jumlah dengan format Rupiah -->
                <div class="form-group">
                    <label class="form-label" for="jumlah">Jumlah (Rp)</label>
                    <div class="input-rupiah">
                        <span class="currency-symbol">Rp</span>
                        <input type="text" id="jumlah" name="jumlah_display" class="form-control" 
                               placeholder="0" value="<?= htmlspecialchars($old_input['jumlah'] ?? '') ?>" 
                               oninput="formatRupiah(this)" required aria-required="true">
                        <input type="hidden" name="jumlah" id="jumlah_real">
                    </div>
                    <small class="text-muted">Gunakan titik sebagai pemisah ribuan (contoh: 1.500.000)</small>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="form-control" rows="4" 
                              placeholder="Tambahkan catatan jika perlu..."><?= htmlspecialchars($old_input['keterangan'] ?? '') ?></textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="form-actions">
                    <a class="btn btn-outline" href="?page=income" id="cancelBtn">Batal</a>
                    <button type="submit" name="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner" style="display: none;"></span>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript untuk Format Rupiah, Loading, dan Konfirmasi -->
<script>
// Format Rupiah saat mengetik
function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let remainder = split[0].length % 3;
    let rupiah = split[0].substr(0, remainder);
    let thousand = split[0].substr(remainder).match(/\d{3}/gi);
    
    if (thousand) {
        let separator = remainder ? '.' : '';
        rupiah += separator + thousand.join('.');
    }
    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    input.value = rupiah;
    
    // Isi hidden input dengan angka tanpa titik
    let realValue = value.replace(/\./g, '');
    document.getElementById('jumlah_real').value = realValue;
}

// Set nilai real saat submit
document.getElementById('incomeForm').addEventListener('submit', function(e) {
    let displayInput = document.getElementById('jumlah');
    let realInput = document.getElementById('jumlah_real');
    let rawValue = displayInput.value.replace(/\./g, '').replace(/,/g, '.');
    realInput.value = rawValue;
    
    // Tampilkan loading
    let btn = document.getElementById('submitBtn');
    btn.classList.add('btn-loading');
    btn.querySelector('.spinner').style.display = 'inline-block';
    
    // Cegah double submit (tombol dinonaktifkan setelah submit)
    setTimeout(() => { btn.disabled = true; }, 10);
});

// Konfirmasi jika ada perubahan sebelum meninggalkan halaman
let formChanged = false;
document.querySelectorAll('#incomeForm input, #incomeForm select, #incomeForm textarea').forEach(field => {
    field.addEventListener('change', () => formChanged = true);
    field.addEventListener('keyup', () => formChanged = true);
});

document.getElementById('cancelBtn').addEventListener('click', function(e) {
    if (formChanged) {
        if (!confirm('Anda memiliki perubahan yang belum disimpan. Yakin ingin membatalkan?')) {
            e.preventDefault();
        }
    }
});

// Isi hidden jumlah saat halaman dimuat
window.addEventListener('load', function() {
    let displayInput = document.getElementById('jumlah');
    if (displayInput.value) {
        formatRupiah(displayInput);
    }
});
</script>