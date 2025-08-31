<?php
session_start();
$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg) unset($_SESSION['success_msg']);
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) unset($_SESSION['error_msg']);

if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit;
}

$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$bulanQuery = "SELECT DISTINCT bulan FROM iuran ORDER BY bulan ASC";
$bulanResult = mysqli_query($koneksi, $bulanQuery);

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$bulan_filter = isset($_GET['bulan']) ? $_GET['bulan'] : '';

$query = "SELECT i.id_iuran, i.bulan, s.nama AS nama,
                 i.minggu_1, i.tgl_bayar_minggu_1,
                 i.minggu_2, i.tgl_bayar_minggu_2,
                 i.minggu_3, i.tgl_bayar_minggu_3,
                 i.minggu_4, i.tgl_bayar_minggu_4
          FROM iuran i
          JOIN siswa s ON i.id_siswa = s.id_siswa";
 

$filters = [];

if (!empty($status_filter)) {
    $filters[] = "(i.minggu_1 = '$status_filter' OR i.minggu_2 = '$status_filter' OR i.minggu_3 = '$status_filter' OR i.minggu_4 = '$status_filter')";
}

if (!empty($bulan_filter)) {
    $filters[] = "i.bulan = '$bulan_filter'";
}

$nama_siswa_filter = isset($_GET['nama']) ? trim($_GET['nama']) : ''; 

if (!empty($nama_siswa_filter)) {
    $filters[] = "s.nama LIKE '%$nama_siswa_filter%'";
}

if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

