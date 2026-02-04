# Web Bendehara Kelas

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

**Web Bendehara Kelas** adalah aplikasi berbasis web sederhana yang dirancang untuk membantu bendahara kelas dalam mencatat dan mengelola keuangan (uang kas). Aplikasi ini menggantikan pencatatan manual di buku besar dengan sistem digital yang lebih rapi, transparan, dan meminimalkan kesalahan penghitungan.

## 📌 Deskripsi Project
Aplikasi ini berfokus pada transparansi dan kemudahan pengelolaan dana kelas.
* **Bendahara** dapat mencatat pemasukan rutin (iuran kas) dan pengeluaran operasional kelas.
* **Sistem** secara otomatis merekapitulasi saldo akhir dan menyediakan laporan sederhana.
* **Keamanan** data terjaga melalui sistem login.

## 🛠️ Tech Stack
Aplikasi ini dibangun menggunakan teknologi web dasar yang ringan dan mudah dijalankan:

* **Backend:** PHP Native (Tanpa Framework)
* **Database:** MySQL
* **Frontend:** HTML, CSS (Bootstrap), JavaScript
* **Server:** Apache (via XAMPP/Laragon)

## 🚀 Fitur Utama

### 🔐 Otentikasi
* **Login & Register**: Sistem keamanan untuk membatasi akses hanya kepada pengguna yang berwenang (Bendahara/Admin).

### 👥 Manajemen Data Siswa
* **CRUD Siswa**: Menambah, mengedit, melihat, dan menghapus data siswa yang menjadi donatur kas kelas.

### 💰 Manajemen Keuangan
* **Pencatatan Pemasukan**: Input data pembayaran uang kas mingguan/bulanan.
* **Pencatatan Pengeluaran**: Input data penggunaan dana untuk keperluan kelas (fotokopi, alat kebersihan, dll).
* **Detail Iuran**: Memantau status pembayaran per siswa.

### 📊 Laporan & Rekap
* **Dashboard Ringkasan**: Menampilkan total saldo, total pemasukan, dan total pengeluaran secara *real-time*.
* **Laporan Kas**: Tabel riwayat transaksi keuangan yang terstruktur.

## 📁 Struktur File
Berikut adalah gambaran struktur file utama dalam aplikasi ini:

```text
web-bendehara-kelas/
├── index.php              # Halaman Dashboard Utama
├── login.php              # Halaman Login
├── register.php           # Halaman Registrasi User Baru
├── data_siswa.php         # Halaman Data Siswa
├── data_pemasukan.php     # Halaman Riwayat Pemasukan
├── data_pengeluaran.php   # Halaman Riwayat Pengeluaran
├── laporan_kas.php        # Halaman Laporan Keuangan
├── tambah_*.php           # File form untuk input data (siswa, pemasukan, pengeluaran)
├── edit_*.php             # File form untuk edit data
├── hapus_*.php            # Logika penghapusan data
└── logout.php             # Script logout

```

## ⚙️ Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan project di komputer lokal menggunakan XAMPP atau Laragon:

### 1. Persiapan Environment

Pastikan komputer Anda sudah terinstall aplikasi web server lokal:

* [XAMPP](https://www.apachefriends.org/) (PHP & MySQL)

### 2. Clone atau Download

Letakkan folder project ini di dalam folder `htdocs` (jika pakai XAMPP) atau `www` (jika pakai Laragon).

```bash
cd htdocs
git clone [https://github.com/raditt10/web-bendehara-kelas.git](https://github.com/raditt10/web-bendehara-kelas.git)

```

### 3. Setup Database

1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama (misalnya): `db_bendehara`.
3. **Import** file database (biasanya berekstensi `.sql`) jika tersedia di dalam folder project.
* *Catatan: Jika file .sql tidak ada, Anda perlu membuat tabel `users`, `siswa`, `pemasukan`, dan `pengeluaran` secara manual sesuai struktur kode.*



### 4. Konfigurasi Koneksi

Cari file koneksi database (biasanya bernama `koneksi.php`, `config.php`, atau ada di bagian atas file `index.php`). Sesuaikan detailnya:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_bendehara"; // Sesuaikan dengan nama database yang dibuat

```

### 5. Jalankan Aplikasi

Buka browser dan akses:
`http://localhost/web-bendehara-kelas`

## 🤝 Kontribusi

Project ini bersifat *open-source*. Jika Anda ingin menambahkan fitur seperti ekspor ke Excel atau grafik statistik:

1. Fork repository ini.
2. Buat branch fitur (`git checkout -b fitur-baru`).
3. Commit perubahan (`git commit -m 'Menambahkan fitur grafik'`).
4. Push (`git push origin fitur-baru`).
5. Buat Pull Request.

## 📄 Lisensi

Project ini bebas digunakan dan dimodifikasi untuk keperluan pembelajaran atau penggunaan pribadi.

---

*Dikembangkan oleh [Raditt10]*

```

```
