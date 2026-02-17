<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$conn = Database::getInstance()->getConnection();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ?page=students');
  exit;
}

// Read flash errors from session (form processing handled by front controller)
$error = $_SESSION['error_msg'] ?? null;
unset($_SESSION['error_msg']);
?>
<div class="container mt-12 mb-12">
  <div style="max-width:720px; margin:0 auto;">
    <h2 class="mb-12">Tambah Data Siswa</h2>
    <?php if ($error): ?>
      <div class="alert alert-error mb-12"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=add_student" class="form-card">
      <label class="form-label">NIS / NISN</label>
      <input type="text" name="nis" placeholder="NIS/NISN" class="form-control" required />

      <label class="form-label">Nama Siswa</label>
      <input type="text" name="nama" placeholder="Nama Siswa" class="form-control" required />

      <label class="form-label">Kontak Orang Tua</label>
      <input type="text" name="kontak" placeholder="Kontak Orang Tua" class="form-control" />

      <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:6px;">
        <a class="btn btn-outline" href="?page=students">Batal</a>
        <button type="submit" name="submit" class="btn btn-primary">Simpan Data</button>
      </div>
    </form>
  </div>
</div>
