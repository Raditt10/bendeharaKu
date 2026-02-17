<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg) unset($_SESSION['success_msg']);
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) unset($_SESSION['error_msg']);
if ($error_msg) unset($_SESSION['error_msg']);

if (!isset($_SESSION['nis'])) {
    header("Location: ?page=login");
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

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
   <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    /* styles copied from original */
  </style>
</head>
<body>
<!-- header is included centrally in public/index.php -->

<div class="container">
  <h2>Laporan Kas XI RPL 1</h2>

  <div class="nav-buttons">
    <a href="?page=home">Kembali</a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="?page=add_dues">Tambah Data</a>
    <?php endif; ?>
    <a href="?page=report">Muat Ulang</a>
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
    <a href="?page=report" class="reset-btn">Reset</a>
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
  <table>
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

            echo "<td class='".$class."'>";
            echo ucfirst($status);

            if ($status === 'lunas' && !empty($tgl)) {
               echo "<br><small>$tgl</small>";
              }
              echo "</td>";
              }
            if ($_SESSION['role'] == 'admin') {
                echo "<td class='aksi'>
                        <a href='?page=detail_dues&id_iuran=".$row['id_iuran']."' class='detail'>Detail</a>
                        <a href='?page=edit_report&id_iuran=".$row['id_iuran']."' class='edit'>Edit</a>
                        <a href='?page=delete_report&id_iuran=".$row['id_iuran']."' class='hapus' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>
                      </td>";
            } else {
                echo "<td class='aksi'>
                        <a href='?page=detail_dues&id_iuran=".$row['id_iuran']."' class='detail'>Detail</a>
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
  </table>
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
