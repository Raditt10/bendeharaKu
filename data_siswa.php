<?php
session_start();

if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit;
}

$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg === 'Anda berhasil login!') {
  $success_msg = null;
  // Biarkan session success_msg tetap ada, supaya hanya index.php yang menghapus
} else if ($success_msg) {
  unset($_SESSION['success_msg']); // Hapus session setelah ditampilkan
}
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) {
  unset($_SESSION['error_msg']);
}

// Query ambil data
$query = "SELECT * FROM siswa";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Siswa</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    html, body {
      height: 100%;
      min-height: 100vh;
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      color: #222;
      overflow-x: hidden;
      background-attachment: fixed;
      animation: bgmove 18s ease-in-out infinite alternate;
      position: relative;
    }
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      z-index: 0;
      opacity: 0.18;
      pointer-events: none;
      background: url('data:image/svg+xml;utf8,<svg width="800" height="600" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg"><g opacity="0.5"><rect x="40" y="40" width="120" height="60" rx="10" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="640" y="80" width="100" height="50" rx="10" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><circle cx="200" cy="500" r="38" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><rect x="320" y="120" width="60" height="60" rx="12" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="500" y="400" width="120" height="60" rx="10" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="100" y="300" width="80" height="40" rx="8" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><rect x="600" y="500" width="60" height="60" rx="12" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="400" y="500" width="80" height="40" rx="8" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><text x="60" y="80" font-size="24" fill="%238e44ad" font-family="Poppins">📚</text><text x="660" y="110" font-size="24" fill="%23f1c40f" font-family="Poppins">✏️</text><text x="340" y="160" font-size="24" fill="%238e44ad" font-family="Poppins">📒</text><text x="520" y="440" font-size="24" fill="%23f1c40f" font-family="Poppins">🖊️</text><text x="120" y="330" font-size="24" fill="%238e44ad" font-family="Poppins">📝</text><text x="620" y="530" font-size="24" fill="%23f1c40f" font-family="Poppins">📏</text><text x="420" y="530" font-size="24" fill="%238e44ad" font-family="Poppins">📐</text><text x="180" y="510" font-size="28" fill="%23f1c40f" font-family="Poppins">🎒</text></g></svg>') center center/cover repeat;
    }
    @media (max-width: 600px) {
      body::before {
        background-size: 400px 300px;
      }
    }
    @keyframes bgmove {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeUp 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .container {
      max-width: 950px;
      margin: 50px auto;
      background: rgba(255,255,255,0.7);
      padding: 32px 36px;
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
      animation-delay: 0.2s;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #8e44ad;
      animation-delay: 0.3s;
    }

    .nav-buttons {
      text-align: center;
      margin-bottom: 20px;
      animation-delay: 0.4s;
    }

    .nav-buttons a {
      display: inline-block;
      margin: 5px 10px;
      padding: 10px 22px;
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #fff;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 16px;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      transition: background 0.3s, color 0.3s, transform 0.2s;
    }
    .nav-buttons a:hover {
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: #2c3e50;
      transform: scale(1.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 16px;
      overflow: hidden;
      background: rgba(255,255,255,0.85);
      box-shadow: 0 4px 16px rgba(44,62,80,0.08);
      animation-delay: 0.5s;
    }
    th, td {
      border: 1px solid #e0e0e0;
      padding: 14px 16px;
      text-align: center;
      font-size: 16px;
    }
    th {
      background: linear-gradient(90deg, #8e44ad, #2c3e50);
      color: #fff;
      font-weight: 700;
      letter-spacing: 1px;
    }
    tr:nth-child(even) {
      background-color: #f7f7fa;
    }
    tr:hover {
      background-color: #e0eafc;
    }

    .aksi-btn {
      padding: 7px 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      margin: 0 8px 8px 0;
      transition: background 0.3s, color 0.3s, transform 0.2s;
      box-shadow: 0 2px 8px rgba(44,62,80,0.08);
      display: inline-block;
    }
    td > .aksi-btn:last-child {
      margin-right: 0;
    }
    td {
      white-space: nowrap;
    }
    @media (max-width: 600px) {
      .container {
        padding: 12px 4px;
      }
      table th, table td {
        padding: 8px 4px;
        font-size: 13px;
      }
      .nav-buttons a {
        padding: 8px 8px;
        font-size: 13px;
      }
      .aksi-btn {
        margin: 0 4px 6px 0;
        font-size: 12px;
        padding: 6px 10px;
      }
    }
    .edit {
      background: linear-gradient(90deg, #27ae60, #2ecc71);
    }
    .hapus {
      background: linear-gradient(90deg, #c0392b, #e74c3c);
    }
    .edit:hover {
      background: linear-gradient(90deg, #2ecc71, #27ae60);
      color: #fff;
      transform: scale(1.07);
    }
    .hapus:hover {
      background: linear-gradient(90deg, #e74c3c, #c0392b);
      color: #fff;
      transform: scale(1.07);
    }
    @media (max-width: 600px) {
      .container {
        padding: 12px 4px;
      }
      table th, table td {
        padding: 8px 4px;
        font-size: 13px;
      }
      .nav-buttons a {
        padding: 8px 8px;
        font-size: 13px;
      }
    }
   .notif-success {
      position: fixed;
      top: 30px;
      left: 50%;
      transform: translateX(-50%) translateY(-40px) scale(0.95);
      background: #2ecc71;
      color: white;
      padding: 16px 32px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(46,204,113,0.18);
      font-weight: 600;
      font-size: 16px;
      display: flex;
      align-items: center;
      gap: 16px;
      min-width: 260px;
      max-width: 90vw;
      opacity: 0;
      pointer-events: none;
      z-index: 9999;
      transition: transform 0.5s cubic-bezier(.4,2,.6,1), opacity 0.5s cubic-bezier(.4,2,.6,1);
      animation: notifFadeIn 0.7s cubic-bezier(.4,2,.6,1) forwards;
    }
    @keyframes notifFadeIn {
      0% { opacity: 0; transform: translateX(-50%) translateY(-40px) scale(0.95); }
      60% { opacity: 1; transform: translateX(-50%) translateY(10px) scale(1.04); }
      100% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
    }
    
.notif-success.show {
  transform: translateX(-50%) translateY(0);
  opacity: 1;
  pointer-events: auto;
}
.notif-success button {
  background: transparent;
  border: none;
  color: white;
  font-size: 20px;
  font-weight: 700;
  cursor: pointer;
  line-height: 1;
  padding: 0;
  user-select: none;
}
    .notif-error {
      position: fixed;
      top: 25px;
      left: 50%;
      transform: translateX(-50%) translateY(-40px) scale(0.95);
      background: #e74c3c;
      color: white;
      padding: 16px 32px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(231,76,60,0.18);
      font-weight: 600;
      font-size: 16px;
      display: flex;
      align-items: center;
      gap: 16px;
      min-width: 260px;
      max-width: 90vw;
      opacity: 0;
      pointer-events: none;
      z-index: 9999;
      transition: transform 0.5s cubic-bezier(.4,2,.6,1), opacity 0.5s cubic-bezier(.4,2,.6,1);
      animation: notifFadeIn 0.7s cubic-bezier(.4,2,.6,1) forwards;
    }
    .notif-error.show {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
      pointer-events: auto;
    }
    .notif-error button {
      background: transparent;
      border: none;
      color: white;
      font-size: 20px;
      font-weight: 700;
      cursor: pointer;
      line-height: 1;
      padding: 0 0 0 10px;
      user-select: none;
    }
  </style>
</head>
<body>

<?php if ($success_msg): ?>
<div id="notif-success" class="notif-success show">
  <span><?= htmlspecialchars($success_msg) ?></span>
  <button onclick="closeNotif()" aria-label="Close notification">&times;</button>
</div>
<?php endif; ?>

<?php if ($error_msg): ?>
<div id="notif-error" class="notif-error show">
  <span><?= htmlspecialchars($error_msg) ?></span>
  <button onclick="closeNotifError()" aria-label="Tutup notifikasi">&times;</button>
</div>
<?php endif; ?>

<div class="container fade-in">
  <h2 class="fade-in">Data Siswa XI RPL 1</h2>

  <div class="nav-buttons fade-in">
    <a href="index.php">Kembali</a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="tambah_siswa.php">Tambah Data</a>
    <?php endif; ?>
    <a href="data_siswa.php">Muat Ulang</a>
  </div>

  <table class="fade-in">
    <tr>
      <th>NO</th>
      <th>NIS/NISN</th>
      <th>NAMA SISWA</th>
      <th>KONTAK ORANG TUA</th>
      <?php if ($_SESSION['role'] == 'admin'): ?>
        <th>AKSI</th>
      <?php endif; ?>
    </tr>
    <?php
    $no = 1;
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr class='fade-in'>
                    <td>".$no++."</td>
                    <td>".$row['nis']."</td>
                    <td>".$row['nama']."</td>
                    <td>".$row['kontak_orangtua']."</td>";
            if ($_SESSION['role'] == 'admin') {
                echo "<td>
                        <a class='aksi-btn edit' href='edit_siswa.php?id=".$row['id_siswa']."'>Edit</a>
                        <a class='aksi-btn hapus' href='hapus_siswa.php?id=".$row['id_siswa']."' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>
                      </td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='".($_SESSION['role'] == 'admin' ? 5 : 4)."'>Tidak ada data</td></tr>";
    }

    mysqli_close($koneksi);
    ?>
  </table>
<script>
  function closeNotif() {
    const notif = document.getElementById("notif-success");
    if (notif) {
      notif.classList.remove("show");
      notif.style.display = 'none';
      notif.innerHTML = '';
      localStorage.setItem('notifSuccessClosed', '1');
    }
  }
  function closeNotifError() {
    const notif = document.getElementById("notif-error");
    if (notif) {
      notif.classList.remove("show");
      notif.style.display = 'none';
      notif.innerHTML = '';
      localStorage.setItem('notifErrorClosed', '1');
    }
  }

// Auto-close dalam 4 detik
window.addEventListener('DOMContentLoaded', () => {
  const notif = document.getElementById("notif-success");
  if (notif) {
    notif.classList.add("show");
    setTimeout(() => {
      notif.classList.remove("show");
    }, 4000);
  }
  const notifError = document.getElementById("notif-error");
  if (notifError) {
    notifError.classList.add("show");
    setTimeout(() => {
      notifError.classList.remove("show");
      notifError.style.display = 'none';
      notifError.innerHTML = '';
      localStorage.setItem('notifErrorClosed', '1');
    }, 3000);
  }
});

window.addEventListener('DOMContentLoaded', () => {
  const notif = document.getElementById("notif-success");
  if (notif) {
    if (!localStorage.getItem('notifSuccessClosed')) {
      notif.classList.add("show");
      setTimeout(() => {
        notif.classList.remove("show");
        notif.style.display = 'none';
        notif.innerHTML = '';
        localStorage.setItem('notifSuccessClosed', '1');
      }, 3000);
    } else {
      notif.classList.remove("show");
      notif.style.display = 'none';
      notif.innerHTML = '';
    }
  }
  // Reset flag saat reload
  window.addEventListener('beforeunload', () => {
    localStorage.removeItem('notifSuccessClosed');
  });
});
</script>
</div>

</body>
</html>
