<?php
session_start();
if (!isset($_SESSION['nis']) || $_SESSION['role'] !== 'admin') {
	header('Location: login.php');
	exit;
}

if (!isset($_GET['id_iuran'])) {
	$_SESSION['error_msg'] = 'ID laporan tidak ditemukan.';
	header('Location: laporan_kas.php');
	exit;
}

$id_iuran = $_GET['id_iuran'];
$koneksi = mysqli_connect('localhost', 'root', '', 'db_bendehara');
if (!$koneksi) {
	$_SESSION['error_msg'] = 'Koneksi database gagal.';
	header('Location: laporan_kas.php');
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
header('Location: laporan_kas.php');
exit;
