<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['nis'])) {
    header("Location: ?page=login");
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Query ambil data
$query = "SELECT * FROM siswa";
$result = mysqli_query($koneksi, $query);

$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg === 'Anda berhasil login!') {
  $success_msg = null;
} else if ($success_msg) {
  unset($_SESSION['success_msg']);
}
$error_msg = $_SESSION['error_msg'] ?? null;
if ($error_msg) {
  unset($_SESSION['error_msg']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Siswa</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    /* styles copied from original */
  </style>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

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
    <a href="?page=home">Kembali</a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="?page=add_student">Tambah Data</a>
    <?php endif; ?>
    <a href="?page=students">Muat Ulang</a>
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
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr class='fade-in'>";
            echo "<td>".$no++."</td>";
            echo "<td>".$row['nis']."</td>";
            echo "<td>".$row['nama']."</td>";
            echo "<td>".$row['kontak_orangtua']."</td>";
            if ($_SESSION['role'] == 'admin') {
                echo "<td>
                        <a class='aksi-btn edit' href='?page=edit_student&id=".$row['id_siswa']."'>Edit</a>
                        <a class='aksi-btn hapus' href='?page=delete_student&id=".$row['id_siswa']."' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
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
