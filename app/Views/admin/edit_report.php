<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

if (isset($_GET['id_iuran'])) {
    $id_iuran = (int)$_GET['id_iuran'];

    $query = "SELECT i.*, s.nama AS nama_siswa FROM iuran i 
              JOIN siswa s ON i.id_siswa = s.id_siswa 
              WHERE i.id_iuran = $id_iuran";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) == 1) {
        $data = mysqli_fetch_assoc($result);

        $minggu_array = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($data["minggu_$i"] === 'lunas') {
                $minggu_array[] = (string)$i;
            }
        }
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    echo "ID tidak ditemukan.";
    exit;
}

if (isset($_POST['simpan'])) {
    $bulan = mysqli_real_escape_string($koneksi, $_POST['bulan']);
    $status = $_POST['status'] ?? 'belum';

    $minggu_lunas = isset($_POST['minggu']) ? $_POST['minggu'] : [];
    $tanggal_minggu = [];
    for ($i = 1; $i <= 4; $i++) {
        $tanggal_minggu[$i] = !empty($_POST["tanggal_minggu_$i"]) ? $_POST["tanggal_minggu_$i"] : null;
    }

    if ($status === 'lunas' && count($minggu_lunas) < 4) {
        echo "<script>alert('Status tidak bisa LUNAS jika belum semua minggu dibayar.');</script>";
        exit;
    }
    $minggu_ke_str = implode(',', $minggu_lunas);
    $update = "UPDATE iuran SET bulan='$bulan', status='$status', minggu_ke='$minggu_ke_str', ";
    for ($i = 1; $i <= 4; $i++) {
        $stat = in_array((string)$i, $minggu_lunas) ? 'lunas' : 'belum';
        $tgl = $tanggal_minggu[$i] ? "'".$tanggal_minggu[$i]."'" : "NULL";
        $update .= "minggu_$i='$stat', tgl_bayar_minggu_$i=$tgl";
        if ($i < 4) $update .= ", ";
    }
    $update .= " WHERE id_iuran=$id_iuran";

    if (mysqli_query($koneksi, $update)) {
      if (session_status() === PHP_SESSION_NONE) session_start();
      $_SESSION['success_msg'] = "Data iuran berhasil diupdate!";
        header("Location: ?page=report");
        exit;
    } else {
        echo "Gagal update data: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Iuran</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <style>
    /* styles copied from original */
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Iuran</h2>
    <form method="POST" action="?page=edit_report&id_iuran=<?= $id_iuran ?>">
      <p><strong>ID Iuran:</strong> <?= $data['id_iuran'] ?></p>
      <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($data['nama_siswa']) ?></p>

      <label for="bulan">Bulan</label>
      <input type="text" name="bulan" id="bulan" required value="<?= htmlspecialchars($data['bulan']) ?>" />

      <label>Status Keseluruhan</label>
      <select name="status" required>
        <option value="belum" <?= ($data['status'] === 'belum') ? 'selected' : '' ?>>Belum</option>
        <option value="lunas" <?= ($data['status'] === 'lunas') ? 'selected' : '' ?>>Lunas</option>
      </select>

      <label>Status Minggu</label>
      <div class="minggu-checklist">
        <?php for ($i=1; $i<=4; $i++): ?>
          <label>
            <input type="checkbox" name="minggu[]" value="<?= $i ?>" <?= in_array((string)$i, $minggu_array) ? 'checked' : '' ?>>
            Minggu ke-<?= $i ?>
          </label>
        <?php endfor; ?>
      </div>

      <label>Tanggal Bayar per minggu</label>
      <?php for ($i=1; $i<=4; $i++): ?>
        <label for="tanggal_minggu_<?= $i ?>">Minggu ke-<?= $i ?></label>
        <input type="date" name="tanggal_minggu_<?= $i ?>" id="tanggal_minggu_<?= $i ?>" value="<?= htmlspecialchars($data["tgl_bayar_minggu_$i"]) ?>" <?= in_array((string)$i, $minggu_array) ? '' : 'disabled' ?> />
      <?php endfor; ?>

      <button type="submit" name="simpan">Simpan Perubahan</button>
    </form>
  </div>

  <script>
    document.querySelectorAll('input[type=checkbox][name="minggu[]"]').forEach((cb, idx) => {
      const tanggalInput = document.getElementById('tanggal_minggu_' + (idx+1));
      cb.addEventListener('change', () => {
        tanggalInput.disabled = !cb.checked;
        if (!cb.checked) tanggalInput.value = '';
      });
      cb.dispatchEvent(new Event('change'));
    });
  </script>
</body>
</html>
