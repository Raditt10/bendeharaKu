<div class="auth-page">
    <div class="form-box">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="margin:0; font-size: 1.5rem;">Selamat Datang</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Silakan masuk ke akun Anda</p>
        </div>

        <?php if ($error_msg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
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

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                Masuk ke Dashboard
            </button>
        </form>

        <p style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
            Belum punya akun? <a href="?page=register" style="color: var(--primary); font-weight: 600; text-decoration: none;">Daftar Sekarang</a>
        </p>
    </div>
</div>