<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Ambil id_siswa dari URL
$id_siswa = $_GET['id'] ?? null;
if (!$id_siswa) {
    echo "ID tidak diberikan.";
    exit;
}

// Query ambil data siswa
$query = "SELECT * FROM siswa WHERE id_siswa = $id_siswa";
$result = mysqli_query($koneksi, $query);

// Cek hasil query
if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) == 0) {
    echo "Data tidak ditemukan.";
    exit();
}
$data = mysqli_fetch_assoc($result);

// Proses update saat form disubmit
if (isset($_POST['submit'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak_orangtua'];

    if ($nis && $nama && $kontak) {
    $update = "UPDATE siswa SET nis='$nis', nama='$nama', kontak_orangtua='$kontak' WHERE id_siswa=$id_siswa";
    if (mysqli_query($koneksi, $update)) {
      if (session_status() === PHP_SESSION_NONE) session_start();
      $_SESSION['success_msg'] = "Data siswa berhasil diupdate!";
      header("Location: ?page=students");
      exit();
    } else {
      $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Student</title>
</head>
<body>
  <div class="container">
    <h2>Edit Data Siswa</h2>

    <?php if (isset($error)) echo "<div class='error'>".htmlspecialchars($error)."</div>"; ?>

    <form method="POST" action="?page=edit_student&id=<?= htmlspecialchars($id_siswa) ?>">
      <label for="nis">NIS/NISN</label>
      <input type="text" id="nis" name="nis" placeholder="NIS/NISN" required value="<?= htmlspecialchars($data['nis']) ?>" />

      <label for="nama">Nama Siswa</label>
      <input type="text" id="nama" name="nama" placeholder="Nama Siswa" required value="<?= htmlspecialchars($data['nama']) ?>" />

      <label for="kontak_orangtua">Kontak Orang Tua</label>
      <input type="text" id="kontak_orangtua" name="kontak_orangtua" placeholder="Kontak Orang Tua" required value="<?= htmlspecialchars($data['kontak_orangtua']) ?>" />

      <button type="submit" name="submit">Simpan Perubahan</button>
    </form>

    <a href="?page=students" class="back-link">Kembali ke Data Siswa</a>
  </div>
</body>
</html>
