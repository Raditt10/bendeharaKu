<?php
// --- BAGIAN PHP TETAP SAMA ---
if (session_status() === PHP_SESSION_NONE) session_start();
$basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);

// Mock Login (Hapus saat production)
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin';
    $_SESSION['nama'] = 'Bendahara Kelas';
}

require_once $basePath . '/config/config.php';
require_once $basePath . '/app/Models/Database.php';

$nama = explode(' ', $_SESSION['nama'])[0];
$koneksi = Database::getInstance()->getConnection();
$tahun_ini = date('Y');
$bulan_ini_angka = date('m');
$bulan_ini_nama_ind = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
][$bulan_ini_angka];

// --- LOGIC DATABASE ---
// A. Pemasukan
$q_income_month = "SELECT SUM(jumlah) as total FROM pemasukan WHERE bulan = '$bulan_ini_nama_ind' AND tahun = '$tahun_ini'";
$res_income = mysqli_query($koneksi, $q_income_month);
$income_month = ($res_income) ? (mysqli_fetch_assoc($res_income)['total'] ?? 0) : 0;

// B. Pengeluaran
$q_expense_month = "SELECT SUM(jumlah) as total FROM pengeluaran WHERE tahun = '$tahun_ini' AND MONTH(tanggal) = '$bulan_ini_angka'";
$res_expense = mysqli_query($koneksi, $q_expense_month);
$expense_month = ($res_expense) ? (mysqli_fetch_assoc($res_expense)['total'] ?? 0) : 0;

// C. Siswa
$q_siswa = "SELECT COUNT(*) as total FROM siswa";
$res_siswa = mysqli_query($koneksi, $q_siswa);
$total_siswa = ($res_siswa) ? mysqli_fetch_assoc($res_siswa)['total'] : 0;

