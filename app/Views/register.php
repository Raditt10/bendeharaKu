<?php
// === LOGIKA PHP ===
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

$success_msg = $_SESSION['success_msg'] ?? null;

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$error_msg = ''; // Inisialisasi variabel kosong agar tidak error di PHP lama

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama'] ?? '');
  $nis = trim($_POST['nis'] ?? '');
  $password_plain = $_POST['password'] ?? '';
  $role = isset($_POST['role']) && $_POST['role'] !== '' ? trim($_POST['role']) : 'siswa';

  if ($nama === '' || $nis === '' || $password_plain === '') {
    $error_msg = 'Nama, NIS, dan password wajib diisi.';
  } else {
    // Cek NIS
    $checkStmt = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM users WHERE nis = ?");
    if ($checkStmt) {
      mysqli_stmt_bind_param($checkStmt, 's', $nis);
      mysqli_stmt_execute($checkStmt);
      mysqli_stmt_bind_result($checkStmt, $count);
      mysqli_stmt_fetch($checkStmt);
      mysqli_stmt_close($checkStmt);
    } else {
      $count = 0;
    }

    if (!empty($count)) {
      $error_msg = 'NIS sudah terdaftar. Gunakan NIS lain atau hubungi admin.';
    } else {
      $password = password_hash($password_plain, PASSWORD_DEFAULT);
      $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, nis, password, role) VALUES (?, ?, ?, ?)");
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssss', $nama, $nis, $password, $role);
        if (mysqli_stmt_execute($stmt)) {
          $show_success = true;
        } else {
          $error_msg = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
      } else {
        $error_msg = 'Gagal menyiapkan query pendaftaran.';
      }
    }
  }
}
?>

<?php if (!empty($show_success)): ?>
  <div id="notif-success" class="notif-success" style="position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 15px; border-radius: 8px; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <i class="fa-solid fa-check-circle"></i> Registrasi berhasil! Mengalihkan...
  </div>
  <script>
    setTimeout(() => { window.location = '?page=login'; }, 2000);
  </script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div style="max-width: 400px; margin: 20px auto 0; background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; text-align: center;">
        <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>

<div class="auth-page">
  <div class="form-box">
    <div style="text-align: center; margin-bottom: 24px;">
        <h2 style="margin:0; font-size: 1.5rem;">Buat Akun Baru</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Isi data diri kamu dengan benar</p>
    </div>

    <form method="POST">
      <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" placeholder="Contoh: Davin Maritza" autocomplete="off" required>
      </div>

      <div class="form-group">
          <label>NIS</label>
          <input type="text" name="nis" placeholder="Nomor Induk Siswa" autocomplete="off" required>
      </div>

      <div class="form-group" style="position: relative;">
          <label>Password</label>
          <input type="password" name="password" id="password" placeholder="Buat password aman" autocomplete="off" required>
          <span onclick="togglePassword()" style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #94a3b8;">
             👁️
          </span>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
          Daftar Sekarang
      </button>
    </form>

    <p style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
        Sudah punya akun? <a href="?page=login" style="color: var(--primary); font-weight: 600; text-decoration: none;">Login di sini</a>
    </p>
  </div>
</div>

<script>
  function togglePassword() {
    const passwordInput = document.getElementById("password");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
  }
</script>