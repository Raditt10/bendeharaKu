<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Prefer explicit query error (from redirect), then session-stored error, then any pre-set $error_msg
$error_msg = $_GET['err'] ?? ($_SESSION['error_msg'] ?? ($error_msg ?? null));
if (isset($_SESSION['error_msg'])) unset($_SESSION['error_msg']);
?>

<div class="auth-page">
    <div class="form-box">
        <div class="auth-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>

        <?php if ($error_msg): ?>
            <div class="error-box">
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=login">
            <div class="form-group">
                <label>NIS / Username</label>
                <input type="text" name="nis" placeholder="Contoh: 12345" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">
                Masuk ke Dashboard
            </button>
        </form>

        <p class="auth-help">
            Belum punya akun? <a href="?page=register">Daftar Sekarang</a>
        </p>
    </div>
</div>