<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE nis='$nis'";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
  $_SESSION['nis'] = $user['nis'];      
  $_SESSION['role'] = $user['role'];    
  $_SESSION['nama'] = $user['nama'];
  $_SESSION['login_success'] = true; 
  $_SESSION['success_msg'] = "Anda berhasil login!";
  header("Location: index.php");
  exit;
    } else {
        $error_msg = 'NIS atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Uang Kas</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
    html, body {
      height: 100%;
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      overflow: hidden;
      animation: bgmove 18s ease-in-out infinite alternate;
      position: relative;
    }
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      opacity: 0.18;
      pointer-events: none;
      z-index: 0;
      background: url('data:image/svg+xml;utf8,<svg width="800" height="600" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg"><g opacity="0.5"><rect x="40" y="40" width="120" height="60" rx="10" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="640" y="80" width="100" height="50" rx="10" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><circle cx="200" cy="500" r="38" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><rect x="320" y="120" width="60" height="60" rx="12" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="500" y="400" width="120" height="60" rx="10" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="100" y="300" width="80" height="40" rx="8" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><rect x="600" y="500" width="60" height="60" rx="12" fill="%23fffbe6" stroke="%23f1c40f" stroke-width="3"/><rect x="400" y="500" width="80" height="40" rx="8" fill="%23eaf6ff" stroke="%238e44ad" stroke-width="3"/><text x="60" y="80" font-size="24" fill="%238e44ad" font-family="Poppins">📚</text><text x="660" y="110" font-size="24" fill="%23f1c40f" font-family="Poppins">✏️</text><text x="340" y="160" font-size="24" fill="%238e44ad" font-family="Poppins">📒</text><text x="520" y="440" font-size="24" fill="%23f1c40f" font-family="Poppins">🖊️</text><text x="120" y="330" font-size="24" fill="%238e44ad" font-family="Poppins">📝</text><text x="620" y="530" font-size="24" fill="%23f1c40f" font-family="Poppins">📏</text><text x="420" y="530" font-size="24" fill="%238e44ad" font-family="Poppins">📐</text><text x="180" y="510" font-size="28" fill="%23f1c40f" font-family="Poppins">🎒</text></g></svg>') center center/cover repeat;
    }
    @keyframes bgmove {
      0% { background-position: 0% 50%; }
      100% { background-position: 100% 50%; }
    }
    @keyframes fadeSlideUp {
      0% {opacity: 0; transform: translateY(40px);}
      100% {opacity: 1; transform: translateY(0);}
    }
    .fade-in {
      animation: fadeSlideUp 0.9s ease forwards;
      opacity: 0;
    }
    .login-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
    }
    .form-box {
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(6px);
      padding: 36px 28px;
      border-radius: 20px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      width: 360px;
      text-align: center;
      border: 1.5px solid rgba(44,62,80,0.08);
    }
    .form-box h2 {
      margin-bottom: 20px;
      color: #8e44ad;
      font-weight: 700;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-top: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    .btn-auth {
      width: 100%;
      background: linear-gradient(90deg,#f1c40f,#8e44ad);
      color: #fff;
      padding: 12px;
      margin-top: 18px;
      border-radius: 10px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    .btn-auth:hover {
      background: linear-gradient(90deg,#8e44ad,#f1c40f);
      color: #2c3e50;
      transform: scale(1.03);
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
    a {
      display: block;
      margin-top: 20px;
      text-decoration: none;
      color: #8e44ad;
      font-size: 14px;
    }
    a:hover {
      text-decoration: underline;
    }
    .password-wrapper {
      position: relative;
      width: 100%;
    }
    .password-wrapper input {
      width: 100%;
      padding-right: 40px;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-25%);
      cursor: pointer;
      font-size: 18px;
      user-select: none;
    }
    .toggle-password:hover {
      color: #8e44ad;
    }

    /* Notifikasi error */
    .notif-error {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(-100%);
      background: #e74c3c;
      color: white;
      padding: 14px 24px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(231, 76, 60, 0.6);
      font-weight: 600;
      font-size: 15px;
      display: flex;
      align-items: center;
      gap: 16px;
      min-width: 280px;
      max-width: 90vw;
      opacity: 0;
      pointer-events: none;
      transition: transform 0.4s ease, opacity 0.4s ease;
      z-index: 9999;
    }
    .notif-error.show {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
      pointer-events: auto;
    }
    .notif-error button {
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

<?php if ($error_msg): ?>
<div id="notif-error" class="notif-error">
  <span><?= htmlspecialchars($error_msg) ?></span>
  <button onclick="closeNotif()" aria-label="Close notification">&times;</button>
</div>
<?php endif; ?>

<div class="login-wrapper fade-in">
  <div class="form-box">
    <h2>Login</h2>
    <form method="POST">
      <input type="text" name="nis" placeholder="Masukkan NIS Kamu" autocomplete="off" required>
      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Masukkan Password" autocomplete="off" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>
      <button type="submit" class="btn-auth">Masuk</button>
    </form>
    <a href="register.php">Belum punya Akun? Buat Akun</a>
  </div>
</div>

<script>
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

  function togglePassword() {
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.querySelector(".toggle-password");

    const isPasswordHidden = passwordInput.type === "password";

    passwordInput.type = isPasswordHidden ? "text" : "password";

    toggleIcon.classList.toggle("fa-eye");
    toggleIcon.classList.toggle("fa-eye-slash");
  }

  // Notif error show/hide
  window.addEventListener('DOMContentLoaded', () => {
    const notif = document.getElementById('notif-error');
    if (notif) {
      setTimeout(() => notif.classList.add('show'), 100);

      setTimeout(() => {
        notif.classList.remove('show');
      }, 4000);
    }
  });

  function closeNotif() {
    const notif = document.getElementById('notif-error');
    if (notif) {
      notif.classList.remove('show');
    }
  }
</script>
</body>
</html>
