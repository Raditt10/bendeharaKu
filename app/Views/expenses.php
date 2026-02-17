<?php
// Pastikan tidak ada output sebelum header
// Bersihkan pesan session
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Ambil Data Pengeluaran
// Ambil daftar tahun dari database pengeluaran
$tahunList = [];
$tahunQ = mysqli_query($koneksi, "SELECT DISTINCT tahun FROM pengeluaran ORDER BY tahun DESC");
while ($t = mysqli_fetch_assoc($tahunQ)) {
    if (!empty($t['tahun'])) $tahunList[] = $t['tahun'];
}

// Tahun aktif dari filter (GET), default tahun sekarang jika ada di list, jika tidak pakai tahun pertama di list
$tahun_aktif = $tahunList[0] ?? date('Y');
if (isset($_GET['tahun']) && $_GET['tahun'] !== '') {
    if (in_array($_GET['tahun'], $tahunList)) {
        $tahun_aktif = $_GET['tahun'];
    } else {
        $tahun_aktif = $_GET['tahun'];
    }
}

// Query data pengeluaran sesuai tahun
$query = "SELECT * FROM pengeluaran WHERE tahun = '" . mysqli_real_escape_string($koneksi, $tahun_aktif) . "' ORDER BY tanggal DESC";
$result = mysqli_query($koneksi, $query);

// Hitung Total Pengeluaran sesuai filter
$total_query = "SELECT SUM(jumlah) AS total FROM pengeluaran WHERE tahun = '" . mysqli_real_escape_string($koneksi, $tahun_aktif) . "'";
$total_result = mysqli_query($koneksi, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pengeluaran = $total_row['total'] ?? 0;
?>
    <div class="page-header">
        <div class="page-title">
            <h2>Data Pengeluaran</h2>
            <p>Kelola uang kas keluar dan pengeluaran kelas.</p>
            <?php if (count($tahunList)): ?>
            <form method="GET" action="" class="filter-bar" id="customYearForm">
                <input type="hidden" name="page" value="expenses">
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
            // Custom Dropdown JS (copy dari income)
            const dropdown = document.getElementById('customDropdown');
            const selected = document.getElementById('dropdownSelected');
            const list = document.getElementById('dropdownList');
            const input = document.getElementById('dropdownInput');
            selected.onclick = function(e) {
                list.style.display = list.style.display === 'none' ? 'block' : 'none';
                e.stopPropagation();
            };
            document.addEventListener('click', function() { list.style.display = 'none'; });
            list.querySelectorAll('.custom-dropdown-item').forEach(function(item) {
                item.onclick = function() {
                    input.value = this.dataset.value;
                    document.getElementById('customYearForm').submit();
                };
            });
            </script>
            <?php endif; ?>
        </div>
    </div>

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

    /* Summary Card (RED THEME) */
    .stats-card {
        background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%); /* Red Gradient */
        color: #ffffff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
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

    table { width: 100%; border-collapse: collapse; text-align: left; }
    thead { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    th { padding: 16px 20px; font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 0.95rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background-color: #f8fafc; }

    /* Expense Specific Text Color */
    .amount-expense { color: #dc2626; font-weight: 700; }

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
        .stats-card { overflow: hidden; }
        
        thead { display: none; }
        
        tr { display: block; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: white; }
        tr:last-child { margin-bottom: 0; }
        td { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding: 10px 0; text-align: right; }
        td:last-child { border-bottom: none; padding-bottom: 0; }
        td:first-child { padding-top: 0; }

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
            <h2>Data Pengeluaran</h2>
            <p>Riwayat penggunaan dana kas kelas.</p>
        </div>
        <div class="action-bar">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="?page=add_expense" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Catat Pengeluaran
                </a>
            <?php endif; ?>
            <a href="?page=expenses" class="btn btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Refresh
            </a>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-info">
            <span>Total Dana Terpakai</span>
            <h3>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
        </div>
        <div class="stats-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal</th>
                    <th>Jumlah Keluar</th>
                    <th>Keterangan</th>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <th style="text-align: right;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Format Tanggal
                        $date = date('d M Y', strtotime($row['tanggal']));
                ?>
                <tr>
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="Tanggal" style="font-weight: 500;"><?= $date ?></td>
                    <td data-label="Jumlah" class="amount-expense">
                        - Rp <?= number_format($row['jumlah'], 2, ',', '.') ?>
                    </td>
                    <td data-label="Keterangan"><?= htmlspecialchars($row['keterangan']) ?></td>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <td data-label="Aksi" class="action-cell" style="text-align: right;">
                        <a href="?page=edit_expense&id_pengeluaran=<?= $row['id_pengeluaran'] ?>" class="action-btn btn-edit">
                            Edit
                        </a>
                        <a href="?page=delete_expense&id_pengeluaran=<?= $row['id_pengeluaran'] ?>" class="action-btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?');">
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
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 10px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Belum ada data pengeluaran.
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

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
?>