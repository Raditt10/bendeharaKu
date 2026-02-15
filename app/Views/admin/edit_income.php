<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=income');
    exit;
}
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    header('Location: ?page=income');
    exit;
}
$id = $_GET['id'];
$query = "SELECT * FROM pemasukan WHERE id_pemasukan = '$id'";
$result = mysqli_query($koneksi, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_msg'] = 'Data pemasukan tidak ditemukan.';
    header('Location: ?page=income');
    exit;
}
$data = mysqli_fetch_assoc($result);
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
        echo "<div class='notif-error show'>Gagal update data: ".htmlspecialchars(mysqli_error($koneksi))."<button onclick=\"this.parentNode.style.display='none'\">&times;</button></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Income</title>
</head>
<body>
<div class="container">
  <h2>Edit Data Pemasukan</h2>
  <form method="POST" action="?page=edit_income&id=<?= htmlspecialchars($id) ?>">
    <label for="bulan">Bulan:</label>
    <input type="text" name="bulan" required value="<?= htmlspecialchars($data['bulan']) ?>">
    <label for="jumlah">Jumlah (Rp):</label>
    <input type="number" name="jumlah" required min="0" value="<?= htmlspecialchars($data['jumlah']) ?>">
    <label for="keterangan">Keterangan:</label>
    <textarea name="keterangan" rows="3" required><?= htmlspecialchars($data['keterangan']) ?></textarea>
    <button type="submit" name="submit" class="button-submit">Simpan Perubahan</button>
  </form>
  <a href="?page=income" class="nav-link">&larr; Kembali ke Data Pemasukan</a>
</div>
</body>
</html>
