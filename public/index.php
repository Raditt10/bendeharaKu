<?php
// index.php - Router Utama Bendehara Kelas

$page = isset($_GET['page']) && is_string($_GET['page']) ? trim($_GET['page']) : 'home';
$safePage = basename($page); // Security: Mencegah directory traversal
$viewPath = __DIR__ . '/../app/Views/' . $safePage . '.php';

    // Cek apakah file view ada, jika tidak ada dan bukan home, tampilkan 404
    if ($page !== 'home' && !is_file($viewPath)) {
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p>";
        exit;
    }
    // provide common view variables to avoid undefined notices
    $error_msg = $error_msg ?? '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bendehara Kelas — Manajemen Keuangan Sekolah</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php 
    // 1. INCLUDE HEADER
    $headerPath = __DIR__ . '/../app/Views/partials/header.php';
    if (is_file($headerPath)) {
        include $headerPath;
    } else {
        // Fallback Header jika file file partial belum ada
        echo '<header class="site-header"><div class="container header-inner"><a class="brand" href="?page=home">Bendehara Kelas</a></div></header>';
    }
    ?>

    <main>
        <?php 
        // 2. LOGIK KONTEN (Home vs Page Lain)
        if ($page === 'home'): ?>
            <section class="hero">
                <div class="container hero-inner">
                    <div class="hero-copy">
                        <h1>Kelola iuran kelas dengan lebih profesional</h1>
                        <p class="lead">Sistem transparan untuk guru dan bendahara. Laporan otomatis, riwayat transaksi, dan manajemen siswa dalam satu genggaman.</p>
                        <div class="cta">
                            <a class="btn btn-primary" href="?page=login">Mulai Sekarang</a>
                            <a class="btn btn-outline" href="?page=register">Daftar Guru</a>
                        </div>
                    </div>
                    <div class="hero-media">
                        <div class="card-stacked">
                            <div class="card">📈 Total Pemasukan</div>
                            <div class="card">📉 Total Pengeluaran</div>
                            <div class="card">📋 Laporan Bulanan</div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="features">
                <div class="container">
                    <h2 class="section-title" style="text-align:center; font-size: 2rem; margin-bottom: 40px;">Mengapa Memilih Kami?</h2>
                    <div class="grid">
                        <div class="feature-card">
                            <h3>Manajemen Siswa</h3>
                            <p>Pantau status pembayaran kas setiap siswa secara real-time tanpa buku manual.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Laporan Otomatis</h3>
                            <p>Generate laporan bulanan untuk orang tua siswa hanya dengan satu klik.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Keamanan Data</h3>
                            <p>Data keuangan disimpan dengan aman menggunakan sistem login terenkripsi.</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php 
        else: 
            // Render halaman lain (login, register, dll)
            include $viewPath; 
        endif; 
        ?>
    </main>

    <?php 
    // 3. INCLUDE FOOTER (Mengambil file dari app/Views/partials/footer.php)
    $footerPath = __DIR__ . '/../app/Views/partials/footer.php';
    if (is_file($footerPath)) {
        include $footerPath;
    } 
    ?>

    <script>
        // Script universal (misal untuk notifikasi)
        function closeNotif(){
            const n = document.getElementById('notif-error'); 
            if(n) n.style.opacity = '0';
            setTimeout(() => { if(n) n.style.display='none'; }, 300);
        }
    </script>
</body>
</html>