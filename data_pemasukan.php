<?php
session_start();

$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg) unset($_SESSION['success_msg']);
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) unset($_SESSION['error_msg']);

$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT * FROM pemasukan ORDER BY bulan DESC";
$result = mysqli_query($koneksi, $query);
$total_query = "SELECT SUM(jumlah) AS total FROM pemasukan";
$total_result = mysqli_query($koneksi, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pemasukan = $total_row['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Pemasukan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    html, body {
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
    @media (max-width: 600px) { body::before { background-size: 400px 300px; } }
    @keyframes bgmove { 0% { background-position: 0% 50%; } 100% { background-position: 100% 50%; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .container {
      max-width: 1200px;
      margin: 32px auto 32px auto;
      background: rgba(255,255,255,0.85);
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      padding: 40px 32px 32px 32px;
      animation: fadeInUp 0.8s ease-out;
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #8e44ad;
      letter-spacing: 1px;
      animation: fadeInUp 0.8s ease-out;
      text-shadow: 0 2px 8px rgba(44,62,80,0.08);
    }
    .nav-buttons {
      margin-top: 20px;
      text-align: center;
      margin-bottom: 24px;
      animation: fadeInUp 0.8s ease-out;
      display: flex;
      justify-content: center;
      gap: 18px;
      flex-wrap: wrap;
    }
    .nav-buttons a {
      display: inline-block;
      margin: 0;
      padding: 10px 28px;
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: #fff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 700;
      border: none;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      transition: background 0.3s, color 0.3s, transform 0.2s;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      z-index: 1;
      letter-spacing: 0.5px;
    }
    .nav-buttons a:hover {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #2c3e50;
      transform: scale(1.05);
    }
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      border-radius: 16px;
      overflow: hidden;
      animation: fadeInUp 0.8s ease-out;
      background: #fff;
      box-shadow: 0 2px 12px rgba(44,62,80,0.08);
    }
    th, td {
      border: 1px solid #e0e0e0;
      padding: 13px 16px;
      text-align: center;
      font-size: 15px;
      white-space: nowrap; 
    }
    th {
      background: linear-gradient(90deg, #8e44ad, #2c3e50);
      color: #fff;
      font-size: 16px;
      letter-spacing: 0.5px;
      text-shadow: 0 1px 4px #0002;
    }
    tr:nth-child(even) { background-color: #f8f6ff; }
    tr:hover { background-color: #f1e9ff; }
    .aksi { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; }
    .aksi a { margin: 0; }
    .aksi a:hover { opacity: 0.8; transform: scale(1.05); }
    .aksi a { text-decoration: none; color: white; padding: 11px 10px; border-radius: 5px; font-size: 14px; transition: all 0.3s ease; min-width: 60px; }
    th, td { vertical-align: middle; }
    @media (max-width: 768px) { table { display: block; overflow-x: auto; white-space: nowrap; } }
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
      padding: 0 0 0 10px;
      user-select: none;
    }
    .notif-error {
      position: fixed;
      top: 80px;
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
    @keyframes notifFadeIn {
      0% { opacity: 0; transform: translateX(-50%) translateY(-40px) scale(0.95); }
      60% { opacity: 1; transform: translateX(-50%) translateY(10px) scale(1.04); }
      100% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Data Pemasukan Kas</h2>
  <div class="nav-buttons">
    <a href="index.php">Kembali</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="tambah_pemasukan.php">Tambah data</a>
    <?php endif; ?>
    <a href="data_pemasukan.php">Muat Ulang</a>
  </div>
  <?php if ($success_msg): ?>
<div id="notif-success" class="notif-success show">
  <span><?= htmlspecialchars($success_msg) ?></span>
  <button onclick="closeNotif()" aria-label="Tutup notifikasi">&times;</button>
</div>
<?php endif; ?>
<?php if ($error_msg): ?>
<div id="notif-error" class="notif-error show">
  <span><?= htmlspecialchars($error_msg) ?></span>
  <button onclick="closeNotifError()" aria-label="Tutup notifikasi">&times;</button>
</div>
<?php endif; ?>
  <div style="overflow-x: auto;">
    <table>
      <tr>
        <th>No</th>
        <th>Bulan</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <th>Aksi</th>
        <?php endif; ?>
      </tr>
      <?php
      $no = 1;
      if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . $row['bulan'] . "</td>";
        echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
        echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
          echo "<td class='aksi'>
            <a href='edit_pemasukan.php?id=" . $row['id_pemasukan'] . "' style='background:linear-gradient(90deg,#3498db,#f1c40f);'>Edit</a>
            <a href='hapus_pemasukan.php?id=" . $row['id_pemasukan'] . "' style='background:linear-gradient(90deg,#e74c3c,#f1c40f);' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>
          </td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Tidak ada data pemasukan.</td></tr>";
}

if ($result instanceof mysqli_result) {
    mysqli_free_result($result);
}
mysqli_close($koneksi);
        ?>
    </table>
  </div>
  <div style="text-align:center;margin:32px 0 0 0;font-size:1rem;font-weight:600;color:#27ae60;">
    Jumlah Dana Terkumpul: Rp <?= number_format($total_pemasukan, 0, ',', '.') ?>
  </div>
</div>
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
  window.addEventListener('DOMContentLoaded', () => {
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
</script>
</body>
</html>

