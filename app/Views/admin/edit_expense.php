<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=expenses');
    exit;
}
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();
if (!isset($_GET['id_pengeluaran'])) {
    $_SESSION['error_msg'] = 'ID pengeluaran tidak ditemukan.';
    header('Location: ?page=expenses');
    exit;
}
$id = $_GET['id_pengeluaran'];
$query = "SELECT * FROM pengeluaran WHERE id_pengeluaran = '$id'";
$result = mysqli_query($koneksi, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_msg'] = 'Data pengeluaran tidak ditemukan.';
    header('Location: ?page=expenses');
    exit;
}
$data = mysqli_fetch_assoc($result);
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $update = "UPDATE pengeluaran SET tanggal='$tanggal', jumlah='$jumlah', keterangan='$keterangan' WHERE id_pengeluaran='$id'";
    if (mysqli_query($koneksi, $update)) {
        $_SESSION['success_msg'] = 'Data pengeluaran berhasil diupdate!';
        header('Location: ?page=expenses');
        exit;
    } else {
        echo "<div class='notif-error show'>Gagal update data: ".htmlspecialchars(mysqli_error($koneksi))."<button onclick=\"this.parentNode.style.display='none'\">&times;</button></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Expense</title>
</head>
<body>
<div class="container">
  <h2>Edit Data Pengeluaran</h2>
  <form method="POST" action="?page=edit_expense&id_pengeluaran=<?= htmlspecialchars($id) ?>">
    <label for="bulan">Bulan:</label>
    <input type="date" name="tanggal" required value="<?= htmlspecialchars($data['tanggal']) ?>">
    <label for="jumlah">Jumlah (Rp):</label>
    <input type="number" name="jumlah" required min="0" value="<?= htmlspecialchars($data['jumlah']) ?>">
    <label for="keterangan">Keterangan:</label>
    <textarea name="keterangan" rows="3" required><?= htmlspecialchars($data['keterangan']) ?></textarea>
    <button type="submit" name="submit" class="button-submit">Simpan Perubahan</button>
  </form>
  <a href="?page=expenses" class="nav-link">&larr; Kembali ke Data Pengeluaran</a>
</div>
</body>
</html>
