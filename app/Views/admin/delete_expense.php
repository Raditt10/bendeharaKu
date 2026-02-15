<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?page=home');
    exit;
}
if (!isset($_GET['id_pengeluaran'])) {
    $_SESSION['error_msg'] = 'ID pengeluaran tidak ditemukan.';
    header('Location: ?page=expenses');
    exit;
}
$id = $_GET['id_pengeluaran'];
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();
$query = "DELETE FROM pengeluaran WHERE id_pengeluaran = '$id'";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_msg'] = 'Data pengeluaran berhasil dihapus.';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus data pengeluaran.';
}
mysqli_close($koneksi);
header('Location: ?page=expenses');
exit;
