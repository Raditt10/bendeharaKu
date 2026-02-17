<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Dashboard assumes front controller enforces authentication
$nama = $_SESSION['nama'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - BendeharaKu</title>
  <link rel="stylesheet" href="assets/css/base.css" />
  <style>
    .dashboard-wrapper { max-width: 1100px; margin: 40px auto; padding: 0 24px; }
    .welcome { font-size: 1.5rem; margin-bottom: 12px; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 16px; margin-top: 24px; }
    .card { background: #fff; padding: 18px; border-radius: 12px; box-shadow: 0 6px 18px rgba(2,6,23,0.06); border:1px solid #eef2f6; }

    /* Simple alert styles for flash messages */
    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; display:flex; justify-content:space-between; align-items:center; border:1px solid transparent; }
    .alert-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
    .alert .close-btn { background: transparent; border: none; font-size: 1.1rem; cursor: pointer; color: inherit; }
  </style>
</head>
<body>
<!-- header is included centrally in public/index.php -->
<div class="dashboard-wrapper">
  <?php if (!empty($_SESSION['success_msg'])): ?>
    <div class="alert alert-success" id="login-success">
      <div><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
      <button class="close-btn" onclick="document.getElementById('login-success').style.display='none'">&times;</button>
    </div>
    <?php unset($_SESSION['success_msg']); endif; ?>
  <div class="welcome">Selamat datang, <strong><?= htmlspecialchars($nama) ?></strong></div>
  <p class="lead">Ringkasan cepat aktivitas dan saldo.</p>

  <div class="cards">
    <div class="card slide-in-right reveal">
      <h3 class="muted">Pemasukan Bulan Ini</h3>
      <p class="stat-number" style="font-size:1.45rem;font-weight:800;margin:8px 0;">Rp 0</p>
      <p class="muted">(contoh)</p>
    </div>
    <div class="card slide-in-left reveal">
      <h3 class="muted">Pengeluaran Bulan Ini</h3>
      <p class="stat-number" style="font-size:1.45rem;font-weight:800;margin:8px 0;">Rp 0</p>
      <p class="muted">(contoh)</p>
    </div>
    <div class="card slide-in-right reveal">
      <h3 class="muted">Jumlah Siswa</h3>
      <p class="stat-number" style="font-size:1.45rem;font-weight:800;margin:8px 0;">0 orang</p>
      <p class="muted">(contoh)</p>
    </div>
  </div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var el = document.getElementById('login-success');
  if (!el) return;
  // auto-hide after 10 seconds
  setTimeout(function(){
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    el.style.opacity = '0';
    el.style.transform = 'translateY(-8px)';
    setTimeout(function(){ if (el.parentNode) el.parentNode.removeChild(el); }, 700);
  }, 10000);
});
</script>
</body>
</html>
