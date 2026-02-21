<style>
    /* =========================================
       EDIT PROFILE PAGE STYLES 
       ========================================= */
    .profile-wrapper {
        padding: 40px 20px 80px;
        max-width: 800px;
        margin: 0 auto;
        min-height: calc(100vh - 70px);
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes fadeUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 32px;
        text-align: center;
        letter-spacing: -0.02em;
    }

    .profile-card {
        background: #ffffff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.8);
        position: relative;
    }

    .profile-cover {
        height: 160px;
        background: radial-gradient(circle at top right, #3b82f6, #4f46e5, #312e81);
        position: relative;
        overflow: hidden;
    }

    .profile-cover::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%),
            radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 20%);
        background-size: 30px 30px;
        background-position: 0 0, 15px 15px;
        opacity: 0.3;
        animation: moveBg 20s linear infinite;
    }

    @keyframes moveBg {
        100% {
            transform: translateY(30px);
        }
    }

    .profile-body {
        padding: 0 40px 48px;
        text-align: center;
        margin-top: -65px;
        position: relative;
        z-index: 2;
    }

    .profile-avatar-medium {
        width: 120px;
        height: 120px;
        margin: 0 auto 24px;
        background: #ffffff;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
        position: relative;
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #eef2ff, #c7d2fe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: #4f46e5;
        border: 2px solid #e0e7ff;
    }

    .profile-role-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: #f59e0b;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
    }

    /* Form Styles */
    .edit-profile-form {
        max-width: 500px;
        margin: 0 auto;
        text-align: left;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 16px 20px;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        font-size: 1.05rem;
        color: #0f172a;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #4f46e5;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .form-control:read-only {
        background-color: #f1f5f9;
        color: #64748b;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .form-control:read-only:focus {
        box-shadow: none;
        border-color: #e2e8f0;
    }

    .form-help-text {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 6px;
        display: block;
        font-style: italic;
    }

    .profile-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        padding-top: 32px;
        margin-top: 32px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-profile {
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-save {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        flex: 1;
        justify-content: center;
    }

    .btn-save:hover {
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .btn-cancel {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        flex: 1;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }

    @media (max-width: 768px) {
        .profile-wrapper {
            padding: 20px 16px 80px;
        }

        .profile-card {
            border-radius: 20px;
        }

        .profile-cover {
            height: 130px;
        }

        .profile-avatar-medium {
            width: 100px;
            height: 100px;
        }

        .avatar-inner {
            font-size: 2.5rem;
        }

        .profile-body {
            margin-top: -50px;
            padding: 0 20px 32px;
        }

        .profile-actions {
            flex-direction: column-reverse;
            gap: 12px;
        }

        .btn-profile {
            width: 100%;
            justify-content: center;
            padding: 16px;
        }
    }
</style>

<div class="profile-wrapper">
    <h1 class="profile-header-title">Pengaturan Profil</h1>

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
                    <span class="form-help-text">NIS adalah identitas tetap dan tidak dapat diubah secara mandiri.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" required placeholder="Masukkan nama lengkap Anda">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email (Opsional)</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" placeholder="contoh@smk.belajar.id">
                    <span class="form-help-text">Boleh dikosongkan jika tidak ada.</span>
                </div>

                <div class="profile-actions">
                    <a href="?page=profile" class="btn-profile btn-cancel">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Batalkan
                    </a>

                    <button type="submit" name="simpan_profil" class="btn-profile btn-save">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Profil
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>