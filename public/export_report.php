<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['nis'])) {
    echo "<script>window.location='index.php?page=login';</script>";
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Models/Database.php';

$koneksi = Database::getInstance()->getConnection();

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

// Set Headers for Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Laporan_Kas_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$html = '<html><head><meta charset="UTF-8"></head><body>';
$html .= '<table border="1" cellpadding="5" cellspacing="0">';
$html .= '<tr>';
$html .= '<th colspan="7" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #4f46e5; color: white;">LAPORAN PEMBAYARAN KAS KELAS</th>';
$html .= '</tr>';
if (!empty($bulan_filter)) {
    $html .= '<tr><td colspan="7" style="text-align: center; font-weight: bold;">Periode Bulan: ' . htmlspecialchars($bulan_filter) . '</td></tr>';
}
$html .= '<tr>';
$html .= '<th style="background-color: #e2e8f0;">No</th>';
$html .= '<th style="background-color: #e2e8f0;">Nama Siswa</th>';
$html .= '<th style="background-color: #e2e8f0;">Bulan</th>';
$html .= '<th style="background-color: #e2e8f0;">Minggu 1</th>';
$html .= '<th style="background-color: #e2e8f0;">Minggu 2</th>';
$html .= '<th style="background-color: #e2e8f0;">Minggu 3</th>';
$html .= '<th style="background-color: #e2e8f0;">Minggu 4</th>';
$html .= '</tr>';

$no = 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td style="text-align:center;">' . $no++ . '</td>';
        $html .= '<td>' . htmlspecialchars($row['nama']) . '</td>';
        $html .= '<td style="text-align:center;">' . htmlspecialchars(ucfirst($row['bulan'])) . '</td>';
        $html .= '<td style="text-align:center; color:' . ($row['minggu_1'] === 'lunas' ? '#059669' : '#dc2626') . '">' . ucfirst($row['minggu_1']) . '</td>';
        $html .= '<td style="text-align:center; color:' . ($row['minggu_2'] === 'lunas' ? '#059669' : '#dc2626') . '">' . ucfirst($row['minggu_2']) . '</td>';
        $html .= '<td style="text-align:center; color:' . ($row['minggu_3'] === 'lunas' ? '#059669' : '#dc2626') . '">' . ucfirst($row['minggu_3']) . '</td>';
        $html .= '<td style="text-align:center; color:' . ($row['minggu_4'] === 'lunas' ? '#059669' : '#dc2626') . '">' . ucfirst($row['minggu_4']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="7" style="text-align: center;">Tidak ada data ditemukan</td></tr>';
}
$html .= '</table>';
$html .= '</body></html>';

echo $html;
exit;
