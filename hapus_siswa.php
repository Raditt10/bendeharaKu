<?php
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_bendehara");

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Pastikan parameter id ada
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query hapus
    $query = "DELETE FROM siswa WHERE id_siswa = $id";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
    session_start();
    $_SESSION['success_msg'] = "Data siswa berhasil dihapus!";
    header("Location: data_siswa.php");
    exit();
    } else {
    session_start();
    $_SESSION['error_msg'] = "Gagal menghapus data.";
    header("Location: data_siswa.php");
    exit();
    }
} else {
    session_start();
    $_SESSION['success_msg'] = "ID tidak ditemukan.";
    header("Location: data_siswa.php");
    exit();
}

mysqli_close($koneksi);
?>
