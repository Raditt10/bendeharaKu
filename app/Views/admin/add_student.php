<?php
// ==========================================
// 1. CONFIG & SESSION & LOGIC (BACKEND)
// ==========================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/app/Models/Database.php';

// Validasi Role (Hanya Admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='?page=students';</script>";
    exit;
}

$conn = Database::getInstance()->getConnection();
$error = '';

// 2. PROSES SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data & Sanitasi
    $nis = mysqli_real_escape_string($conn, trim($_POST['nis'] ?? ''));
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak'] ?? ''));

    // Ubah format kontak 08xx jadi 628xx
    if (substr($kontak, 0, 1) === '0') {
        $kontak = '62' . substr($kontak, 1);
    }

    // Validasi Input
    if (empty($nis) || empty($nama)) {
        $error = 'NIS dan Nama Siswa wajib diisi.';
    } else {
        // Cek Duplikasi NIS
        $cekQuery = "SELECT id_siswa FROM siswa WHERE nis = '$nis'";
        if (mysqli_num_rows(mysqli_query($conn, $cekQuery)) > 0) {
            $error = "NIS '$nis' sudah terdaftar! Gunakan NIS lain.";
        } else {
            // Insert Data
            $sql = "INSERT INTO siswa (nis, nama, kontak_orangtua) VALUES ('$nis', '$nama', '$kontak')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['success_msg'] = 'Data siswa berhasil ditambahkan.';
                echo "<script>window.location='?page=students';</script>";
                exit;
            } else {
                $error = 'Terjadi kesalahan sistem: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* GLOBAL RESET */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* LAYOUT WRAPPER */
        .form-page-wrapper {
            padding: 100px 20px 80px;
            max-width: 700px;
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

        /* BACK BUTTON */
        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-weight: 700;
            text-decoration: none;
            margin-bottom: 24px;
            transition: color 0.2s ease;
            font-size: 0.95rem;
        }

        .btn-back-link:hover {
            color: #4f46e5;
        }

        /* MAIN CARD */
        .premium-card {
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .card-cover {
            height: 120px;
            background: radial-gradient(circle at top right, #3b82f6, #4f46e5, #312e81);
            position: relative;
            overflow: hidden;
        }

        .card-cover::before {
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

        .card-body {
            padding: 0 40px 40px;
            margin-top: -50px;
            position: relative;
            z-index: 2;
        }

        .icon-header-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: #ffffff;
            border-radius: 50%;
            padding: 6px;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-header-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #eef2ff, #c7d2fe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 8px 0;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            margin: 0 0 32px 0;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        /* Input with Icon */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            font-size: 1.05rem;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #4f46e5;
            outline: none;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 32px;
        }

        .btn-action {
            flex: 1;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            border: none;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-cancel:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-save {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }

        .btn-save:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-left: 5px solid #ef4444;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-page-wrapper {
                padding: 20px 16px 80px;
            }

            .premium-card {
                border-radius: 20px;
            }

            .card-body {
                padding: 0 24px 32px;
            }

            .form-actions {
                flex-direction: column-reverse;
                gap: 12px;
            }

            .btn-action {
                width: 100%;
                padding: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="form-page-wrapper">

        <div class="premium-card">
            <div class="card-cover"></div>

            <div class="card-body">
                <div class="icon-header-wrapper">
                    <div class="icon-header-inner">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </div>
                </div>

                <h2 class="form-title">Tambah Siswa Baru</h2>
                <p class="form-subtitle">Masukkan data identitas siswa kelas XI RPL 1</p>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="formStudent">

                    <div class="form-group">
                        <label class="form-label">NIS (Nomor Induk Siswa)</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <input type="number" name="nis" class="form-input" placeholder="Contoh: 232410" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input type="text" name="nama" class="form-input" placeholder="Nama lengkap sesuai absensi" required autocomplete="off" style="text-transform: capitalize;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. HP / WhatsApp Orang Tua</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </span>
                            <input type="number" name="kontak" class="form-input" placeholder="08..." autocomplete="off">
                        </div>
                        <small style="color:#94a3b8; font-size:0.85rem; margin-top:6px; display:block;">*Opsional, boleh dikosongkan. Sistem akan menyesuaikan awalan angka.</small>
                    </div>

                    <div class="form-actions">
                        <a href="?page=students" class="btn-action btn-cancel">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Batalkan
                        </a>
                        <button type="submit" class="btn-action btn-save">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Simpan Data
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>