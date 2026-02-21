<?php
// 1. Logic Hapus Data (Backend)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    require_once __DIR__ . '/../Models/Database.php';
    $koneksi = Database::getInstance()->getConnection();
    $id = (int)$_GET['id'];
    $query = "DELETE FROM pemasukan WHERE id_pemasukan = '$id'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success_msg'] = 'Data pemasukan berhasil dihapus.';
    } else {
        $_SESSION['error_msg'] = 'Gagal menghapus data pemasukan.';
    }
    header('Location: ?page=income');
    exit;
}

// 2. Setup & Flash Messages
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// 3. Filter Tahun
$tahunList = [];
$tahunQ = mysqli_query($koneksi, "SELECT DISTINCT tahun FROM pemasukan ORDER BY tahun DESC");
while ($t = mysqli_fetch_assoc($tahunQ)) {
    if (!empty($t['tahun'])) $tahunList[] = $t['tahun'];
}

$tahun_aktif = $tahunList[0] ?? date('Y');
if (isset($_GET['tahun']) && $_GET['tahun'] !== '') {
    $tahun_aktif = $_GET['tahun'];
}

// 4. Query Data
$query = "SELECT * FROM pemasukan WHERE tahun = '" . mysqli_real_escape_string($koneksi, $tahun_aktif) . "' ORDER BY id_pemasukan DESC";
$result = mysqli_query($koneksi, $query);

// 5. Hitung Total
$total_pemasukan = 0;
$filteredRows = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $filteredRows[] = $row;
        $total_pemasukan += $row['jumlah'];
    }
}
?>

