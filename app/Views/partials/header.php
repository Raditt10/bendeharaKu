<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Helper sederhana untuk mengecek link aktif
if (!function_exists('isActive')) {
    function isActive($target) {
        $current = isset($_GET['page']) ? $_GET['page'] : 'home';
        return $current === $target ? 'active' : '';
    }
}
?>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="?page=home">
            BendeharaKu <span style="color: var(--primary);">XI RPL 1</span>
        </a>

        <nav class="nav">
            <?php if (!isset($_SESSION['nis'])): ?>
                <a href="?page=home" class="<?= isActive('home') ?>">Beranda</a>
                <a href="#features">Fitur</a>
                
                <div class="auth-buttons">
                    <a href="?page=login" class="btn btn-sm btn-outline">Masuk</a>
                    <a href="?page=register" class="btn btn-sm btn-primary">Daftar</a>
                </div>

            <?php else: ?>
                <div class="nav-links">
                    <a href="?page=income" class="<?= isActive('income') ?>">Pemasukan</a>
                    <a href="?page=expenses" class="<?= isActive('expenses') ?>">Pengeluaran</a>
                    <a href="?page=students" class="<?= isActive('students') ?>">Siswa</a>
                    <a href="?page=report" class="<?= isActive('report') ?>">Laporan</a>
                </div>

                <a href="?page=logout" class="btn btn-sm btn-outline btn-logout" style="margin-left: 15px; border-color: #fee2e2; color: #ef4444;">
                    Keluar
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>