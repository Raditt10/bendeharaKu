<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

if (isset($_GET['id_iuran'])) {
    $id_iuran = $_GET['id_iuran'];
    $query = "SELECT i.*, s.nama AS nama_siswa FROM iuran i
              JOIN siswa s ON i.id_siswa = s.id_siswa
              WHERE i.id_iuran = '" . $id_iuran . "'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    echo "ID iuran tidak diberikan.";
    exit;
}

mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Iuran</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    /* styles copied from original */
  </style>
</head>
<body>

<div class="container">
  <h2>Detail Iuran</h2>

  <div class="data-item">
    <span class="label">ID Iuran:</span> <?= $data['id_iuran'] ?>
  </div>
  <div class="data-item">
    <span class="label">Bulan:</span> <?= $data['bulan'] ?>
  </div>
  <div class="data-item">
    <span class="label">Nama Siswa:</span> <?= $data['nama_siswa'] ?>
  </div>
  <div class="data-item">
    <span class="label">Status:</span> 
    <span class="status <?= $data['status'] == 'lunas' ? 'lunas' : 'belum' ?>">
      <?= ucfirst($data['status']) ?>
    </span>
  </div>

  <div class="data-item">
    <span class="label">Tanggal Bayar:</span> 
    <div class="tanggal-bayar-list">
    <?php
      $ada = false;
      for ($i = 1; $i <= 4; $i++) {
          $tanggal = $data["tgl_bayar_minggu_$i"];
          if (!empty($tanggal)) {
              $ada = true;
              echo "<div class='tanggal-item'>Minggu $i: <strong>$tanggal</strong></div>";
          }
      }
      if (!$ada) echo '<div class="tanggal-item">Belum ada pembayaran</div>';
    ?>
    </div>
  </div>

  <div class="data-item">
    <span class="label">Progress Mingguan:</span>
    <div class="checkbox-group progress-list">
      <?php for ($i = 1; $i <= 4; $i++): 
        $status = $data["minggu_$i"];
        $tanggal = $data["tgl_bayar_minggu_$i"];
        $isLunas = $status === 'lunas';
      ?>
        <div class="progress-item <?= $isLunas ? 'lunas' : 'belum' ?>">
          <input type="checkbox" disabled <?= $isLunas ? 'checked' : '' ?>>
          <span>Minggu <?= $i ?><?= $isLunas && $tanggal ? " (<strong>$tanggal</strong>)" : '' ?></span>
        </div>
      <?php endfor; ?>
    </div>
  </div>

  <a href="?page=report" class="back-button">Kembali</a>
</div>

</body>
</html>
