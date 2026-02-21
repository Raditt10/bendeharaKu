<?php
// Pastikan session aktif & cek login
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['nis'])) {
    header("Location: ?page=login");
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// --- 1. LOGIKA HAPUS DATA (Backend) ---
if (isset($_GET['delete_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['delete_id']);

    // Hapus data iuran yang berelasi dengan siswa ini terlebih dahulu
    mysqli_query($koneksi, "DELETE FROM iuran WHERE id_siswa = '$id_hapus'");

    $deleteQuery = "DELETE FROM siswa WHERE id_siswa = '$id_hapus'";
    if (mysqli_query($koneksi, $deleteQuery)) {
        $_SESSION['success_msg'] = "Data siswa berhasil dihapus.";
    } else {
        $_SESSION['error_msg'] = "Gagal menghapus data siswa: " . mysqli_error($koneksi);
    }

    // Redirect agar URL bersih
    echo "<script>window.location.href='?page=students';</script>";
    exit;
}

// Bersihkan Flash Message
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// --- 2. SEARCH & PAGINATION ---
$q = trim($_GET['q'] ?? '');
$pageNum = max(1, (int)($_GET['p'] ?? 1));
$perPage = 15;
$offset = ($pageNum - 1) * $perPage;

// Prepare filter
$like = '%' . $q . '%';

// Count total with filter
$countStmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM siswa WHERE nama LIKE ? OR nis LIKE ?");
mysqli_stmt_bind_param($countStmt, 'ss', $like, $like);
mysqli_stmt_execute($countStmt);
$countRes = mysqli_stmt_get_result($countStmt);
$total_siswa = 0;
if ($countRes) {
    $total_siswa = mysqli_fetch_assoc($countRes)['total'] ?? 0;
}
mysqli_stmt_close($countStmt);

// Query Data Siswa (with filter + pagination)
$stmt = mysqli_prepare($koneksi, "SELECT * FROM siswa WHERE nama LIKE ? OR nis LIKE ? ORDER BY nama ASC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, 'ssii', $like, $like, $perPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<style>
    /* =========================================
       1. DESKTOP STYLES
       ========================================= */
    .page-header {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (min-width: 768px) {
        .page-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .page-title h2 {
        margin: 0;
        font-size: 1.75rem;
        color: #1e293b;
    }

    .page-title p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    /* Stats Card (Indigo Theme) */
    .stats-card {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .stats-info {
        z-index: 2;
    }

    .stats-info span {
        display: block;
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stats-info h3 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
    }

    .stats-icon {
        color: rgba(255, 255, 255, 0.15);
        transform: scale(2) rotate(-10deg);
        position: absolute;
        right: 20px;
        bottom: -10px;
        z-index: 1;
    }

    /* Table Styles */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    thead {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    th {
        padding: 16px 20px;
        font-weight: 600;
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 0.95rem;
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background-color: #f8fafc;
    }

    /* Avatar & Profile */
    .student-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        background: #e0e7ff;
        color: #4338ca;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid #c7d2fe;
        flex-shrink: 0;
    }

    .student-info {
        display: flex;
        flex-direction: column;
    }

    .student-name {
        font-weight: 600;
        color: #1e293b;
    }

    .student-nis {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* Buttons & Search */
    .action-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .action-bar form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Container Tombol Header (Desktop) */
    .action-bar-header {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #6366f1;
        color: white;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .btn-primary:hover {
        background: #4f46e5;
        transform: translateY(-1px);
    }

    .btn-outline {
        background: white;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .btn-outline:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .search-input {
        padding: 10px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.95rem;
        width: 220px;
        transition: border-color 0.2s;
        height: 42px;
        box-sizing: border-box;
    }

    .search-input:focus {
        border-color: #6366f1;
        outline: none;
    }

    .btn-search-icon {
        width: 42px;
        height: 42px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* --- STYLING TOMBOL AKSI ROW (GAYA BARU) --- */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    /* Warna Edit (Indigo style) */
    .btn-edit-row {
        background: #eef2ff;
        color: #4f46e5;
    }

    .btn-edit-row:hover {
        background: #e0e7ff;
        color: #4338ca;
    }

    /* Warna Hapus (Red style) */
    .btn-del-row {
        background: #fef2f2;
        color: #ef4444;
    }

    .btn-del-row:hover {
        background: #fee2e2;
        color: #b91c1c;
    }


    /* Pagination */
    .pagination {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-top: 20px;
        justify-content: center;
    }

    .page-link {
        padding: 8px 14px;
        border-radius: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .page-link:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .page-link.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }

    /* Alerts Premium */
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        animation: slideDownFade 0.5s ease forwards;
        gap: 12px;
    }

    .alert-success {
        background-color: #ecfdf5;
        border: 1px solid #10b981;
        color: #065f46;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #ef4444;
        color: #991b1b;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }

    .alert-content {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .close-alert {
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 1.5rem;
        color: inherit;
        opacity: 0.6;
        line-height: 1;
        padding: 0 4px;
        transition: opacity 0.2s;
    }

    .close-alert:hover {
        opacity: 1;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =========================================
       2. MOBILE ONLY STYLES (CARD VIEW)
       ========================================= */
    @media (max-width: 768px) {
        .container {
            padding-top: 20px !important;
        }

        .page-header {
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Perbaikan Tombol & Pencarian untuk Mobile */
        .action-bar-header {
            display: block;
            margin-bottom: 0;
        }

        /* Tombol Reset/Refresh disembunyikan agar simpel di mobile */
        .action-bar-header .btn-outline {
            display: none;
        }

        /* Form Cari dibuat Full Width di Mobile */
        .action-bar-header form {
            width: 100%;
            display: flex;
            gap: 8px;
            margin: 0 0 16px 0;
        }

        .search-input {
            flex: 1;
            width: auto;
        }

        /* Tombol Floating Add (Pojok Kanan Bawah) */
        .action-bar-header .btn-primary:not(.btn-search-icon) {
            position: fixed;
            bottom: 24px;
            right: 20px;
            z-index: 1000;
            border-radius: 50px;
            padding: 14px 24px;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            font-size: 1rem;
            white-space: nowrap;
            width: auto;
            transition: none;
        }

        .action-bar {
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .table-card {
            background: transparent;
            box-shadow: none;
            border: none;
        }

        thead {
            display: none;
        }

        tbody {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding-bottom: 80px;
        }

        /* Padding bottom agar tidak ketutup tombol floating */

        tr {
            display: grid;
            grid-template-areas:
                "profile profile"
                "contact contact"
                "actions actions";
            background: #ffffff;
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 18px 0 rgba(60, 72, 100, 0.10);
            border: none;
            position: relative;
            gap: 12px;
        }

        td {
            border-bottom: none;
            padding: 0;
            display: block;
            background: transparent;
        }

        td.td-no {
            display: none;
        }

        td.td-profile {
            grid-area: profile;
            border-bottom: 1px dashed #eef2ff;
            padding-bottom: 12px;
        }

        td.td-contact {
            grid-area: contact;
            font-size: 0.95rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        /* --- STYLE AKSI MOBILE (FULL WIDTH TOMBOL BESAR) --- */
        td.td-action {
            grid-area: actions;
            width: 100%;
            margin-top: 4px;
            padding-top: 0;
            border-top: none;
        }

        .action-btns {
            display: flex;
            width: 100%;
            justify-content: stretch;
            gap: 14px;
        }

        .icon-btn {
            flex: 1;
            /* Melar penuh */
            height: 48px;
            /* Tinggi tombol mobile */
            border-radius: 14px;
            font-size: 1.1rem;
        }

        .icon-btn svg {
            width: 20px;
            height: 20px;
        }
    }
</style>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <?php if ($success_msg): ?>
        <div class="alert alert-success" id="alert-success">
            <div class="alert-content">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= htmlspecialchars($success_msg) ?></span>
            </div>
            <button class="close-alert" onclick="document.getElementById('alert-success').style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-error" id="alert-error">
            <div class="alert-content">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= htmlspecialchars($error_msg) ?></span>
            </div>
            <button class="close-alert" onclick="document.getElementById('alert-error').style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="page-title">
            <h2>Data Siswa</h2>
            <p>Manajemen data anggota kelas XI RPL 1.</p>
        </div>

        <div class="action-bar-header">
            <form method="get" action="" style="display: flex; align-items: center; gap: 8px;">
                <input type="hidden" name="page" value="students" />
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari Nama / NIS..." class="search-input" />
                <button type="submit" class="btn btn-primary btn-search-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="?page=add_student" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Data
                </a>
            <?php endif; ?>
            <?php if (!empty($q)): ?>
                <a href="?page=students" class="btn btn-outline">Reset</a>
            <?php else: ?>
                <a href="?page=students" class="btn btn-outline btn-search-icon" title="Refresh">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 4v6h-6"></path>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-info">
            <span>Total Siswa Terdaftar</span>
            <h3><?= $total_siswa ?> Siswa</h3>
        </div>
        <div class="stats-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Kontak Orang Tua</th>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th style="text-align: right;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Generate Initials
                        $initials = '';
                        $parts = explode(' ', $row['nama']);
                        foreach ($parts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        $initials = substr($initials, 0, 2);
                ?>
                        <tr>
                            <td class="td-no"><?= $no++ ?></td>

                            <td class="td-profile">
                                <div class="student-profile">
                                    <div class="avatar"><?= $initials ?></div>
                                    <div class="student-info">
                                        <span class="student-name"><?= htmlspecialchars($row['nama']) ?></span>
                                        <span class="student-nis">NIS: <?= htmlspecialchars($row['nis']) ?></span>
                                    </div>
                                </div>
                            </td>

                            <td class="td-contact">
                                <?php if (!empty($row['kontak_orangtua'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $row['kontak_orangtua'])) ?>" target="_blank" style="text-decoration:none; color:#4338ca; display:flex; align-items:center; gap:8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        <?= htmlspecialchars($row['kontak_orangtua']) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-style:italic; display:flex; align-items:center; gap:6px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Tidak ada kontak
                                    </span>
                                <?php endif; ?>
                            </td>

                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <td class="td-action">
                                    <div class="action-btns">
                                        <a href="?page=edit_student&id=<?= $row['id_siswa'] ?>" class="icon-btn btn-edit-row" title="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <a href="#" class="icon-btn btn-del-row" title="Hapus" onclick="openWarning('?page=students&delete_id=<?= $row['id_siswa'] ?>'); return false;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="<?= ($_SESSION['role'] == 'admin' ? 4 : 3) ?>" style="text-align:center; padding: 40px; color: #64748b;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 10px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Tidak ditemukan data siswa.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    // Pagination Controls
    $totalPages = max(1, (int)ceil($total_siswa / $perPage));
    if ($totalPages > 1):
    ?>
        <div class="pagination">
            <?php if ($pageNum > 1): ?>
                <a class="page-link" href="?page=students&q=<?= urlencode($q) ?>&p=<?= $pageNum - 1 ?>">&laquo;</a>
            <?php endif; ?>

            <?php
            $start = max(1, $pageNum - 2);
            $end = min($totalPages, $pageNum + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <?php if ($i == $pageNum): ?>
                    <span class="page-link active"><?= $i ?></span>
                <?php else: ?>
                    <a class="page-link" href="?page=students&q=<?= urlencode($q) ?>&p=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pageNum < $totalPages): ?>
                <a class="page-link" href="?page=students&q=<?= urlencode($q) ?>&p=<?= $pageNum + 1 ?>">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 300);
            });
        }, 3000);

        // Floating Button — Footer Awareness (Mobile)
        if (window.innerWidth <= 992) {
            var fabBtn = document.querySelector('.action-bar-header .btn-primary:not(.btn-search-icon)');
            var siteFooter = document.querySelector('.site-footer');
            if (fabBtn && siteFooter) {
                var FAB_GAP = 24;

                function adjustFab() {
                    var footerTop = siteFooter.getBoundingClientRect().top;
                    var overlap = window.innerHeight - footerTop;
                    fabBtn.style.bottom = (overlap > 0 ? overlap + FAB_GAP : FAB_GAP) + 'px';
                }
                window.addEventListener('scroll', adjustFab, {
                    passive: true
                });
                adjustFab();
            }
        }
    });
</script>

<?php
if ($result instanceof mysqli_result) {
    mysqli_free_result($result);
}
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    mysqli_stmt_close($stmt);
}
?>

<?php
// Include warning modal (modal konfirmasi hapus)
$requireWarning = __DIR__ . '/partials/warning.php';
if (file_exists($requireWarning)) require $requireWarning;
?>