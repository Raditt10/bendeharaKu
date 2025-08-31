<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: data_pemasukan.php');
    exit;
}
$koneksi = mysqli_connect('localhost', 'root', '', 'db_bendehara');
if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    header('Location: data_pemasukan.php');
    exit;
}
$id = $_GET['id'];
$query = "SELECT * FROM pemasukan WHERE id_pemasukan = '$id'";
$result = mysqli_query($koneksi, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_msg'] = 'Data pemasukan tidak ditemukan.';
    header('Location: data_pemasukan.php');
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
        header('Location: data_pemasukan.php');
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
  <title>Edit Pemasukan</title>
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
      max-width: 600px;
      margin: 40px auto 32px auto;
      background: rgba(255,255,255,0.88);
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      padding: 36px 24px 28px 24px;
      animation: fadeInUp 0.8s ease-out;
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
      position: relative;
      z-index: 1;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #8e44ad;
      letter-spacing: 1px;
      animation: fadeInUp 0.8s ease-out;
      text-shadow: 0 2px 8px rgba(44,62,80,0.08);
    }
    form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #2c3e50;
      letter-spacing: 0.2px;
    }
    form input, form textarea {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 18px;
      border: 1.5px solid #8e44ad;
      border-radius: 10px;
      font-size: 15px;
      background: #fffbe6;
      color: #4b0082;
      box-shadow: 0 2px 8px rgba(142, 68, 173, 0.08);
      transition: all 0.3s;
    }
    form input:focus, form textarea:focus {
      outline: none;
      border-color: #f1c40f;
      background: #fff;
      color: #2c3e50;
      box-shadow: 0 0 12px #f1c40faa;
    }
    .button-submit {
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: white;
      padding: 13px 0;
      border: none;
      width: 100%;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 700;
      font-size: 17px;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      position: relative;
      overflow: hidden;
      z-index: 1;
      transition: background 0.3s, color 0.3s, transform 0.2s;
    }
    .button-submit:hover {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #2c3e50;
      transform: scale(1.03);
    }
    .button-submit:active::after {
      content: '';
      position: absolute;
      left: 50%;
      top: 50%;
      width: 200%;
      height: 200%;
      background: rgba(241,196,15,0.18);
      border-radius: 50%;
      transform: translate(-50%, -50%) scale(0.7);
      animation: ripple 0.5s linear;
      z-index: 2;
    }
    @keyframes ripple {
      to { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
    }
    .nav-link {
      display: block;
      text-align: center;
      margin-top: 22px;
      text-decoration: none;
      color: #8e44ad;
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 0.2px;
      transition: color 0.2s;
    }
    .nav-link:hover {
      color: #f1c40f;
      text-decoration: underline;
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
      opacity: 1;
      pointer-events: auto;
      z-index: 9999;
      transition: transform 0.5s cubic-bezier(.4,2,.6,1), opacity 0.5s cubic-bezier(.4,2,.6,1);
      animation: notifFadeIn 0.7s cubic-bezier(.4,2,.6,1) forwards;
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
    @media (max-width: 700px) {
      .container { padding: 18px 4vw; }
      form input, form textarea { font-size: 14px; }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Edit Data Pemasukan</h2>
  <form method="POST" action="">
    <label for="bulan">Bulan:</label>
    <input type="text" name="bulan" required value="<?= htmlspecialchars($data['bulan']) ?>">
    <label for="jumlah">Jumlah (Rp):</label>
    <input type="number" name="jumlah" required min="0" value="<?= htmlspecialchars($data['jumlah']) ?>">
    <label for="keterangan">Keterangan:</label>
    <textarea name="keterangan" rows="3" required><?= htmlspecialchars($data['keterangan']) ?></textarea>
    <button type="submit" name="submit" class="button-submit">Simpan Perubahan</button>
  </form>
  <a href="data_pemasukan.php" class="nav-link">&larr; Kembali ke Data Pemasukan</a>
</div>
</body>
</html>