// D. Grafik
$data_bulan = array_fill_keys(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'], 0);
$q_chart = "SELECT bulan, SUM(jumlah) as total FROM pemasukan WHERE tahun = '$tahun_ini' GROUP BY bulan";
$res_chart = mysqli_query($koneksi, $q_chart);

if ($res_chart) {
    while ($row = mysqli_fetch_assoc($res_chart)) {
        $b = ucfirst(strtolower($row['bulan']));
        if (isset($data_bulan[$b])) $data_bulan[$b] = (int)$row['total'];
    }
}
$labels_bulan = json_encode(array_keys($data_bulan));
$values_bulan = json_encode(array_values($data_bulan));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard Bendahara</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4f46e5;      /* Indigo 600 */
            --primary-dark: #4338ca; /* Indigo 700 */
            --bg-body: #f8fafc;      /* Slate 50 */
            --text-main: #0f172a;    /* Slate 900 */
            --text-muted: #64748b;   /* Slate 500 */
            --white: #ffffff;
            --success: #10b981;
            --danger: #ef4444;
            --radius-card: 20px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05);
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding-bottom: 90px; /* Space for bottom nav */
        }

        /* --- CONTENT WRAPPER --- */
        .main-content {
            padding: 24px; /* Padding standar agar tidak nempel pojok */
            max-width: 1000px;
            margin: 0 auto;
            padding-top: 30px; /* Tambahan jarak atas karena header dihapus */
        }

        /* --- HORIZONTAL SCROLL CARDS (Mobile Feel) --- */
        .stats-scroller {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 20px;
            scroll-snap-type: x mandatory;
            margin: 0 -24px; 
            padding: 0 24px 20px 24px; 
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .stats-scroller::-webkit-scrollbar { display: none; }

        .stat-card {
            min-width: 260px;
            background: var(--white);
            border-radius: var(--radius-card);
            padding: 20px;
            box-shadow: var(--shadow-soft);
            scroll-snap-align: center;
            border: 1px solid rgba(226, 232, 240, 0.6);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }
        
        .stat-card:active { transform: scale(0.98); }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .icon-box {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .bg-blue { background: #eff6ff; color: #2563eb; }
        .bg-orange { background: #fff7ed; color: #f97316; }
        .bg-purple { background: #f5f3ff; color: #8b5cf6; }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
        }

        .stat-trend {
            margin-top: 8px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .text-up { color: var(--success); }
        .text-neutral { color: var(--text-muted); }

        /* --- CHART SECTION --- */
        .chart-section {
            background: var(--white);
            border-radius: var(--radius-card);
            padding: 24px;
            box-shadow: var(--shadow-soft);
            margin-top: 10px;
            position: relative;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* --- BOTTOM NAVIGATION --- */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--white);
            display: flex;
            justify-content: space-around;
            padding: 12px 0 20px 0; 
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
            z-index: 1000;
            border-top: 1px solid #f1f5f9;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            gap: 6px;
            transition: color 0.2s;
        }

        .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .nav-item.active {
            color: var(--primary);
        }
        .nav-item.active i {
            transform: translateY(-2px);
            transition: transform 0.2s;
        }

        /* --- ALERT --- */
        .alert-float {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-main);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.9rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: top 0.5s cubic-bezier(0.4,0,0.2,1), opacity 0.5s cubic-bezier(0.4,0,0.2,1);
            width: max-content;
            min-width: 180px;
            max-width: 96vw;
            text-align: center;
            word-break: keep-all;
            overflow-wrap: anywhere;
            justify-content: center;
            padding-left: 24px;
            padding-right: 24px;
        }
        .alert-float.show {
            animation: slideDown 0.5s cubic-bezier(0.4,0,0.2,1);
        }
        .alert-float.hide {
            top: -80px !important;
            opacity: 0 !important;
            pointer-events: none;
        }
        @media (max-width: 480px) {
            .alert-float {
                font-size: 0.98rem;
                padding: 10px 4vw;
                width: max-content;
                min-width: 140px;
                max-width: 96vw;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
        }
        @keyframes slideDown { from { top: -60px; } to { top: 20px; } }

        @media (min-width: 768px) {
            .stats-scroller {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                overflow: visible;
                margin: 0;
                padding: 0;
            }
            .stat-card { min-width: auto; }
            .bottom-nav { display: none; }
            body { padding-bottom: 20px; }
        }
    </style>
</head>
<body>

    <?php if (!empty($_SESSION['success_msg'])): ?>
    <div class="alert-float" id="alertBox">
        <i class="fas fa-check-circle" style="color: #4ade80;"></i>
        <span><?= htmlspecialchars($_SESSION['success_msg']) ?></span>
    </div>
    <?php unset($_SESSION['success_msg']); endif; ?>

    <div class="main-content">
        
        <div class="stats-scroller">
            
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Pemasukan Bulan Ini</div>
                    </div>
                    <div class="icon-box bg-blue">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <h2 class="stat-value">Rp <?= number_format($income_month, 0, ',', '.') ?></h2>
                <div class="stat-trend text-neutral">
                    <i class="far fa-calendar-alt"></i> Data per <?= $bulan_ini_nama_ind ?>
                </div>
                <div style="position:absolute; right:-20px; bottom:-20px; width:100px; height:100px; background:#eff6ff; border-radius:50%; z-index:0;"></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Pengeluaran</div>
                    </div>
                    <div class="icon-box bg-orange">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <h2 class="stat-value">Rp <?= number_format($expense_month, 0, ',', '.') ?></h2>
                <div class="stat-trend text-neutral">
                    <i class="fas fa-info-circle"></i> Estimasi keluar
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <div class="icon-box bg-purple">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h2 class="stat-value"><?= $total_siswa ?> <span style="font-size:1rem; font-weight:500;">Anak</span></h2>
                <div class="stat-trend text-up">
                    <i class="fas fa-check"></i> Status Aktif
                </div>
            </div>

        </div>

        <div class="chart-section">
            <div class="section-title">
                Arus Kas Tahunan
                <button style="border:none; background:#f1f5f9; padding:6px 12px; border-radius:8px; font-size:0.8rem; font-weight:600; color:#64748b;"><?= $tahun_ini ?></button>
            </div>
            <div class="chart-wrapper">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom navigation bar removed as requested -->

    <script>
        // --- CONFIG CHART.JS ---
        const ctx = document.getElementById('financeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= $labels_bulan ?>,
                datasets: [{
                    label: 'Pemasukan',
                    data: <?= $values_bulan ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#94a3b8',
                            callback: function(value) {
                                if(value >= 1000000) return (value/1000000) + 'jt';
                                if(value >= 1000) return (value/1000) + 'rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#94a3b8',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6
                        }
                    }
                }
            }
        });

        // --- SHOW ANIMATION ON LOAD ---
        const alertBox = document.getElementById('alertBox');
        if(alertBox) {
            alertBox.classList.add('show');
        }
        // --- AUTO HIDE ALERT ---
        setTimeout(() => {
            if(alertBox) {
                alertBox.classList.remove('show');
                alertBox.classList.add('hide');
            }
        }, 4000);
    </script>
</body>
</html>