<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
if (!isset($_GET['id_pengeluaran'])) {
    $_SESSION['error_msg'] = 'ID pengeluaran tidak ditemukan.';
    header('Location: data_pengeluaran.php');
    exit;
}
$id = $_GET['id_pengeluaran'];
$koneksi = mysqli_connect('localhost', 'root', '', 'db_bendehara');
if (!$koneksi) {
    $_SESSION['error_msg'] = 'Koneksi database gagal.';
    header('Location: data_pengeluaran.php');
    exit;
}
$query = "DELETE FROM pengeluaran WHERE id_pengeluaran = '$id'";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_msg'] = 'Data pengeluaran berhasil dihapus.';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus data pengeluaran.';
}
mysqli_close($koneksi);
header('Location: data_pengeluaran.php');
exit;
