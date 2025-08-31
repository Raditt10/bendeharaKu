<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tentang Aplikasi Kas</title>
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
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes bounce {
      0% { transform: translateY(0); }
      100% { transform: translateY(-10px); }
    }
    .fade-in {
      animation: fadeSlideUp 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards;
      opacity: 0;
    }
    .fade-delay-1 { animation-delay: 0.2s; }
    .fade-delay-2 { animation-delay: 0.4s; }
    .fade-delay-3 { animation-delay: 0.6s; }
    .fade-delay-4 { animation-delay: 0.8s; }

    .wrapper {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 100vh;
      min-height: 100dvh;
      justify-content: space-between;
      background: rgba(255,255,255,0.7);
      border-radius: 24px;
      box-shadow: 0 10px 32px rgba(44,62,80,0.10);
      backdrop-filter: blur(4px);
      border: 1.5px solid rgba(44,62,80,0.08);
      margin: 32px auto 32px auto;
      max-width: 950px;
      padding: 0 0 24px 0;
      position: relative;
      overflow: hidden;
      animation: fadeSlideUp 1.2s cubic-bezier(0.4, 0, 0.2, 1);
      transition: box-shadow 0.3s, background 0.3s;
    }
    .wrapper:hover {
      box-shadow: 0 16px 48px rgba(142,68,173,0.18);
      background: rgba(255,255,255,0.85);
    }

    header {
      background: linear-gradient(120deg, #f6d365 0%, #fda085 40%, #a18cd1 100%, #fbc2eb 120%);
      padding: 24px 36px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .about-emoji {
      font-size: 32px;
      vertical-align: middle;
      animation: bounce 1.2s infinite alternate;
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

    main {
      flex: 1;
      padding: 24px 36px;
      color: #2c3e50;
      font-size: 1.1rem;
      line-height: 1.65;
      user-select: text;
      animation: fadeSlideUp 0.7s ease forwards;
    }
    main h2 {
      font-size: 26px;
      margin-bottom: 12px;
      font-weight: 600;
      color: #4b4b4b;
      text-align: center;
    }
    main p {
      margin-top: 0;
      margin-bottom: 1em;
    }
    main ul {
      margin: 12px 0 24px 24px;
      list-style-type: disc;
    }
    main ul li {
      margin-bottom: 8px;
    }

    footer {
      font-size: 0.9rem;
      color: #4b4b4b;
      text-align: center;
      padding: 12px 0 18px 0;
      border-top: 1px solid rgba(44,62,80,0.12);
      user-select: none;
      font-style: italic;
      background: transparent;
      animation: fadeSlideUp 1.1s ease forwards;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <h1><span class="about-emoji">📋</span>Tentang kami</h1>
      <nav>
        <a href="index.php">Beranda</a>
        <a href="data_pemasukan.php">Data Pemasukan</a>
        <a href="data_pengeluaran.php">Data Pengeluaran</a>
      </nav>
    </header>
    <main>
      <h2>Tentang Kelas XI RPL 1</h2>
<p>Kelas XI RPL 1 adalah salah satu kelas dari jurusan Rekayasa Perangkat Lunak di SMKN 13 Bandung. Kelas ini terdiri dari <strong>35 siswa</strong> yang memiliki semangat tinggi dalam belajar, berorganisasi, dan mengembangkan keterampilan di bidang teknologi.</p>

<p>Dibimbing oleh wali kelas yang bijaksana dan penuh perhatian, yaitu <strong>Ibu Pratiwi</strong>, XI RPL 1 berkomitmen untuk membentuk lingkungan belajar yang produktif, kompak, dan bertanggung jawab.</p>

<p>Untuk membantu pengelolaan dan koordinasi kelas, berikut adalah struktur organisasi kelas XI RPL 1:</p>
<ul>
  <li><strong>Ketua Kelas:</strong> Nabil Akbar Fadillah</li>
  <li><strong>Sekretaris:</strong> Raikhania</li>
  <li><strong>Bendahara:</strong> Rafaditya Syahputra <strong>(Dev this Website)</strong> & Adelya Fauzi</li>
</ul>

<p>Aplikasi ini dibuat untuk memudahkan pencatatan kas kelas secara digital, mulai dari pemasukan, pengeluaran, hingga pelaporan yang transparan dan efisien. Diharapkan dengan adanya sistem ini, seluruh anggota kelas dapat dengan mudah memantau dan berkontribusi dalam pengelolaan keuangan kelas.</p>

    </main>
    <footer>
      &copy; <?php echo date('Y'); ?> Aplikasi Kas • Semua hak cipta dilindungi.
    </footer>
  </div>
</body>
</html>
