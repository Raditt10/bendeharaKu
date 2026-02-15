<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg) unset($_SESSION['success_msg']);
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) unset($_SESSION['error_msg']);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

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
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    /* styles copied from original */
  </style>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <h2>Data Pemasukan Kas</h2>
  <div class="nav-buttons">
    <a href="?page=home">Kembali</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="?page=add_income">Tambah data</a>
    <?php endif; ?>
    <a href="?page=income">Muat Ulang</a>
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
            <a href='?page=edit_income&id=" . $row['id_pemasukan'] . "' style='background:linear-gradient(90deg,#3498db,#f1c40f);'>Edit</a>
            <a href='?page=delete_income&id=" . $row['id_pemasukan'] . "' style='background:linear-gradient(90deg,#e74c3c,#f1c40f);' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>
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
