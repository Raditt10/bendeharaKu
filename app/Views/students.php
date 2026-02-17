<?php
// Pastikan session aktif & cek login
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['nis'])) {
    header("Location: ?page=login");
    exit;
}

// Bersihkan Flash Message
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Search & Pagination
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
    /* Layout Utama */
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

    .page-title h2 { margin: 0; font-size: 1.75rem; color: #1e293b; }
    .page-title p { margin: 5px 0 0; color: #64748b; font-size: 0.95rem; }

    /* Stats Card - Indigo Theme for Users */
    .stats-card {
        /* Hardcoded Gradient agar background pasti muncul */
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
    
    .stats-info { z-index: 2; }
    .stats-info span { display: block; font-size: 1rem; color: rgba(255,255,255,0.9); font-weight: 500; margin-bottom: 8px; }
    .stats-info h3 { margin: 0; font-size: 2.5rem; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; }
    
    .stats-icon { 
        color: rgba(255,255,255,0.15); 
        transform: scale(2) rotate(-10deg);
        position: absolute;
        right: 20px;
        bottom: -10px;
        z-index: 1;
    }

    /* Action Buttons */
    .action-bar { display: flex; gap: 10px; flex-wrap: wrap; }

    /* Table & Card Styles */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    table { width: 100%; border-collapse: collapse; text-align: left; }
    thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    th { padding: 16px 20px; font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 0.95rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background-color: #f8fafc; }

    /* User Avatar & Name Styling */
    .student-profile { display: flex; align-items: center; gap: 12px; }
    /* avatar styles are provided globally in base.css */
    .student-info { display: flex; flex-direction: column; }
    .student-name { font-weight: 600; color: #1e293b; }
    .student-nis { font-size: 0.8rem; color: #64748b; margin-top: 2px; }

    /* Buttons */
    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-edit { background: #eff6ff; color: #2563eb; }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete { background: #fef2f2; color: #ef4444; }
    .btn-delete:hover { background: #fee2e2; }

    /* Alert */
    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        animation: slideIn 0.3s ease;
    }
    .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .close-alert { background: transparent; border: none; cursor: pointer; font-size: 1.2rem; color: inherit; opacity: 0.7; }

    @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* RESPONSIVE (MOBILE) */
    @media (max-width: 768px) {
        .page-header { align-items: flex-start; }
        .stats-card { overflow: hidden; }
        
        thead { display: none; }
        
        tr { display: block; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: white; }
        tr:last-child { margin-bottom: 0; }
        
        td { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding: 12px 0; text-align: right; }
        td:last-child { border-bottom: none; }
        td:first-child { padding-top: 0; }
        
        /* Specific adjustments for profile in mobile */
        .student-profile { flex-direction: row-reverse; text-align: right; }
        .student-info { align-items: flex-end; }
        
        td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            text-align: left;
            margin-right: 15px;
        }
        .action-cell { justify-content: flex-end; gap: 10px; }
    }
</style>

<div class="container mt-12 mb-12">

    <?php if ($success_msg): ?>
    <div class="alert alert-success" id="alert-success">
        <span><?= htmlspecialchars($success_msg) ?></span>
        <button class="close-alert" onclick="document.getElementById('alert-success').style.display='none'">&times;</button>
    </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
    <div class="alert alert-error" id="alert-error">
        <span><?= htmlspecialchars($error_msg) ?></span>
        <button class="close-alert" onclick="document.getElementById('alert-error').style.display='none'">&times;</button>
    </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="page-title">
            <h2>Data Siswa</h2>
            <p>Manajemen data anggota kelas XI RPL 1.</p>
        </div>
        <div class="action-bar">
            <form method="get" action="" style="display:flex; gap:8px; align-items:center;">
                <input type="hidden" name="page" value="students" />
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama atau NIS..." class="form-control" style="width:220px;" />
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="?page=add_student" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Siswa
                </a>
            <?php endif; ?>
            <a href="?page=students" class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Refresh
            </a>
        </div>
    </div>

    <div class="stats-card card">
        <div class="stats-info">
            <span>Total Siswa Terdaftar</span>
            <h3><?= $total_siswa ?> Siswa</h3>
        </div>
        <div class="stats-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
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
                $no = 1;
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Ambil inisial nama untuk avatar
                        $initials = '';
                        $parts = explode(' ', $row['nama']);
                        foreach($parts as $part) { $initials .= strtoupper(substr($part, 0, 1)); }
                        $initials = substr($initials, 0, 2);
                ?>
                <tr>
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="Identitas">
                        <div class="student-profile">
                            <div class="avatar"><?= $initials ?></div>
                            <div class="student-info">
                                <span class="student-name"><?= htmlspecialchars($row['nama']) ?></span>
                                <span class="student-nis">NIS: <?= htmlspecialchars($row['nis']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td data-label="Kontak">
                        <?php if(!empty($row['kontak_orangtua'])): ?>
                            <a href="tel:<?= htmlspecialchars($row['kontak_orangtua']) ?>" style="text-decoration:none; color:#2563eb; font-weight:500;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:4px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <?= htmlspecialchars($row['kontak_orangtua']) ?>
                            </a>
                        <?php else: ?>
                            <span style="color:#94a3b8; font-style:italic;">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                    <td data-label="Aksi" class="action-cell" style="text-align: right;">
                        <a href="?page=edit_student&id=<?= $row['id_siswa'] ?>" class="action-btn btn-edit">
                            Edit
                        </a>
                        <a href="?page=delete_student&id=<?= $row['id_siswa'] ?>" class="action-btn btn-delete" onclick="return confirm('Yakin ingin menghapus data siswa ini? Semua data pembayaran terkait juga akan terhapus.');">
                            Hapus
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php 
                    }
                } else { 
                ?>
                <tr>
                    <td colspan="<?= ($_SESSION['role'] == 'admin' ? 4 : 3) ?>" style="text-align:center; padding: 40px; color: #64748b;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 10px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Belum ada data siswa.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    // Pagination controls
    $totalPages = max(1, (int)ceil($total_siswa / $perPage));
    if ($totalPages > 1):
    ?>
    <div class="pagination mt-12">
        <?php if ($pageNum > 1): ?>
            <a class="page-link" href="?page=students&q=<?= urlencode($q) ?>&p=<?= $pageNum-1 ?>">&laquo; Prev</a>
        <?php endif; ?>

        <div style="display:flex; gap:6px; align-items:center;">
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
        </div>
        <?php if ($pageNum < $totalPages): ?>
            <a class="page-link" href="?page=students&q=<?= urlencode($q) ?>&p=<?= $pageNum+1 ?>">Next &raquo;</a>
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