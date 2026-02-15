<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=income');
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan'] ?? ''));
    $jumlah = (float)($_POST['jumlah'] ?? 0);
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));

    if ($bulan !== '' && $jumlah > 0) {
        $sql = "INSERT INTO pemasukan (bulan, jumlah, keterangan) VALUES ('{$bulan}', '{$jumlah}', '{$keterangan}')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_msg'] = 'Pemasukan berhasil ditambahkan.';
            header('Location: ?page=income');
            exit();
        } else {
            $error = 'Gagal menyimpan data.';
        }
    } else {
        $error = 'Bulan dan jumlah wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Add Income</title>
</head>
<body>
<div class="card">
  <h2>Tambah Pemasukan</h2>
  <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="POST" action="?page=add_income">
    <label>Bulan (contoh: Januari 2026)</label>
    <input type="text" name="bulan" required />
    <label>Jumlah (angka)</label>
    <input type="number" name="jumlah" step="0.01" required />
    <label>Keterangan</label>
    <textarea name="keterangan"></textarea>
    <button type="submit" name="submit">Simpan</button>
  </form>
  <p style="margin-top:12px"><a href="?page=income">Kembali</a></p>
</div>
</body>
</html>
