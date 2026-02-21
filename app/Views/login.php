<style>
    /* Container Utama */
    .auth-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .form-box {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 420px;
        width: 100%;
        border: 1px solid #e2e8f0;
        animation: fadeInAuth 0.5s ease-out;
    }

    /* CSS NOTIFIKASI ERROR */
    .error-box {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #ef4444;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        animation: shake 0.4s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-6px);
        }

        75% {
            transform: translateX(6px);
        }
    }

    .auth-header h2 {
        font-size: 1.75rem;
        font-weight: 800;
        text-align: center;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .auth-header p {
        color: #64748b;
        text-align: center;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #334155;
        display: block;
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
    }

    .form-group input {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 1rem;
        transition: all 0.2s;
        outline: none;
    }

    .form-group input:focus {
        border-color: #4f46e5;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .toggle-password {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        background: #4338ca;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 20px 0;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .divider::before {
        margin-right: 12px;
    }

    .divider::after {
        margin-left: 12px;
    }

    .auth-help {
        text-align: center;
        margin-top: 20px;
        font-size: 0.9rem;
        color: #64748b;
    }

    .auth-help a {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    /* Google Sign-In Button */
    .google-btn-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 480px) {
        .form-box {
            padding: 28px 20px;
        }
    }
</style>

<div class="auth-page">
    <div class="form-box">
        <div class="auth-header">
            <h2>Selamat Datang</h2>
            <p>Masuk untuk mengelola keuangan kelas</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="error-box" id="errorNotification">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
            <script>
                // Hilangkan notifikasi otomatis setelah 5 detik
                setTimeout(() => {
                    const err = document.getElementById('errorNotification');
                    if (err) {
                        err.style.opacity = '0';
                        err.style.transform = 'translateY(-10px)';
                        err.style.transition = 'all 0.5s ease';
                        setTimeout(() => err.remove(), 500);
                    }
                }, 5000);
            </script>
        <?php endif; ?>

        <form method="POST" action="?page=login">
            <div class="form-group">
                <label for="nis">NIS (Nomor Induk Siswa)</label>
                <input type="text" id="nis" name="nis" placeholder="Masukkan NIS Anda" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePass()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Masuk Dashboard</button>
        </form>

        <div class="divider">ATAU</div>

        <div class="google-btn-wrapper">
            <div id="g_id_onload"
                data-client_id="610440154596-0897o83ukejtnr5qlm20mih02bmman37.apps.googleusercontent.com"
                data-callback="handleGoogleLogin">
            </div>
            <div class="g_id_signin" data-type="standard" data-theme="outline" data-size="large"></div>
        </div>

        <div class="auth-help">
            Belum punya akun? <a href="?page=register">Daftar Sekarang</a>
        </div>
    </div>
</div>

<form id="google-login-form" method="POST" action="?page=login" style="display:none;">
    <input type="hidden" name="credential" id="google-credential">
</form>

<script>
    // Set Google button width dynamically to fit container
    (function() {
        function setGoogleBtnWidth() {
            var wrapper = document.querySelector('.google-btn-wrapper');
            var btn = document.querySelector('.g_id_signin');
            if (wrapper && btn) {
                var w = Math.min(340, wrapper.clientWidth);
                btn.setAttribute('data-width', w);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setGoogleBtnWidth);
        } else {
            setGoogleBtnWidth();
        }
    })();
</script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
    function togglePass() {
        const p = document.getElementById('password');
        p.type = p.type === 'password' ? 'text' : 'password';
    }

    function handleGoogleLogin(response) {
        document.getElementById('google-credential').value = response.credential;
        document.getElementById('google-login-form').submit();
    }
</script>