<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=home');
    exit;
}
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    header('Location: ?page=income');
    exit;
}
$id = $_GET['id'];
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();
$query = "DELETE FROM pemasukan WHERE id_pemasukan = '$id'";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_msg'] = 'Data pemasukan berhasil dihapus.';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus data pemasukan.';
}
mysqli_close($koneksi);
header('Location: ?page=income');
exit;
