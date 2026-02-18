<?php
// Cek Login (Pastikan session sudah ada dari index.php)
if (!isset($_SESSION['nis'])) {
    echo "<script>window.location='?page=login';</script>";
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// --- 1. LOGIC PHP & QUERY ---

// Flash Messages
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Ambil Data Bulan
$bulanQuery = "SELECT DISTINCT bulan FROM iuran ORDER BY bulan ASC";
$bulanResult = mysqli_query($koneksi, $bulanQuery);
$bulanData = [];
while ($row = mysqli_fetch_assoc($bulanResult)) {
    $bulanData[] = $row['bulan'];
}

// Setup Variables
$status_filter = $_GET['status'] ?? '';
$bulan_filter = $_GET['bulan'] ?? '';
$nama_siswa_filter = isset($_GET['nama']) ? trim($_GET['nama']) : '';

// Query Utama
$query = "SELECT i.id_iuran, i.bulan, s.nama AS nama, s.nis,
                 i.minggu_1, i.tgl_bayar_minggu_1,
                 i.minggu_2, i.tgl_bayar_minggu_2,
                 i.minggu_3, i.tgl_bayar_minggu_3,
                 i.minggu_4, i.tgl_bayar_minggu_4
          FROM iuran i
          JOIN siswa s ON i.id_siswa = s.id_siswa";

$filters = [];

if (!empty($status_filter)) {
    $filters[] = "(i.minggu_1 = '$status_filter' OR i.minggu_2 = '$status_filter' OR i.minggu_3 = '$status_filter' OR i.minggu_4 = '$status_filter')";
}
if (!empty($bulan_filter)) {
    $filters[] = "i.bulan = '$bulan_filter'";
}
if (!empty($nama_siswa_filter)) {
    $filters[] = "s.nama LIKE '%" . mysqli_real_escape_string($koneksi, $nama_siswa_filter) . "%'";
}
if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

$query .= " ORDER BY i.bulan DESC, s.nama ASC"; 
$result = mysqli_query($koneksi, $query);
?>

<style>
    /* Layout Wrapper */
    .report-header {
        margin-bottom: 32px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .report-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .report-desc {
        color: var(--text-tertiary);
        font-size: 0.95rem;
        margin: 0;
    }

    /* --- CUSTOM FILTER COMPONENT --- */
    .filter-wrapper {
        background: white;
        padding: 20px;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 24px;
    }

    /* 1. Custom Search Input */
    .search-group {
        flex: 2;
        min-width: 250px;
        position: relative;
    }
    .search-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--gray-400); pointer-events: none;
    }
    .custom-input {
        width: 100%;
        padding: 10px 14px 10px 42px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-lg);
        font-size: 0.95rem;
        outline: none;
        transition: var(--transition-base);
        color: var(--text-primary);
        background: white;
        font-family: inherit;
    }
    .custom-input:focus { 
        border-color: var(--primary-500); 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); 
    }

    /* 2. Custom Dropdown */
    .custom-select-container {
        position: relative;
        flex: 1;
        min-width: 180px;
        user-select: none;
        font-family: inherit;
    }
    .custom-select-trigger {
        position: relative;
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px;
        background: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-lg);
        cursor: pointer;
        font-size: 0.95rem;
        color: var(--text-primary);
        transition: var(--transition-base);
    }
    .custom-select-trigger:hover { border-color: var(--primary-400); }
    .custom-select-container.open .custom-select-trigger { 
        border-color: var(--primary-500); 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); 
    }
    
    .arrow { transition: transform 0.2s ease; width: 16px; height: 16px; color: var(--gray-400); }
    .custom-select-container.open .arrow { transform: rotate(180deg); }

    .custom-options {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0;
        background: white; 
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg); 
        border: 1px solid var(--border-light);
        z-index: 50;
        opacity: 0; visibility: hidden; transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 250px; overflow-y: auto;
    }
    .custom-select-container.open .custom-options { opacity: 1; visibility: visible; transform: translateY(0); }
    
    .custom-option {
        padding: 10px 14px; cursor: pointer; font-size: 0.9rem; color: var(--text-secondary);
        transition: background 0.1s;
    }
    .custom-option:hover { background: var(--primary-50); color: var(--primary-700); }
    .custom-option.selected { background: var(--primary-50); color: var(--primary-700); font-weight: 600; }

    /* Buttons inside filter (Reset) */
    .btn-action-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-icon-wrapper { display: flex; align-items: center; gap: 8px; }

    /* --- FLOATING ACTION BUTTON (NEW STYLE LIKE EXPENSES) --- */
    .action-bar { 
        display: flex; 
        justify-content: flex-end; 
        margin-bottom: 24px; 
    }

    /* --- TABLE STYLING --- */
    .table-container {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    thead { background: var(--gray-50); border-bottom: 1px solid var(--border-light); }
    th { 
        padding: 16px; text-align: left; 
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; 
        color: var(--text-tertiary); font-weight: 700; 
    }
    td { 
        padding: 16px; border-bottom: 1px solid var(--gray-100); 
        font-size: 0.95rem; color: var(--text-secondary); vertical-align: middle; 
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--gray-50); }

    /* Status Badges */
    .badge {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 4px 10px; border-radius: 99px; 
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .badge-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-pending { background: var(--gray-100); color: var(--gray-500); border: 1px solid var(--gray-200); }
    
    .date-sub { display: block; font-size: 0.7rem; color: var(--gray-400); margin-top: 4px; font-weight: 500; text-align: center; }

    /* Action Buttons (Row) */
    .action-btns { display: flex; gap: 8px; justify-content: flex-end; }
    .icon-btn {
        width: 32px; height: 32px; border-radius: var(--radius-md); 
        display: flex; align-items: center; justify-content: center;
        transition: var(--transition-base); border: none; cursor: pointer;
    }
    .btn-edit-row { background: var(--primary-50); color: var(--primary-600); } 
    .btn-edit-row:hover { background: var(--primary-100); color: var(--primary-700); }
    
    .btn-del-row { background: #fef2f2; color: #ef4444; } 
    .btn-del-row:hover { background: #fee2e2; color: #b91c1c; }

    /* Alert */
    .custom-alert {
        padding: 16px; border-radius: var(--radius-lg); margin-bottom: 24px; 
        display: flex; justify-content: space-between; align-items: center; 
        font-size: 0.9rem; font-weight: 500;
        animation: slideDown 0.4s ease;
    }
    .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    /* --- MOBILE RESPONSIVE --- */
    @media (max-width: 992px) {
        .filter-wrapper { flex-direction: column; align-items: stretch; gap: 12px; }
        .search-group, .custom-select-container, .btn-action-group { width: 100%; min-width: 0; }
        .btn { width: 100%; }

        /* MODIFIKASI TOMBOL FLOATING SEPERTI EXPENSE */
        .action-bar {
            position: fixed; 
            bottom: 20px; 
            right: 20px; 
            z-index: 90; 
            justify-content: flex-end; 
            pointer-events: none; /* Container tidak menghalangi klik */
            margin-bottom: 0;
        }
        .action-bar .btn-primary {
            pointer-events: auto; /* Tombol bisa diklik */
            border-radius: 50px; 
            padding: 14px 24px; 
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4); 
            font-size: 1rem;
            width: auto; /* Jangan full width, ikuti konten */
        }
        
        /* Table to Cards */
        thead { display: none; }
        .table-container { background: transparent; border: none; box-shadow: none; }
        tbody { display: grid; gap: 16px; grid-template-columns: 1fr; padding-bottom: 80px; } /* Padding bottom agar tidak tertutup tombol floating */
        
        tr {
            background: #fff;
            border-radius: 18px;
            padding: 24px 18px 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            box-shadow: 0 4px 18px 0 rgba(60,72,100,0.10), 0 1.5px 4px 0 rgba(60,72,100,0.06);
            border: none;
            position: relative;
            margin-bottom: 10px;
        }

        td { 
            padding: 0; 
            border: none; 
            background: transparent !important; 
            display: block; 
        }
        td:nth-child(1) { display: none; } /* No */

        /* Header Card (Nama & Bulan) */
        td:nth-child(2) {
            font-size: 1.18rem;
            font-weight: 800;
            color: #222;
            order: 1;
            margin-bottom: 2px;
        }
        td:nth-child(3) {
            font-size: 1.02rem;
            color: #5b21b6;
            font-weight: 700;
            order: 2;
            margin-bottom: 12px;
        }

        /* Grid Status Mingguan */
        .mobile-grid-container {
            order: 3;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            background: #f3f4f6;
            padding: 16px 10px;
            border-radius: 12px;
            border: none;
            margin-bottom: 10px;
        }
        .grid-item {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: center;
        }
        .grid-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.01em;
        }

        /* Actions */
        td:last-child {
            order: 4;
            margin-top: 10px;
            border-top: none;
            padding-top: 0;
        }
        .action-btns {
            justify-content: stretch;
            width: 100%;
            gap: 14px;
        }
        .icon-btn {
            flex: 1;
            height: 48px;
            border-radius: 14px;
            font-size: 1.1rem;
        }

        .desktop-cell { display: none !important; }
    }
</style>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <?php if ($success_msg): ?>
        <div class="custom-alert alert-success" id="alert-box">
            <span><?= htmlspecialchars($success_msg) ?></span>
            <button onclick="document.getElementById('alert-box').remove()" style="background:none;border:none;cursor:pointer;font-size:1.2rem;opacity:0.6;">&times;</button>
        </div>
    <?php endif; ?>

    <div class="report-header">
        <h2 class="report-title">Laporan Kas</h2>
        <p class="report-desc">Rekapitulasi pembayaran iuran siswa kelas.</p>
    </div>

    <form method="GET" id="filterForm">
        <input type="hidden" name="page" value="report">
        
        <div class="filter-wrapper">
            <div class="search-group">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="nama" class="custom-input" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($nama_siswa_filter) ?>" onblur="document.getElementById('filterForm').submit()">
            </div>

            <div class="custom-select-container" id="bulanSelect">
                <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan_filter) ?>">
                <div class="custom-select-trigger">
                    <span class="selected-text">
                        <?php
                        if ($bulan_filter) {
                            $namaBulan = preg_split('/\s+/', $bulan_filter)[0];
                            echo htmlspecialchars($namaBulan);
                        } else {
                            echo 'Semua Bulan';
                        }
                        ?>
                    </span>
                    <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div class="custom-options">
                    <div class="custom-option <?= $bulan_filter == '' ? 'selected' : '' ?>" data-value="">Semua Bulan</div>
                    <?php foreach($bulanData as $b): ?>
                        <?php $namaBulan = preg_split('/\s+/', $b)[0]; ?>
                        <div class="custom-option <?= $bulan_filter == $b ? 'selected' : '' ?>" data-value="<?= $b ?>"><?= htmlspecialchars($namaBulan) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="custom-select-container" id="statusSelect">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                <div class="custom-select-trigger">
                    <span class="selected-text">
                        <?= $status_filter == 'lunas' ? 'Status: Lunas' : ($status_filter == 'belum' ? 'Status: Belum' : 'Semua Status') ?>
                    </span>
                    <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div class="custom-options">
                    <div class="custom-option <?= $status_filter == '' ? 'selected' : '' ?>" data-value="">Semua Status</div>
                    <div class="custom-option <?= $status_filter == 'lunas' ? 'selected' : '' ?>" data-value="lunas">Lunas</div>
                    <div class="custom-option <?= $status_filter == 'belum' ? 'selected' : '' ?>" data-value="belum">Belum Lunas</div>
                </div>
            </div>

            <div class="btn-action-group">
                <button type="button" class="btn btn-outline" onclick="window.location='?page=report'">
                    <span class="btn-icon-wrapper">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                        Reset
                    </span>
                </button>
            </div>
        </div>
    </form>
    
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <div class="action-bar">
        <a href="?page=add_dues" class="btn btn-primary">
            <span class="btn-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Data Baru
            </span>
        </a>
    </div>
    <?php endif; ?>

        <?php
        // Ambil nama bulan aktif dari filter, jika ada
        $keteranganBulan = '';
        if ($bulan_filter) {
            $keteranganBulan = htmlspecialchars($bulan_filter);
        }
        ?>

        <!-- Keterangan Bulan Aktif (Mobile Only) -->
        <?php if ($keteranganBulan): ?>
        <div class="bulan-keterangan-mobile">
            <?= $keteranganBulan ?>
        </div>
        <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Bulan</th>
                    <th style="text-align:center;">M1</th>
                    <th style="text-align:center;">M2</th>
                    <th style="text-align:center;">M3</th>
                    <th style="text-align:center;">M4</th>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th style="text-align:right;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="font-weight: 600; color: var(--text-primary);">
                        <?= htmlspecialchars($row['nama']) ?>
                        <div class="mobile-grid-container" style="margin-top:14px; margin-bottom:0; display:grid;">
                            <?php for($i=1; $i<=4; $i++): 
                                $status = $row["minggu_$i"];
                            ?>
                            <div class="grid-item">
                                <span class="grid-label">M<?= $i ?></span>
                                <span class="badge <?= $status == 'lunas' ? 'badge-success' : 'badge-pending' ?>">
                                    <?= $status == 'lunas' ? '✓' : '-' ?>
                                </span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td>
                        <?php $bulanSplit = preg_split('/\s+/', $row['bulan']); echo htmlspecialchars($bulanSplit[0]); ?>
                    </td>
                    <?php for($i=1; $i<=4; $i++): 
                        $status = $row["minggu_$i"];
                        $tgl = $row["tgl_bayar_minggu_$i"];
                        $isLunas = ($status == 'lunas');
                    ?>
                        <td class="desktop-cell" style="text-align:center;">
                            <span class="badge <?= $isLunas ? 'badge-success' : 'badge-pending' ?>">
                                <?= $isLunas ? 'Lunas' : '-' ?>
                            </span>
                            <?php if($isLunas && $tgl): ?>
                                <span class="date-sub"><?= date('d/m', strtotime($tgl)) ?></span>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                    <td>
                        <div class="action-btns">
                            <a href="?page=edit_report&id_iuran=<?= $row['id_iuran'] ?>" class="icon-btn btn-edit-row" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>
                            <a href="#" class="icon-btn btn-del-row" title="Hapus" onclick="openWarning('?page=report&delete_id=<?= $row['id_iuran'] ?>'); return false;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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
                    <td colspan="8" style="padding: 60px 0; text-align: center;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:12px; opacity:0.7;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span style="color: var(--text-tertiary);">Data tidak ditemukan</span>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/partials/warning.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Setup Custom Dropdowns
    function setupDropdown(id) {
        const container = document.getElementById(id);
        if (!container) return;

        const trigger = container.querySelector('.custom-select-trigger');
        const options = container.querySelectorAll('.custom-option');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const displaySpan = container.querySelector('.selected-text');

        // Toggle Open
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.custom-select-container').forEach(el => {
                if (el !== container) el.classList.remove('open');
            });
            container.classList.toggle('open');
        });

        // Option Click
        options.forEach(option => {
            option.addEventListener('click', function() {
                container.querySelector('.selected').classList.remove('selected');
                this.classList.add('selected');
                
                const value = this.getAttribute('data-value');
                const text = this.textContent;
                
                hiddenInput.value = value;
                displaySpan.textContent = text;
                
                container.classList.remove('open');
                document.getElementById('filterForm').submit();
            });
        });
    }

    setupDropdown('bulanSelect');
    setupDropdown('statusSelect');

    // 2. Click Outside to Close
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-container')) {
            document.querySelectorAll('.custom-select-container').forEach(el => {
                el.classList.remove('open');
            });
        }
    });

    // 3. Mobile Grid Check
    const checkMobile = () => {
        const isMobile = window.innerWidth <= 992;
        document.querySelectorAll('.mobile-grid-container').forEach(grid => {
            grid.style.display = isMobile ? 'grid' : 'none';
        });
    };
    
    window.addEventListener('resize', checkMobile);
    checkMobile(); 
});
</script>