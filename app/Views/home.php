<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$success_msg = $_SESSION['success_msg'] ?? null;
if ($success_msg) {
  unset($_SESSION['success_msg']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Uang Kas X RPL 1</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Inline page-specific overrides (kept minimal) */
  </style>
</head>
<body>

<?php if ($success_msg): ?>
<div id="notif-success" class="notif-success show">
  <span><?= htmlspecialchars($success_msg) ?></span>
  <button onclick="closeNotif()" aria-label="Close notification">&times;</button>
</div>
<?php endif; ?>

<?php
// Dashboard data (show totals)
require_once __DIR__ . '/../Models/Database.php';
$conn = Database::getInstance()->getConnection();

$total_students = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM siswa");
if ($res) { $row = mysqli_fetch_assoc($res); $total_students = $row['c'] ?? 0; }

$total_income = 0;
$res = mysqli_query($conn, "SELECT SUM(jumlah) AS s FROM pemasukan");
if ($res) { $row = mysqli_fetch_assoc($res); $total_income = $row['s'] ?? 0; }

$total_expenses = 0;
$res = mysqli_query($conn, "SELECT SUM(jumlah) AS s FROM pengeluaran");
if ($res) { $row = mysqli_fetch_assoc($res); $total_expenses = $row['s'] ?? 0; }
?>

<div class="wrapper">
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="container">
    <section class="panel">
      <div style="display:flex;align-items:center;gap:18px;">
        <div style="font-size:48px">👋</div>
        <div>
          <h2>Welcome to Uang Kas XI RPL 1</h2>
          <p class="muted">Manage student records, income and expense entries for the class. Use the navigation to proceed.</p>
        </div>
      </div>
    </section>

    <section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-top:1rem">
      <div class="panel">
        <h3>Total Students</h3>
        <p style="font-size:28px;font-weight:700;margin-top:8px;"><?= number_format((int)$total_students) ?></p>
        <p class="muted">Registered in the system</p>
        <p style="margin-top:12px"><a href="?page=students">View students &raquo;</a></p>
      </div>

      <div class="panel">
        <h3>Total Income</h3>
        <p style="font-size:28px;font-weight:700;margin-top:8px;">Rp <?= number_format((float)$total_income,0,',','.') ?></p>
        <p class="muted">Sum of all income records</p>
        <p style="margin-top:12px"><a href="?page=income">View income &raquo;</a></p>
      </div>

      <div class="panel">
        <h3>Total Expenses</h3>
        <p style="font-size:28px;font-weight:700;margin-top:8px;">Rp <?= number_format((float)$total_expenses,0,',','.') ?></p>
        <p class="muted">Sum of all expense records</p>
        <p style="margin-top:12px"><a href="?page=expenses">View expenses &raquo;</a></p>
      </div>
    </section>

    <footer style="margin-top:28px;color:var(--slate-600);">&copy; <?= date("Y") ?> X RPL 1 — SMKN 13 Bandung</footer>
  </main>
</div>

  <script>
  // (JS omitted for brevity - copied from original index.php)
  </script>

</body>
</html>
