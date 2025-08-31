<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");

// Ambil id_siswa dari URL
$id_siswa = $_GET['id'];

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
      session_start();
      $_SESSION['success_msg'] = "Data siswa berhasil diupdate!";
      header("Location: data_siswa.php");
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
  <title>Edit Data Siswa</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    /* Match Edit Iuran style */
    body {
      height: 100%;
      min-height: 100vh;
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-attachment: fixed;
      animation: bgmove 18s ease-in-out infinite alternate;
      color: #2c3e50;
    }

    @keyframes bgmove {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }

    .container {
      max-width: 650px;
      margin: 60px auto;
      background: rgba(255, 255, 255, 0.9);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.12);
      backdrop-filter: blur(5px);
    }

    h2 {
      text-align: center;
      margin-bottom: 35px;
      color: #8e44ad;
      font-weight: 700;
    }

    label {
      display: block;
      margin-top: 18px;
      font-weight: 600;
      color: #3c3c3c;
      font-size: 16px;
    }

    input[type="text"] {
      width: 90%;
      padding: 12px 14px;
      margin-top: 6px;
      border-radius: 10px;
      border: 1.8px solid #a18cd1;
      font-size: 15px;
      transition: border-color 0.3s ease;
      font-family: 'Poppins', sans-serif;
      color: #4a4a4a;
    }

    input[type="text"]:focus {
      border-color: #8e44ad;
      outline: none;
      box-shadow: 0 0 8px #8e44ad55;
    }

    button {
      margin-top: 35px;
      width: 100%;
      padding: 14px 0;
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      border: none;
      border-radius: 12px;
      font-size: 18px;
      font-weight: 700;
      color: white;
      cursor: pointer;
      box-shadow: 0 5px 15px rgba(241, 196, 15, 0.4);
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }

    button:hover {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #2c3e50;
      transform: scale(1.05);
      box-shadow: 0 8px 22px rgba(141, 68, 173, 0.6);
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: #8e44ad;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Data Siswa</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" action="">
      <label for="nis">NIS/NISN</label>
      <input type="text" id="nis" name="nis" placeholder="NIS/NISN" required value="<?= htmlspecialchars($data['nis']) ?>" />

      <label for="nama">Nama Siswa</label>
      <input type="text" id="nama" name="nama" placeholder="Nama Siswa" required value="<?= htmlspecialchars($data['nama']) ?>" />

      <label for="kontak_orangtua">Kontak Orang Tua</label>
      <input type="text" id="kontak_orangtua" name="kontak_orangtua" placeholder="Kontak Orang Tua" required value="<?= htmlspecialchars($data['kontak_orangtua']) ?>" />

      <button type="submit" name="submit">Simpan Perubahan</button>
    </form>

    <a href="data_siswa.php" class="back-link">Kembali ke Data Siswa</a>
  </div>
</body>
</html>
