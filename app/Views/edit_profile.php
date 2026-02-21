<style>
    /* =========================================
       EDIT PROFILE PAGE STYLES 
       ========================================= */
    .profile-wrapper {
        padding: 40px 20px 80px;
        max-width: 800px;
        margin: 0 auto;
        min-height: calc(100vh - 70px);
    }

    .profile-header-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main, #0f172a);
        margin-bottom: 24px;
        text-align: center;
    }

    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        position: relative;
    }

    .profile-cover {
        height: 140px; 
        background: linear-gradient(135deg, #2563eb 0%, #6366f1 100%);
        position: relative;
    }
    .profile-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.6;
    }

    .profile-body {
        padding: 0 32px 32px;
        text-align: center;
        margin-top: -50px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar-medium {
        width: 100px;
        height: 100px;
        margin: 0 auto 24px;
        background: #ffffff;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #e0e7ff, #eef2ff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        color: #4f46e5;
        border: 2px solid #e2e8f0;
    }

    /* Form Styles */
    .edit-profile-form {
        max-width: 500px;
        margin: 0 auto;
        text-align: left;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        font-size: 1rem;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #4f46e5;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .form-control:read-only {
        background-color: #e2e8f0;
        color: #64748b;
        cursor: not-allowed;
    }

    .profile-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        padding-top: 32px;
    }

    .btn-profile {
        padding: 14px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .btn-save {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    .btn-save:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    @media (max-width: 768px) {
        .profile-wrapper { padding: 20px 16px 80px; }
        .profile-actions { flex-direction: column; }
        .btn-profile { width: 100%; justify-content: center; }
    }
</style>

<div class="profile-wrapper">
    <h1 class="profile-header-title">Edit Profil</h1>

    <div class="profile-card">
        <div class="profile-cover"></div>

        <div class="profile-body">
            <div class="profile-avatar-medium">
                <div class="avatar-inner">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                </div>
            </div>

            <form action="?page=edit_profile" method="POST" class="edit-profile-form">
                
                <div class="form-group">
                    <label class="form-label" for="nis">Nomor Induk Siswa (NIS)</label>
                    <input type="text" id="nis" class="form-control" value="<?= htmlspecialchars($_SESSION['nis'] ?? '') ?>" readonly>
                    <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 4px; display: block;">NIS tidak dapat diubah.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" required placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required placeholder="contoh@smk.belajar.id">
                </div>

                <div class="profile-actions">
                    <a href="?page=profile" class="btn-profile btn-cancel">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Batal
                    </a>

                    <button type="submit" name="simpan_profil" class="btn-profile btn-save">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>