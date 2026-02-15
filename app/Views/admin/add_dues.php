<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=report');
    exit;
}

// fetch siswa
$siswaRes = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa ORDER BY nama ASC");

$error = '';
if (isset($_POST['submit'])) {
    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan'] ?? ''));

    if ($id_siswa > 0 && $bulan !== '') {
        $sql = "INSERT INTO iuran (id_siswa, bulan, minggu_1, minggu_2, minggu_3, minggu_4) VALUES ('{$id_siswa}', '{$bulan}', 'belum','belum','belum','belum')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_msg'] = 'Data iuran berhasil ditambahkan.';
            header('Location: ?page=report');
            exit();
        } else {
            $error = 'Gagal menyimpan data iuran.';
        }
    } else {
        $error = 'Pilih siswa dan isi bulan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Add Dues</title>
</head>
<body>
<div class="card">
  <h2>Tambah Data Iuran</h2>
  <?php if ($error): ?><div style="color:#e74c3c"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="POST" action="?page=add_dues">
    <label>Pilih Siswa</label>
    <select name="id_siswa" required>
      <option value="">-- Pilih --</option>
      <?php while ($row = mysqli_fetch_assoc($siswaRes)): ?>
        <option value="<?= $row['id_siswa'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
      <?php endwhile; ?>
    </select>
    <label>Bulan (contoh: Januari 2026)</label>
    <input type="text" name="bulan" required />
    <button type="submit" name="submit">Simpan</button>
  </form>
  <p style="margin-top:12px"><a href="?page=report">Kembali</a></p>
</div>
</body>
</html>
