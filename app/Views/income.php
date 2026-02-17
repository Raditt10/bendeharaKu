<?php
// Proses hapus pemasukan jika ada parameter id
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
// Pastikan tidak ada output sebelum header
// Bersihkan pesan session
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Koneksi Database
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Ambil daftar tahun dari database
$tahunList = [];
$tahunQ = mysqli_query($koneksi, "SELECT DISTINCT tahun FROM pemasukan ORDER BY tahun DESC");
while ($t = mysqli_fetch_assoc($tahunQ)) {
    if (!empty($t['tahun'])) $tahunList[] = $t['tahun'];
}

// Tahun aktif dari filter (GET), default tahun sekarang jika ada di list, jika tidak pakai tahun pertama di list
$tahun_aktif = $tahunList[0] ?? date('Y');
if (isset($_GET['tahun']) && $_GET['tahun'] !== '') {
    // Jika tahun tidak ada di list, tetap tampilkan halaman dengan pesan kosong, jangan redirect
    if (in_array($_GET['tahun'], $tahunList)) {
        $tahun_aktif = $_GET['tahun'];
    } else {
        // Tahun tidak valid, tetap tampilkan halaman dengan data kosong
        $tahun_aktif = $_GET['tahun'];
    }
}

// Ambil data pemasukan sesuai tahun
$query = "SELECT * FROM pemasukan WHERE tahun = '" . mysqli_real_escape_string($koneksi, $tahun_aktif) . "' ORDER BY id_pemasukan DESC";
$result = mysqli_query($koneksi, $query);

