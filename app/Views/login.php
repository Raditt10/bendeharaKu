<style>
* { box-sizing: border-box; }
.auth-page { min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
.form-box { background: #fff; border-radius: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); padding: 40px; max-width: 420px; width: 100%; display: flex; flex-direction: column; gap: 20px; border: 1px solid #e2e8f0; }
.auth-header h2 { font-size: 1.75rem; font-weight: 800; margin: 0 0 8px 0; text-align: center; color: #0f172a; line-height: 1.2; }
.auth-header p { color: #64748b; font-size: 1rem; text-align: center; margin: 0 0 24px 0; line-height: 1.5; }
.form-group { margin-bottom: 4px; }
.form-group label { font-weight: 600; font-size: 0.95rem; color: #334155; margin-bottom: 8px; display: block; }
.input-wrapper { position: relative; width: 100%; }
.form-group input { width: 100%; padding: 14px 16px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 1rem; background: #f8fafc; transition: all 0.2s ease; outline: none; color: #1e293b; }
input[type="password"], input.password-shown { padding-right: 45px; }
.form-group input:focus { border-color: #4f46e5; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
.toggle-password { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; display: flex; align-items: center; justify-content: center; transition: color 0.2s; }
.toggle-password:hover { color: #4f46e5; }
.auth-submit { margin-top: 8px; }
.auth-submit .btn-primary { width: 100%; font-size: 1rem; font-weight: 700; border-radius: 12px; padding: 14px 0; background: #4f46e5; color: #fff; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06); }
.auth-submit .btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2); }
.divider { display: flex; align-items: center; text-align: center; margin: 16px 0; color: #94a3b8; font-size: 0.9rem; font-weight: 500; }
.divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e2e8f0; }
.divider:not(:empty)::before { margin-right: 12px; }
.divider:not(:empty)::after { margin-left: 12px; }
.google-btn-wrapper { display: flex; justify-content: center; width: 100%; }
.auth-help { text-align: center; color: #64748b; font-size: 0.95rem; margin-top: 16px; }
.auth-help a { color: #4f46e5; font-weight: 600; text-decoration: none; transition: color 0.2s; }
.auth-help a:hover { color: #3730a3; text-decoration: underline; }
.error-box { background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; border-radius: 12px; padding: 12px 16px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
@media (max-width: 640px) { .auth-page { padding: 16px; align-items: center; } .form-box { padding: 32px 24px; border-radius: 20px; } .auth-header h2 { font-size: 1.5rem; } .form-group input { font-size: 16px; padding: 12px 14px; } }
</style>

<div class="auth-page">
    <div class="form-box">
        <div class="auth-header">
            <h2>Selamat Datang</h2>
            <p>Masuk untuk mengelola keuangan kelas</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="error-box">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=login">
            <div class="form-group">
                <label for="nis">NIS (Nomor Induk Siswa)</label>
                <div class="input-wrapper">
                    <input type="text" id="nis" name="nis" placeholder="Masukkan NIS Anda" pattern="[0-9]+" title="Hanya angka">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="password-input" placeholder="••••••••">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="auth-submit">
                <button type="submit" class="btn-primary">Masuk Dashboard</button>
            </div>
        </form>

        <div class="divider">ATAU</div>

        <div class="google-btn-wrapper">
            <div id="g_id_onload"
                 data-client_id="610440154596-0897o83ukejtnr5qlm20mih02bmman37.apps.googleusercontent.com"
                 data-callback="handleGoogleLogin"
                 data-auto_prompt="false">
            </div>

            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left"
                 data-width="340">
            </div>
        </div>

        <div class="auth-help">
            Belum punya akun? <a href="?page=register">Daftar Sekarang</a>
        </div>
    </div>
</div>

<form id="google-login-form" method="POST" action="?page=login" style="display:none;">
    <input type="hidden" name="credential" id="google-credential">
</form>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        input.classList.add('password-shown');
        btn.style.color = "#4f46e5"; 
    } else {
        input.type = "password";
        input.classList.remove('password-shown');
        btn.style.color = "#94a3b8"; 
    }
}

function handleGoogleLogin(response) {
    document.getElementById('google-credential').value = response.credential;
    document.getElementById('google-login-form').submit();
}
</script>