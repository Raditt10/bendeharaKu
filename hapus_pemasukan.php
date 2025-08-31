<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = 'ID pemasukan tidak ditemukan.';
    header('Location: data_pemasukan.php');
    exit;
}
$id = $_GET['id'];
$koneksi = mysqli_connect('localhost', 'root', '', 'db_bendehara');
if (!$koneksi) {
    $_SESSION['error_msg'] = 'Koneksi database gagal.';
    header('Location: data_pemasukan.php');
    exit;
}
$query = "DELETE FROM pemasukan WHERE id_pemasukan = '$id'";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_msg'] = 'Data pemasukan berhasil dihapus.';
} else {
    $_SESSION['error_msg'] = 'Gagal menghapus data pemasukan.';
}
mysqli_close($koneksi);
header('Location: data_pemasukan.php');
exit;
