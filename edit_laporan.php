<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

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
        session_start();
        $_SESSION['success_msg'] = "Data iuran berhasil diupdate!";
        header("Location: laporan_kas.php");
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
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.container {
  max-width: 650px;
  margin: 60px auto;
  background: rgba(255, 255, 255, 0.9);
  padding: 40px 35px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.12);
  backdrop-filter: blur(5px);
  animation: fadeInUp 0.8s cubic-bezier(.4,2,.6,1);
}

h2 {
  text-align: center;
  margin-bottom: 35px;
  color: #8e44ad;
  font-weight: 700;
  animation: fadeInUp 0.8s cubic-bezier(.4,2,.6,1);
}

label {
  display: block;
  margin-top: 18px;
  font-weight: 600;
  color: #3c3c3c;
  font-size: 16px;
}

input[type="text"],
input[type="date"],
select {
  width: 70%;
  padding: 12px 14px;
  margin-top: 6px;
  border-radius: 10px;
  border: 1.8px solid #a18cd1;
  font-size: 15px;
  transition: border-color 0.3s ease;
  font-family: 'Poppins', sans-serif;
  color: #4a4a4a;
}

select {
  width: 30%;
}

input[type="text"]{
  width: 40%;
}

input[type="text"]:focus,
input[type="date"]:focus,
select:focus {
  border-color: #8e44ad;
  outline: none;
  box-shadow: 0 0 8px #8e44ad55;
}

.minggu-checklist {
  margin: 20px 0;
}

.minggu-checklist label {
  display: flex;
  align-items: center;
  margin: 10px 0;
  font-weight: 600;
  color: #5a3e7a;
}

.checkbox {
  transform: scale(1.3);
  margin-right: 14px;
  cursor: pointer;
}

.tanggal-mingguan {
  margin-top: 15px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.tanggal-mingguan label {
  font-weight: 600;
  color: #5a3e7a;
  margin-bottom: 6px;
}

.tanggal-mingguan input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1.8px solid #a18cd1;
  font-size: 15px;
  color: #4a4a4a;
  font-family: 'Poppins', sans-serif;
}

.tanggal-mingguan input:disabled {
  background-color: #f2e7ff;
  border-color: #d1b3ff;
  color: #8c7bb6;
  cursor: not-allowed;
}

select.lunas {
  background-color: #d4edda;
  color: #155724;
}

select.belum {
  background-color: #f8d7da;
  color: #721c24;
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
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Iuran</h2>
    <form method="POST" action="?id_iuran=<?= $id_iuran ?>">
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

