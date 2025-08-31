<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$error_msg = '';

if (isset($_POST['submit'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak'];

    if ($nis && $nama && $kontak) {
        $query = "INSERT INTO siswa (nis, nama, kontak_orangtua) VALUES ('$nis', '$nama', '$kontak')";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success_msg'] = "Data siswa berhasil ditambahkan!";
            header("Location: data_siswa.php");
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
  <title>Tambah Data Siswa</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-attachment: fixed;
      animation: bgmove 18s ease-in-out infinite alternate;
    }
    @keyframes bgmove {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .container {
      max-width: 450px;
      margin: 60px auto;
      background: rgba(255,255,255,0.7);
      padding: 36px 40px;
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
      animation: fadeSlideUp 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards; 
      opacity: 0;
    }
    h2 {
      text-align: center;
      color: #8e44ad;
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 1px;
      text-shadow: 0 2px 8px rgba(44,62,80,0.08);
    }
    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }
    form input[type="text"] {
      width: 90%;
      padding: 12px;
      margin-bottom: 18px;
      border-radius: 10px;
      border: 1.5px solid #ccc;
      font-size: 16px;
      background: rgba(255,255,255,0.85);
      transition: border 0.3s;
    }
    form input[type="text"]:focus {
      border: 1.5px solid #8e44ad;
      outline: none;
    }
    form input[type="submit"] {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 17px;
      padding: 12px 0;
      width: 100%;
      margin-top: 8px;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      cursor: pointer;
      transition: background 0.3s, color 0.3s, transform 0.2s;
    }
    form input[type="submit"]:hover {
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: #2c3e50;
      transform: scale(1.04);
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 18px;
      text-decoration: none;
      color: #8e44ad;
      font-weight: 600;
      font-size: 15px;
      transition: color 0.3s;
    }
    .back-link:hover {
      color: #2c3e50;
      text-decoration: underline;
    }
    .error {
      color: #e74c3c;
      text-align: center;
      margin-bottom: 16px;
      font-weight: 600;
      font-size: 15px;
    }
    @media (max-width: 600px) {
      .container {
        padding: 16px 6px;
        max-width: 98vw;
      }
      h2 {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Tambah Data Siswa</h2>

  <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

  <form method="POST" action="">
    <input type="text" name="nis" placeholder="NIS/NISN" />
    <input type="text" name="nama" placeholder="Nama Siswa" />
    <input type="text" name="kontak" placeholder="Kontak Orang Tua" />
    <input type="submit" name="submit" value="Simpan Data" />
  </form>

  <a href="data_siswa.php" class="back-link">Kembali ke Data Siswa</a>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
  document.querySelector('.container').style.opacity = 1;
});

window.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.container');
  container.style.opacity = 1;
  container.style.transform = 'translateY(0)';
});
  const notif = document.getElementById("notif-success");
  if (notif) {
    notif.classList.add("show");
    setTimeout(() => {
      notif.classList.remove("show");
      notif.style.display = 'none';
      notif.innerHTML = '';
      localStorage.setItem('notifSuccessClosed', '1');
    }, 3000);
  }
});
  function closeNotif() {
    const notif = document.getElementById("notif-success");
    if (notif) {
      notif.classList.remove("show");
      notif.style.display = 'none';
      notif.innerHTML = '';
      localStorage.setItem('notifSuccessClosed', '1');
    }
  }
</script>

</body>
</html>
