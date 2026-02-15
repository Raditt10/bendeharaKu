<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/Database.php';
$koneksi = Database::getInstance()->getConnection();

// Pastikan parameter id ada
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query hapus
    $query = "DELETE FROM siswa WHERE id_siswa = $id";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['success_msg'] = "Data siswa berhasil dihapus!";
    header("Location: ?page=students");
    exit();
    } else {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['error_msg'] = "Gagal menghapus data.";
    header("Location: ?page=students");
    exit();
    }
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['success_msg'] = "ID tidak ditemukan.";
    header("Location: ?page=students");
    exit();
}

mysqli_close($koneksi);
?>
