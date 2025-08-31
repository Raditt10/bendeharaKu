<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_GET['id_iuran'])) {
    $id_iuran = $_GET['id_iuran'];
    $query = "SELECT i.*, s.nama AS nama_siswa FROM iuran i
              JOIN siswa s ON i.id_siswa = s.id_siswa
              WHERE i.id_iuran = '$id_iuran'";
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
    body {
  height: 100%;
  min-height: 100vh;
  background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  background-attachment: fixed;
  animation: bgmove 18s ease-in-out infinite alternate;
}

@keyframes bgmove {
  0% { background-position: 0% 50%; }
  100% { background-position: 100% 50%; }
}

.container {
  max-width: 900px;
  margin: 60px auto;
  background: rgba(255, 255, 255, 0.85);
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(5px);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #8e44ad;
}

.data-item {
  margin-bottom: 18px;
  font-size: 17px;
}

.label {
  font-weight: 600;
  color: #2c3e50;
}

.status {
  padding: 5px 12px;
  border-radius: 6px;
  font-weight: bold;
  display: inline-block;
}

.status.lunas {
  background-color: #eafaf1;
  color: #27ae60;
  box-shadow: 0 1px 4px #27ae6022;
}

.status.belum {
  background-color: #fdecea;
  color: #e74c3c;
  box-shadow: 0 1px 4px #e74c3c22;
}

.tanggal-bayar-list {
  margin-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.tanggal-item {
  background: #fffbe6;
  color: #8e44ad;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 15px;
  width: fit-content;
  box-shadow: 0 1px 3px #f1c40f55;
}

.progress-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 6px;
}

.progress-item {
  display: flex;
  align-items: center;
  background-color: #fffbe6;
  color: #8e44ad;
  padding: 7px 14px;
  border-radius: 6px;
  font-size: 15px;
  box-shadow: 0 1px 4px #f1c40f22;
  width: fit-content;
}

.progress-item.lunas {
  background-color: #eaf6ff;
  color: #27ae60;
  font-weight: bold;
}

.progress-item.belum {
  background-color: #fffbe6;
  color: #e74c3c;
  font-weight: bold;
}

.progress-item input[type="checkbox"] {
  transform: scale(1.2);
  margin-right: 10px;
}

.back-button {
  display: block;
  margin: 30px auto 0;
  background: linear-gradient(90deg, #f1c40f, #8e44ad);
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  transition: all 0.3s ease;
  max-width: 250px;
}

.back-button:hover {
  background: linear-gradient(90deg, #8e44ad, #f1c40f);
  color: #2c3e50;
  transform: scale(1.05);
}

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

  <a href="laporan_kas.php" class="back-button">Kembali</a>
</div>

</body>
</html>
