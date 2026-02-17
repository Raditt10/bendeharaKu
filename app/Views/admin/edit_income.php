<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);

// 1. Validasi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=income');
    exit;
}

require_once $basePath . '/config/config.php';
require_once $basePath . '/app/Models/Database.php';

$koneksi = Database::getInstance()->getConnection();

// 2. Cek ID di URL
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    header('Location: ?page=income');
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM pemasukan WHERE id_pemasukan = '$id'";
$result = mysqli_query($koneksi, $query);

// 3. Cek Data Ada/Tidak
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_msg'] = 'Data pemasukan tidak ditemukan.';
    header('Location: ?page=income');
    exit;
}

$data = mysqli_fetch_assoc($result);

// 4. Proses Update Data
if (isset($_POST['submit'])) {
    $bulan = $_POST['bulan'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $update = "UPDATE pemasukan SET bulan='$bulan', jumlah='$jumlah', keterangan='$keterangan' WHERE id_pemasukan='$id'";
    
    if (mysqli_query($koneksi, $update)) {
        $_SESSION['success_msg'] = 'Data pemasukan berhasil diupdate!';
        header('Location: ?page=income');
        exit;
    } else {
        echo "<script>alert('Gagal update: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
}

// --- INCLUDE HEADER ---
// Pastikan path ini sesuai dengan struktur foldermu
include_once __DIR__ . '/../partials/header.php';
?>

<style>
    /* Menggunakan class .edit-container agar tidak bentrok dengan .container di header */
    .edit-container {
        max-width: 600px;
        margin: 48px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 40px 28px 32px 28px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .edit-container h2 {
        margin-bottom: 18px;
        color: #2d3748;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -1px;
    }
    .section {
        width: 100%;
        margin-bottom: 28px;
        background: #f8fafc;
        border-radius: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.05);
        padding: 24px 18px;
        box-sizing: border-box;
    }
    .section-title {
        font-weight: 600;
        color: #3b4252;
        margin-bottom: 14px;
        font-size: 1.25rem;
        text-align: center;
    }
    .data-list {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 0;
        border: 1px solid #e2e8f0;
        font-size: 1rem;
    }
    .data-list dt { font-weight: 500; color: #2d3748; margin-bottom: 2px; }
    .data-list dd { margin: 0 0 10px 0; color: #47525e; font-size: 1.05rem; }
    
    .form-group { margin-bottom: 18px; width: 100%; }
    .form-group label { display: block; font-weight: 500; color: #2d3748; margin-bottom: 6px; font-size: 1rem; }
    
    .form-group input, 
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 1rem;
        background: #f9fafb;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    .form-group input:focus, 
    .form-group textarea:focus { border-color: #3182ce; outline: none; background: #fff; }
    
    .button-submit {
        background: #3182ce;
        color: #fff;
        border: none;
        padding: 12px 32px;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
        box-shadow: 0 2px 8px rgba(49, 130, 206, 0.2);
        width: 100%;
    }
    .button-submit:hover { background: #2563eb; }

    .nav-link-back {
        display: inline-block;
        margin-top: 18px;
        color: #3182ce;
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
    }
    .nav-link-back:hover { text-decoration: underline; }
    
    /* Responsive Mobile */
    @media (max-width: 768px) {
        .edit-container { max-width: 90vw; padding: 24px 4vw; margin-top: 20px; }
    }
</style>

<div class="edit-container">
    <h2>Edit Data Pemasukan</h2>

    <div class="section">
        <div class="section-title">Data Sebelumnya</div>
        <dl class="data-list">
            <dt>Bulan:</dt>
            <dd><?= htmlspecialchars($data['bulan']) ?></dd>
            <dt>Jumlah (Rp):</dt>
            <dd><?= number_format($data['jumlah'], 0, ',', '.') ?></dd>
            <dt>Keterangan:</dt>
            <dd><?= htmlspecialchars($data['keterangan']) ?></dd>
        </dl>
    </div>

    <div class="section">
        <div class="section-title">Input Data Baru</div>
        <form method="POST" action="?page=edit_income&id=<?= htmlspecialchars($id) ?>">
            <div class="form-group">
                <label for="bulan">Bulan:</label>
                <input type="text" name="bulan" id="bulan" required value="<?= htmlspecialchars($data['bulan']) ?>">
            </div>
            
            <div class="form-group">
                <label for="jumlah">Jumlah (Rp):</label>
                <input type="number" name="jumlah" id="jumlah" required min="0" value="<?= htmlspecialchars($data['jumlah']) ?>">
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan:</label>
                <textarea name="keterangan" id="keterangan" rows="3" required><?= htmlspecialchars($data['keterangan']) ?></textarea>
            </div>
            
            <div style="text-align: right;">
                <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php 
// --- INCLUDE FOOTER ---
// Pastikan path ini sesuai
include_once __DIR__ . '/../partials/footer.php'; 
?>