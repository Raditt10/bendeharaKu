<?php
session_start();

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
      display: flex;
      flex-direction: column;
     min-height: 100vh;
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
    .wrapper {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 100vh;
  background: rgba(255,255,255,0.7);
  border-radius: 24px;
  box-shadow: 0 10px 32px rgba(44,62,80,0.10);
  backdrop-filter: blur(4px);
  border: 1.5px solid rgba(44,62,80,0.08);
  margin: 64px auto 32px auto; /* ← tambah jarak dari atas */
  max-width: 950px;
  position: relative;
  overflow: hidden;
  animation: fadeSlideUp 1.2s cubic-bezier(0.4, 0, 0.2, 1);
  transition: box-shadow 0.3s, background 0.3s;
}

    .wrapper:hover {
      box-shadow: 0 16px 48px rgba(142,68,173,0.18);
      background: rgba(255,255,255,0.85);
    }
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
      animation: fadeSlideUp 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      opacity: 0;
    }
    .fade-delay-1 { animation-delay: 0.2s; }
    .fade-delay-2 { animation-delay: 0.4s; }
    .fade-delay-3 { animation-delay: 0.6s; }
    .fade-delay-4 { animation-delay: 0.8s; }
    header {
          background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
          padding: 24px 36px;
          color: #fff;
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          gap: 18px;
          box-shadow: 0 4px 24px rgba(44,62,80,0.12);
          border-radius: 0 0 24px 24px;
          backdrop-filter: blur(6px);
          border-bottom: 2.5px solid rgba(255,255,255,0.18);
          transition: background 0.5s;
        }
    header h1 {
          font-size: 28px;
          font-weight: 700;
          letter-spacing: 1px;
          text-shadow: 0 2px 8px rgba(44,62,80,0.12);
          margin-bottom: 12px;
        }
    nav {
      display: flex;
      gap: 24px;
      align-items: center;
    }
    nav a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      position: relative;
      padding: 8px 0;
      transition: color 0.3s, background 0.3s;
      border-radius: 6px;
    }
    nav a::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -4px;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      transition: width 0.3s;
      border-radius: 2px;
    }
    nav a:hover {
      color: #f1c40f;
      background: rgba(241,196,15,0.08);
    }
    nav a:hover::after {
      width: 100%;
    }
    .btn-auth {
      background: linear-gradient(90deg, #f1c40f, #8e44ad);
      color: #fff;
      padding: 8px 20px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 700;
      border: none;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      transition: background 0.3s, color 0.3s, transform 0.2s;
      cursor: pointer;
    }
    .btn-auth:hover {
      background: linear-gradient(90deg, #8e44ad, #f1c40f);
      color: #2c3e50;
      transform: scale(1.05);
    }
    main {
      max-width: 100%;
      margin: 50px auto;
      padding: 48px 32px;
      background: rgba(255,255,255,0.7);
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      text-align: center;
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
      flex: 1;
    }
    main h2 {
      font-size: 32px;
      margin-bottom: 18px;
      font-weight: 700;
      color: #8e44ad;
      letter-spacing: 1px;
      text-shadow: 0 2px 8px rgba(44,62,80,0.08);
    }
    main p, main h4 {
      font-size: 18px;
      color: #444;
      line-height: 1.7;
      margin-bottom: 10px;
    }
    main h4 {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 18px;
    }
    .welcome-emoji {
      font-size: 40px;
      margin-bottom: 10px;
      display: inline-block;
      animation: bounce 1.2s infinite alternate;
    }
    @keyframes bounce {
      0% { transform: translateY(0); }
      100% { transform: translateY(-10px); }
    }
    footer {
      text-align: center;
      padding: 24px;
      font-size: 15px;
      color: #fff;
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      border-radius: 0 0 28px 28px;
      box-shadow: 0 -2px 12px rgba(44,62,80,0.10);
      letter-spacing: 1px;
      border-top: 2.5px solid rgba(255,255,255,0.18);
      transition: background 0.5s;
      width: 100%;
      align-self: flex-end;
    }
    @media (max-width: 600px) {
      header {
        flex-direction: column;
        gap: 10px;
        padding: 18px 10px;
      }
      nav {
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
      }
      main {
        padding: 24px 8px;
      }
    }
    .btn-auth {
    position: relative;
    overflow: hidden;
    z-index: 1;
  }
  .btn-auth .ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple-animate 0.6s linear;
    background: rgba(255,255,255,0.5);
    pointer-events: none;
    z-index: 2;
  }
  @keyframes ripple-animate {
    to {
      transform: scale(2.5);
      opacity: 0;
    }
  }
.notif-success {
      position: fixed;
      top: 30px;
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
    @keyframes notifFadeIn {
      0% { opacity: 0; transform: translateX(-50%) translateY(-40px) scale(0.95); }
      60% { opacity: 1; transform: translateX(-50%) translateY(10px) scale(1.04); }
      100% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
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
  padding: 0;
  user-select: none;
}

  </style>
</head>
<body>

<?php if ($success_msg): ?>
<div id="notif-success" class="notif-success show">
  <span><?= htmlspecialchars($success_msg) ?></span>
  <button onclick="closeNotif()" aria-label="Close notification">&times;</button>
</div>
<?php endif; ?>

<div class="wrapper">
  <header class="fade-in fade-delay-1">
    <h1 class="fade-in fade-delay-2">Uang Kas XI RPL 1</h1>
    <nav class="fade-in fade-delay-3">
      <?php if (!isset($_SESSION['nis'])): ?>
      <?php else: ?>
        <a href="data_pemasukan.php">Data Pemasukan</a>
        <a href="data_pengeluaran.php">Data Pengeluaran</a>

        <a href="tentang.php">Tentang kami</a>
        <a href="logout.php" class="btn-auth">Logout</a>
      <?php endif; ?>
    </nav>
  </header>
  
  <main class="fade-in fade-delay-4" style="transition: transform 0.3s;">
    <?php if (isset($_SESSION['nis'])): ?>
      <span class="welcome-emoji" style="animation: bounce 1.2s infinite alternate;">👋</span>
      <h2 style="transition: color 0.3s;">Selamat Datang!</h2>
      <h4 style="color:#2c3e50;font-weight:600;margin-bottom:18px;transition: color 0.3s;">Hai, <?= htmlspecialchars($_SESSION['nama']); ?>!</h4>
      <p style="font-size:18px;color:#444;line-height:1.7;margin-bottom:10px;transition: color 0.3s;">Aplikasi ini digunakan untuk mengelola uang kas kelas <strong>XI RPL 1</strong> secara digital.<br>
      Anda dapat melihat data siswa, menambahkan data, serta mencatat pemasukan dan pengeluaran kas.</p>
      <div style="margin: 30px 0; display: flex; justify-content: center; gap: 24px; flex-wrap: wrap;">
        <a href="data_siswa.php" class="btn-auth" style="background: linear-gradient(90deg,#8e44ad,#f1c40f);box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">Data Siswa</a>
        <a href="laporan_kas.php" class="btn-auth" style="background: linear-gradient(90deg,#f1c40f,#8e44ad);box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">Laporan Kas</a>
      </div>
    <?php else: ?>
      <span class="welcome-emoji" style="animation: bounce 1.2s infinite alternate;">👋</span>
      <h2 style="transition: color 0.3s;">Selamat Datang!</h2>
      <p style="font-size:18px;color:#444;line-height:1.7;margin-bottom:10px;transition: color 0.3s;">Untuk mengakses fitur seperti melihat data siswa atau laporan kas,<br>
      silakan <strong>login</strong> terlebih dahulu.</p>
      <div style="margin: 30px 0; display: flex; justify-content: center; gap: 24px; flex-wrap: wrap;">
        <a href="login.php" class="btn-auth" style="background: linear-gradient(90deg,#f1c40f,#8e44ad);box-shadow:0 2px 8px rgba(44,62,80,0.10);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">Login</a>
      </div>
    <?php endif; ?>
  </main>

  <footer class="fade-in fade-delay-4">
    &copy; <?= date("Y") ?> X RPL 1 — SMKN 13 Bandung
  </footer>
</div>

  <script>
  // Ripple effect for .btn-auth buttons
  document.querySelectorAll('.btn-auth').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const circle = document.createElement('span');
      circle.className = 'ripple';
      const rect = btn.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      circle.style.width = circle.style.height = size + 'px';
      circle.style.left = (e.clientX - rect.left - size/2) + 'px';
      circle.style.top = (e.clientY - rect.top - size/2) + 'px';
      btn.appendChild(circle);
      setTimeout(() => circle.remove(), 600);
    });
  });

function closeNotif() {
  const notif = document.getElementById("notif-success");
  if (notif) {
    notif.classList.remove("show");
    notif.style.display = 'none';
    notif.innerHTML = '';
    localStorage.setItem('notifSuccessClosed', '1');
  }
}

// Auto-close dalam 4 detik
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

</body>
</html>