<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['nis']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=login');
    exit;
}

if (!isset($_GET['id_iuran'])) {
    $_SESSION['error_msg'] = 'ID laporan tidak ditemukan.';
    header('Location: ?page=report');
    exit;
}

$id_iuran = $_GET['id_iuran'];
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();
if (!$koneksi) {
    $_SESSION['error_msg'] = 'Koneksi database gagal.';
    header('Location: ?page=report');
    exit;
}

// Hapus data iuran berdasarkan id_iuran
$query = "DELETE FROM iuran WHERE id_iuran = '$id_iuran'";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_msg'] = 'Data laporan berhasil dihapus.';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus data laporan.';
}

mysqli_close($koneksi);
header('Location: ?page=report');
exit;