// Hitung total pemasukan sesuai filter
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

    .page-title h2 { margin: 0; font-size: 1.75rem; color: var(--text-main, #1e293b); }
    .page-title p { margin: 5px 0 0; color: var(--text-muted, #64748b); font-size: 0.95rem; }

    /* Summary Card (DIPERBAIKI) */
    .stats-card {
        /* Menggunakan warna Hardcode agar pasti muncul backgroundnya */
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
    
    /* Table Container */
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

    thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    th { padding: 16px 20px; font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 0.95rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background-color: #f8fafc; }

    /* Badges & Actions */
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

    /* ALERT NOTIFICATION */
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

    /* RESPONSIVE TABLE (Mobile Card View) */
    @media (max-width: 768px) {
        .page-header { align-items: flex-start; }
        
        .stats-card { 
            overflow: hidden; /* Mencegah scrollbar horizontal karena icon */
        }
        
        /* Hide Table Header */
        thead { display: none; }
        
        /* Make rows look like cards */
        tr { display: block; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: white; }
        tr:last-child { margin-bottom: 0; }
        td { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding: 10px 0; text-align: right; }
        td:last-child { border-bottom: none; padding-bottom: 0; }
        td:first-child { padding-top: 0; }

        /* Add labels using data-label attribute */
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

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    
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
            <h2>Data Pemasukan</h2>
            <p>Kelola uang kas masuk dan donasi kelas.</p>
                <?php if (count($tahunList)): ?>
                <form method="GET" action="" class="filter-bar" id="customYearForm">
                    <input type="hidden" name="page" value="income">
                    <label class="filter-label">Tahun</label>
                    <div class="custom-dropdown" id="customDropdown">
                        <div class="custom-dropdown-selected" id="dropdownSelected">
                            <?= htmlspecialchars($tahun_aktif) ?>
                            <span class="custom-dropdown-arrow">&#9662;</span>
                        </div>
                        <div class="custom-dropdown-list" id="dropdownList" style="display:none;">
                            <?php foreach ($tahunList as $th): ?>
                                <div class="custom-dropdown-item<?= $th == $tahun_aktif ? ' selected' : '' ?>" data-value="<?= $th ?>"><?= $th ?></div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="tahun" id="dropdownInput" value="<?= htmlspecialchars($tahun_aktif) ?>">
                    </div>
                </form>
                <script>
                // Custom Dropdown JS
                const dropdown = document.getElementById('customDropdown');
                const selected = document.getElementById('dropdownSelected');
                const list = document.getElementById('dropdownList');
                const input = document.getElementById('dropdownInput');
                const form = document.getElementById('customYearForm');
                selected.onclick = function(e) {
                    e.stopPropagation();
                    list.style.display = list.style.display === 'block' ? 'none' : 'block';
                    dropdown.classList.toggle('open');
                };
                document.addEventListener('click', function() {
                    list.style.display = 'none';
                    dropdown.classList.remove('open');
                });
                list.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
                    item.onclick = function(e) {
                        e.stopPropagation();
                        input.value = this.dataset.value;
                        // Pastikan value input hidden sudah benar sebelum submit
                        setTimeout(function(){ form.submit(); }, 10);
                    };
                });
                </script>
                <?php endif; ?>
        <style>
            /* Filter Bar Styling */
            /* Filter Bar Styling */
            .filter-bar {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 18px;
                background: #f8fafc;
                padding: 12px 20px;
                border-radius: 10px;
                box-shadow: 0 1px 4px rgba(37,99,235,0.04);
                width: fit-content;
            }
            .filter-label {
                font-weight: 600;
                color: #2563eb;
                font-size: 1rem;
                margin-right: 2px;
                letter-spacing: 0.01em;
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
                transition: border-color 0.22s, box-shadow 0.22s;
                box-shadow: 0 2px 8px rgba(37,99,235,0.07);
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 110px;
            }
            .custom-dropdown.open .custom-dropdown-selected,
            .custom-dropdown-selected:hover {
                border-color: #1d4ed8;
                background: #f3f6fd;
                box-shadow: 0 4px 16px rgba(37,99,235,0.13);
            }
            .custom-dropdown-arrow {
                margin-left: 10px;
                font-size: 1.1em;
                color: #2563eb;
                pointer-events: none;
            }
            .custom-dropdown-list {
                position: absolute;
                top: 110%;
                left: 0;
                right: 0;
                background: #fff;
                border: 2px solid #2563eb;
                border-radius: 0 0 10px 10px;
                box-shadow: 0 8px 32px rgba(37,99,235,0.13);
                z-index: 10;
                max-height: 220px;
                overflow-y: auto;
                animation: fadeInDropdown 0.18s;
            }
            @keyframes fadeInDropdown {
                from { opacity: 0; transform: translateY(-8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .custom-dropdown-item {
                padding: 10px 18px;
                font-size: 1.05rem;
                color: #2563eb;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s, color 0.15s;
            }
            .custom-dropdown-item.selected,
            .custom-dropdown-item:hover {
                background: #2563eb;
                color: #fff;
            }           
            .filter-select-wrap::after {
                content: '\25BC';
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: #2563eb;
                font-size: 0.8em;
                pointer-events: none;
            }
        </style>
        </div>
        <div class="action-bar">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="?page=add_income" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Tambah Data
                </a>
            <?php endif; ?>
            <a href="?page=income" class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Refresh
            </a>
        </div>
    </div>

    <div class="stats-card reveal slide-in-left">
        <div class="stats-info">
            <span>Total Dana Terkumpul</span>
            <h3>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
        </div>
        <div class="stats-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
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
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="Bulan" style="font-weight: 600;">
                        <?= htmlspecialchars($row['bulan']) ?>
                    </td>
                    <td data-label="Jumlah" style="color: #10b981; font-weight: 600;">
                        + Rp <?= number_format($row['jumlah'], 2, ',', '.') ?>
                    </td>
                    <td data-label="Keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <td data-label="Aksi" class="action-cell" style="text-align: right;">
                        <a href="?page=edit_income&id=<?= $row['id_pemasukan'] ?>" class="action-btn btn-edit">
                            Edit
                        </a>
                        <a href="#" class="action-btn btn-delete" onclick="openWarning('?page=income&id=<?= $row['id_pemasukan'] ?>'); return false;">
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
                    <td colspan="5" style="text-align:center; padding: 40px; color: #64748b;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 10px;"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
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
    });
</script>

<?php
if ($result instanceof mysqli_result) {
    mysqli_free_result($result);
}
?>