$query .= " ORDER BY i.bulan ASC, s.nama ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Kas</title>
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
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .container {
      max-width: 1500px;
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
      margin-top: 35px;
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
    .filter-form {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      margin-bottom: 25px;
      gap: 15px;
      animation: fadeInUp 0.8s ease-out;
    }
    .filter-form select, .filter-form button {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }
    .filter-form button {
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: #fff;
      border: none;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      position: relative;
      overflow: hidden;
      z-index: 1;
      letter-spacing: 0.5px;
    }
    .filter-form button:hover {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #2c3e50;
      transform: scale(1.05);
    }

    .filter-form input[type="text"] {
  padding: 10px 16px;
  border-radius: 12px;
  border: 1.8px solid #8e44ad;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  color: #4b0082;
  box-shadow: 0 4px 10px rgba(142, 68, 173, 0.1);
  transition: all 0.3s ease;
  min-width: 280px;
  flex-grow: 1;
  background-color: #fffbe6;
  max-width: 180px;
}

.filter-form input[type="text"]:focus {
  outline: none;
  border-color: #f1c40f;
  box-shadow: 0 0 12px #f1c40faa;
  background-color: #fff;
  color: #2c3e50;
}

.reset-btn {
  padding: 8px 12px;
  border-radius: 8px;
  border: none;
  background: linear-gradient(90deg, #8e44ad, #f1c40f);
  color: white;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  text-decoration: none;
  box-shadow: 0 2px 8px rgba(44,62,80,0.10);
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}

.reset-btn:hover {
  background: linear-gradient(90deg, #f1c40f, #8e44ad);
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
    tr:nth-child(even) {
      background-color: #f8f6ff;
    }
    tr:hover {
      background-color: #f1e9ff;
    }
    .lunas {
      color: #27ae60;
      font-weight: bold;
      background: #fffbe6;
      border-radius: 6px;
      box-shadow: 0 1px 4px #27ae6022;
    }
    .belum {
      color: #e74c3c;
      font-weight: bold;
      background: #eaf6ff;
      border-radius: 6px;
      box-shadow: 0 1px 4px #e74c3c22;
    }

    .aksi {
     display: flex;
     justify-content: center;
     flex-wrap: wrap;
     gap: 8px;
    }

    .aksi a {
     margin: 0;
   }
    .aksi a:hover {
      opacity: 0.8;
      transform: scale(1.05);
    }
    .aksi a {
      text-decoration: none;
      color: white;
      padding: 11px 10px; /* Ukuran lebih ramping */
      border-radius: 5px;
      font-size: 14px;
      transition: all 0.3s ease;
      min-width: 60px; /* Supaya sejajar */
    }

    th, td {
  vertical-align: middle;
}


    td.aksi, th:last-child {
  min-width: 200px;     
  white-space: nowrap;  
}

    
  .aksi .detail { background: linear-gradient(90deg, #f1c40f, #8e44ad); }
  .aksi .edit { background: linear-gradient(90deg, #3498db, #f1c40f); }
  .aksi .hapus { background: linear-gradient(90deg, #e74c3c, #f1c40f); }
    .aksi a:hover {
      opacity: 0.8;
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
  .notif-success {
    position: fixed;
    top: 0;
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
    top: 0;
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
  <h2>Laporan Kas XI RPL 1</h2>

  <div class="nav-buttons">
    <a href="index.php">Kembali</a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="tambah_data.php">Tambah Data</a>
    <?php endif; ?>
    <a href="laporan_kas.php">Muat Ulang</a>
  </div>

  <form method="GET" class="filter-form">
    <select name="status">
      <option value="">Filter Status</option>
      <option value="lunas" <?= ($status_filter == 'lunas') ? 'selected' : '' ?>>Lunas</option>
      <option value="belum" <?= ($status_filter == 'belum') ? 'selected' : '' ?>>Belum</option>
    </select>
   
    <select name="bulan">
      <option value="">Filter Bulan</option>
      <?php
        while ($rowBulan = mysqli_fetch_assoc($bulanResult)) {
            $selected = ($bulan_filter == $rowBulan['bulan']) ? 'selected' : '';
            echo "<option value='".$rowBulan['bulan']."' $selected>".$rowBulan['bulan']."</option>";
        }
      ?>
    </select>

    <input 
    type="text" 
    name="nama" 
    placeholder="Cari Nama Siswa" 
    value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>" 
  />

    <button type="submit">Terapkan Filter</button>
    <a href="laporan_kas.php" style="text-decoration:none;"><a href="laporan_kas.php" class="reset-btn">Reset</a>
  </form>

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
  <table>   <table>
    <tr>
      <th>NO</th>
      <th>BULAN</th>
      <th>NAMA SISWA</th>
      <th>MINGGU 1</th>
      <th>MINGGU 2</th>
      <th>MINGGU 3</th>
      <th>MINGGU 4</th>
      <th>AKSI</th>
    </tr>
    <?php
    $no = 1;
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$no++."</td>";
            echo "<td>".$row['bulan']."</td>";
            echo "<td>".$row['nama']."</td>";
            for ($i = 1; $i <= 4; $i++) {
            $status = $row["minggu_$i"];
            $tgl = $row["tgl_bayar_minggu_$i"];
            $class = $status == 'lunas' ? 'lunas' : 'belum';

            echo "<td class='$class'>";
            echo ucfirst($status);

            if ($status === 'lunas' && !empty($tgl)) {
               echo "<br><small>$tgl</small>";
              }
              echo "</td>";
              }
            if ($_SESSION['role'] == 'admin') {
                echo "<td class='aksi'>
                        <a href='detail_iuran.php?id_iuran=".$row['id_iuran']."' class='detail'>Detail</a>
                        <a href='edit_laporan.php?id_iuran=".$row['id_iuran']."' class='edit'>Edit</a>
                        <a href='hapus_laporan.php?id_iuran=".$row['id_iuran']."' class='hapus' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>
                      </td>";
            } else {
                echo "<td class='aksi'>
                        <a href='detail_iuran.php?id_iuran=".$row['id_iuran']."' class='detail'>Detail</a>
                      </td>";
            }
            echo "</tr>";
        }
    } else {
        $colspan = ($_SESSION['role'] == 'admin') ? 9 : 8;
        echo "<tr><td colspan='$colspan'>Tidak ada data</td></tr>";
    }
    mysqli_free_result($result);
    mysqli_close($koneksi);
    ?>
  </table> </table>
</div>

</body>
</html>

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
