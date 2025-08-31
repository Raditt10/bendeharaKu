-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Agu 2025 pada 05.42
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bendehara`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `iuran`
--

CREATE TABLE `iuran` (
  `id_iuran` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `minggu_1` enum('lunas','belum') DEFAULT 'belum',
  `minggu_2` enum('lunas','belum') DEFAULT 'belum',
  `minggu_3` enum('lunas','belum') DEFAULT 'belum',
  `minggu_4` enum('lunas','belum') DEFAULT 'belum',
  `tgl_bayar_minggu_1` date DEFAULT NULL,
  `tgl_bayar_minggu_2` date DEFAULT NULL,
  `tgl_bayar_minggu_3` date DEFAULT NULL,
  `tgl_bayar_minggu_4` date DEFAULT NULL,
  `minggu_ke` varchar(50) DEFAULT NULL,
  `status` enum('belum','lunas','','') NOT NULL DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iuran`
--

INSERT INTO `iuran` (`id_iuran`, `id_siswa`, `bulan`, `minggu_1`, `minggu_2`, `minggu_3`, `minggu_4`, `tgl_bayar_minggu_1`, `tgl_bayar_minggu_2`, `tgl_bayar_minggu_3`, `tgl_bayar_minggu_4`, `minggu_ke`, `status`) VALUES
(9, 4, 'JULI', 'lunas', 'lunas', 'belum', 'belum', '2025-08-09', '2025-08-13', NULL, NULL, '1,2', 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemasukan`
--

CREATE TABLE `pemasukan` (
  `id_pemasukan` int(11) NOT NULL,
  `bulan` varchar(100) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemasukan`
--

INSERT INTO `pemasukan` (`id_pemasukan`, `bulan`, `jumlah`, `keterangan`) VALUES
(8, 'JANUARI', 1000000.00, 'gatau');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `tanggal`, `jumlah`, `keterangan`) VALUES
(3, '2025-08-14', 908000.00, 'ya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `kontak_orangtua` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nama`, `nis`, `kontak_orangtua`) VALUES
(4, 'TIFAYATUL HUSNA AZZAHRA', '0097955381', '083862727098'),
(17, 'ALIF YUSUF ANWAR', '0089761823', '081220022885'),
(18, 'FREGA TEGUH DWIGUNA ', '0086517268', '085158228465'),
(19, 'NABIL AKBAR FADILLAH', '0087215222', '085724392492'),
(21, 'DEAN SULTAN SADYA', '3094589164', '087888794279'),
(22, 'REFKY FAVIAN MAHARDIKA', '0082273407', '085797945279'),
(23, 'RIZKY FAUZI RAHMAN ', '0093347067', '085864543566'),
(24, 'AYYAS HUSAYN', '0089341771', '087746284060'),
(25, 'RAIKHANIA RIZKY PUTRI HERDIANA ', '00945547984', '088229325715'),
(26, 'ARBIANSYAH AKBAR', '0095336041', '083108483510'),
(27, 'MUHAMMAD WILDAN TAUFIK', '0095147822', '081394002880'),
(28, 'IRHAM HADI AHSANU', '0087467723', '085722879380'),
(29, 'DANIS FERDIANSYAH', '0096900894', '087888762121'),
(30, 'MUHAMMAD RAFFA HARVANI', '0084058748', '082216734077'),
(31, 'BINTANG PUTRA RASYA DIKA', '0097296846', '083844398309'),
(32, 'DZIKRY FARERA LENGGANA', '0098787501', '089508535945'),
(33, 'KAISA VIDYA AMATULLAH ', '3097144451', '085723222636'),
(35, 'ALYA ALMIRA PUTRI', '0087916106', '0895609760701'),
(36, 'MARVA AULIA AHMAD', '0088190060', '08987480189'),
(37, 'NESYA MEGA PUTRI', '0082483612', '089630050306'),
(38, 'DAFFA DERYAN RASHIF ', '0094995529', '082121052686'),
(39, 'HANIF YIANWAR WAHIDAN', '0099930276', '085559718603'),
(40, 'ADLY SYAKIEB HAFIDZ GUSTIRA', '0083620508', '085860609552'),
(41, 'PUTRI JASMINE AZZAHRA RAMADHANI', '0084245542', '087834124956'),
(42, 'SASHYKIRANA ANANDITA SAHRONI', '0091176198', '081322371931'),
(43, 'ANNISA AGHNIYA FAZA', '0095085901', '0881023322444'),
(44, 'YUGA PUTRA NUGRAHA', '0091042703', '085861470747'),
(45, 'SITUMORANG, GABRIELDO', '0089999286', '0895341725629'),
(46, 'FARREL MUTTAQIN', '0083912690', '085814570139'),
(47, 'RAFADITYA SYAHPUTRA', '0082947154', '089661916855');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `role` enum('siswa','admin') NOT NULL DEFAULT 'siswa',
  `nama` varchar(100) NOT NULL,
  `nis` int(15) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `role`, `nama`, `nis`, `password`) VALUES
(8, 'admin', 'rafa', 123, '$2y$10$k2Cd/zou0BQK3rqqzhJxleck9HExYUuQVYunGPvizpxUR0c4so/hG'),
(9, 'siswa', 'adit', 12, '$2y$10$TEfQugiBC48jQzjqBdkGq.1O.OzYv9lzAKK0jk47p/fhDV9yQh2vW'),
(10, 'admin', 'Naiyva fathia agung', 1234, '$2y$10$CYCSubS01fnl0xLdg8bppOAjQkAW29idZcVJIyHtKP88FZZxI3mn.'),
(25, 'siswa', 'rafaditya ', 1237, '$2y$10$6gHJA3e6Z4BN.oUQhvEYzezsD40htdyr.iJXreRP2yg/3JuIlDt9S'),
(27, 'siswa', 'ADELYA FAUZI ALFIAN', 89688, '$2y$10$D7EkjZIfnwlrzPczyhmmH.heRlYtaLPi/0h8nF5bwtCZi/2K9rMri');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `iuran`
--
ALTER TABLE `iuran`
  ADD PRIMARY KEY (`id_iuran`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indeks untuk tabel `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD PRIMARY KEY (`id_pemasukan`);

--
-- Indeks untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `iuran`
--
ALTER TABLE `iuran`
  MODIFY `id_iuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pemasukan`
--
ALTER TABLE `pemasukan`
  MODIFY `id_pemasukan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `iuran`
--
ALTER TABLE `iuran`
  ADD CONSTRAINT `iuran_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