<style>
    /* --- CSS GLOBAL (Desktop Basis) --- */
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

    /* Stats Card */
    .stats-card {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: #ffffff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
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

    /* Filter Bar (Desktop) */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        background: #f8fafc;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.04);
        width: fit-content;
    }

    .filter-label {
        font-weight: 600;
        color: #2563eb;
        font-size: 1rem;
        margin-right: 2px;
    }

    .custom-dropdown {
        position: relative;
        min-width: 110px;
        user-select: none;
    }

    .custom-dropdown-selected {
        font-weight: 600;
        color: #2563eb;
        background: #fff;
        border: 2px solid #2563eb;
        border-radius: 8px;
        padding: 8px 36px 8px 16px;
        font-size: 1.08rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .custom-dropdown-list {
        position: absolute;
        top: 110%;
        left: 0;
        right: 0;
        background: #fff;
        border: 2px solid #2563eb;
        border-radius: 0 0 10px 10px;
        z-index: 10;
        max-height: 220px;
        overflow-y: auto;
        display: none;
    }

    .custom-dropdown-item {
        padding: 10px 18px;
        font-size: 1.05rem;
        color: #2563eb;
        font-weight: 600;
        cursor: pointer;
    }

    .custom-dropdown-item:hover {
        background: #2563eb;
        color: #fff;
    }

    .custom-dropdown.open .custom-dropdown-list {
        display: block;
    }

    .custom-dropdown-arrow {
        margin-left: 10px;
    }

    /* Buttons Header */
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
    }

    .btn-primary {
        background: #2563eb;
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-primary:hover {
        background: #1d4ed8;
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

    /* Table Styles (Desktop) */
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

    /* --- STYLING TOMBOL AKSI (GAYA REPORT) --- */
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

    /* --- MOBILE APP UI TRANSFORMATION --- */
    @media (max-width: 768px) {
        .container {
            padding-top: 20px !important;
        }

        .page-header {
            gap: 15px;
            margin-bottom: 20px;
        }

        .page-title {
            width: 100%;
        }

        /* Filter Wrapper */
        .filter-wrapper {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            width: 100%;
            margin-top: 10px !important;
        }

        /* Filter Bar Compact */
        .filter-bar {
            flex: 1;
            min-width: 0;
            margin-bottom: 0;
            padding: 8px 12px;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .filter-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-right: 0;
        }

        .custom-dropdown {
            width: 100%;
        }

        .custom-dropdown-selected {
            width: 100%;
            box-sizing: border-box;
            padding: 4px 0;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            color: #0f172a;
            justify-content: space-between;
        }

        /* Tombol Floating Add */
        .action-bar-header {
            position: fixed;
            bottom: 24px;
            right: 20px;
            left: auto;
            z-index: 1000;
            justify-content: flex-end;
            pointer-events: none;
            width: auto;
            transition: none;
        }

        .action-bar-header .btn-primary {
            pointer-events: auto;
            border-radius: 50px;
            padding: 14px 24px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            font-size: 1rem;
            white-space: nowrap;
        }

        .action-bar-header .btn-outline {
            display: none;
        }

        /* Table to Cards Transformation */
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

        /* Layout Grid Kartu Pemasukan Mobile */
        tr {
            display: grid;
            grid-template-areas:
                "month amount"
                "desc desc"
                "actions actions";
            grid-template-columns: 1fr auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 18px 0 rgba(60, 72, 100, 0.10);
            border: none;
            gap: 12px;
        }

        td.td-no {
            display: none;
        }

        td {
            border-bottom: none;
            padding: 0;
            display: block;
            background: transparent;
        }

        /* Bulan (Top Left) */
        td.td-month {
            grid-area: month;
            font-size: 1.1rem;
            color: #1e293b;
            font-weight: 800;
            margin-bottom: 2px;
        }

        /* Nominal (Top Right) */
        td.td-amount {
            grid-area: amount;
            text-align: right;
            font-size: 1rem;
            font-weight: 700;
            color: #10b981 !important;
        }

        /* Keterangan (Middle) */
        td.td-desc {
            grid-area: desc;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 4px;
        }

        /* --- STYLE AKSI MOBILE (FULL WIDTH TOMBOL BESAR) --- */
        td.td-action {
            grid-area: actions;
            width: 100%;
            margin-top: 10px;
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
            <h2>Data Pemasukan</h2>
            <p>Kelola uang kas masuk dan donasi kelas.</p>

            <div class="filter-wrapper" style="margin-top: 18px; display: flex; gap: 12px; flex-wrap: wrap;">
                <?php if (count($tahunList)): ?>
                    <form method="GET" action="" class="filter-bar" id="customYearForm">
                        <input type="hidden" name="page" value="income">
                        <label class="filter-label">Tahun</label>
                        <div class="custom-dropdown" id="customDropdown">
                            <div class="custom-dropdown-selected" id="dropdownSelected">
                                <?= htmlspecialchars($tahun_aktif) ?>
                                <span class="custom-dropdown-arrow">&#9662;</span>
                            </div>
                            <div class="custom-dropdown-list" id="dropdownList">
                                <?php foreach ($tahunList as $th): ?>
                                    <div class="custom-dropdown-item<?= $th == $tahun_aktif ? ' selected' : '' ?>" data-value="<?= $th ?>"><?= $th ?></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="tahun" id="dropdownInput" value="<?= htmlspecialchars($tahun_aktif) ?>">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="action-bar-header">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="?page=add_income" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Catat Pemasukan
                </a>
            <?php endif; ?>
            <a href="?page=income" class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px">
                    <path d="M23 4v6h-6"></path>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                </svg>
                Refresh
            </a>
        </div>
    </div>

    <script>
        const dropdown = document.getElementById('customDropdown');
        const selected = document.getElementById('dropdownSelected');
        const list = document.getElementById('dropdownList');
        const input = document.getElementById('dropdownInput');
        const form = document.getElementById('customYearForm');

        if (dropdown) {
            selected.onclick = function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            };
            document.addEventListener('click', function() {
                dropdown.classList.remove('open');
            });
            list.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
                item.onclick = function(e) {
                    e.stopPropagation();
                    input.value = this.dataset.value;
                    dropdown.classList.remove('open');
                    form.submit();
                };
            });
        }
    </script>

    <div class="stats-card">
        <div class="stats-info">
            <span>Total Dana Terkumpul</span>
            <h3>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
        </div>
        <div class="stats-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Bulan</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <th style="text-align: right;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (count($filteredRows)) {
                    foreach ($filteredRows as $row) {
                ?>
                        <tr>
                            <td class="td-no"><?= $no++ ?></td>
                            <td class="td-month">
                                <?= htmlspecialchars($row['bulan']) ?>
                            </td>
                            <td class="td-amount" style="color: #10b981; font-weight: 700;">
                                + Rp <?= number_format($row['jumlah'], 2, ',', '.') ?>
                            </td>
                            <td class="td-desc"><?= htmlspecialchars($row['keterangan']) ?></td>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <td class="td-action">
                                    <div class="action-btns">
                                        <a href="?page=edit_income&id=<?= $row['id_pemasukan'] ?>" class="icon-btn btn-edit-row" title="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <a href="#" class="icon-btn btn-del-row" title="Hapus" onclick="openWarning('?page=income&id=<?= $row['id_pemasukan'] ?>'); return false;">
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
                        <td colspan="5" style="text-align:center; padding: 40px; color: #64748b;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 10px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Belum ada data pemasukan.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/partials/warning.php'; ?>
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
            var fabEl = document.querySelector('.action-bar-header');
            var siteFooter = document.querySelector('.site-footer');
            if (fabEl && siteFooter) {
                var FAB_GAP = 24;

                function adjustFab() {
                    var footerTop = siteFooter.getBoundingClientRect().top;
                    var overlap = window.innerHeight - footerTop;
                    fabEl.style.bottom = (overlap > 0 ? overlap + FAB_GAP : FAB_GAP) + 'px';
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
?>