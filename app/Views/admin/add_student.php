<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=students');
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis'] ?? ''));
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));

    if ($nis !== '' && $nama !== '' && $kontak !== '') {
        $query = "INSERT INTO siswa (nis, nama, kontak_orangtua) VALUES ('{$nis}', '{$nama}', '{$kontak}')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_msg'] = "Data siswa berhasil ditambahkan!";
            header('Location: ?page=students');
            exit();
        } else {
            $error = "Gagal menambahkan data!";
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Student</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>/* reuse styles */</style>
</head>
<body>
<div class="container">
  <h2>Tambah Data Siswa</h2>
  <?php if ($error) echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>
  <form method="POST" action="?page=add_student">
    <input type="text" name="nis" placeholder="NIS/NISN" />
    <input type="text" name="nama" placeholder="Nama Siswa" />
    <input type="text" name="kontak" placeholder="Kontak Orang Tua" />
    <input type="submit" name="submit" value="Simpan Data" />
  </form>
  <a href="?page=students">Kembali ke Data Siswa</a>
</div>
</body>
</html>
