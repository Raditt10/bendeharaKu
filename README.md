# BendeharaKu - Sistem Manajemen Keuangan Kelas

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-MVC-orange)
![License](https://img.shields.io/badge/License-MIT-green)

**BendeharaKu** adalah aplikasi manajemen keuangan kelas berbasis web yang dirancang dengan pola arsitektur **Model-View-Controller (MVC)**. Aplikasi ini bertujuan untuk membantu bendahara sekolah atau organisasi dalam mencatat iuran, pemasukan, dan pengeluaran secara transparan, akuntabel, dan sistematis.

---

## 📌 Deskripsi Project

Berbeda dengan sistem pencatatan tradisional, BendeharaKu memisahkan logika bisnis, antarmuka pengguna, dan akses database untuk memastikan kode yang lebih bersih dan mudah dikembangkan.

* **Transparansi:** Seluruh anggota dapat melihat rekapitulasi keuangan secara real-time.
* **Manajemen Data:** Memudahkan pengelolaan data siswa, riwayat iuran, serta kategori pengeluaran.
* **Keamanan:** Dilengkapi dengan sistem autentikasi untuk membatasi akses fitur administratif.

## 🛠️ Tech Stack

Teknologi yang digunakan dalam pengembangan:

* **Language:** PHP Native
* **Architecture:** Pattern MVC (Model-View-Controller)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3 (Bootstrap), JavaScript
* **Server:** Apache (XAMPP/Laragon)

## 🚀 Fitur Utama

### 🔐 Autentikasi & Otorisasi
* **Login & Register:** Sistem masuk bagi pengguna terdaftar untuk mengelola data.

### 👥 Manajemen Siswa & Iuran
* **Data Siswa:** Kelola informasi lengkap siswa yang ada di kelas.
* **Detail Iuran:** Lacak status pembayaran iuran kas mingguan atau bulanan secara mendetail per individu.

### 💰 Pencatatan Keuangan
* **Manajemen Pemasukan:** Mencatat dana masuk dari berbagai sumber selain iuran rutin.
* **Manajemen Pengeluaran:** Dokumentasi penggunaan dana untuk keperluan operasional kelas.

### 📊 Laporan & Dashboard
* **Dashboard Statistik:** Ringkasan total saldo, pemasukan, dan pengeluaran di halaman utama.
* **Laporan Kas:** Rekapitulasi transaksi dalam format yang rapi dan mudah dibaca.

---

## 🖼️ Preview Antarmuka

Berikut adalah tampilan antarmuka aplikasi BendeharaKu:

<table>
  <tr>
    <td width="50%" align="center" valign="top">
      <img src="screenshot/Screenshot%202026-02-17%20205909.png" alt="Landing Page" width="100%">
      <br>
      <sub><b>Landing Page</b></sub>
    </td>
    <td width="50%" align="center" valign="top">
      <img src="screenshot/Screenshot%202026-02-17%20210033.png" alt="Dashboard" width="100%">
      <br>
      <sub><b>Dashboard</b></sub>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="top">
      <br>
      <img src="screenshot/Screenshot%202026-02-17%20210015.png" alt="Data pengeluaran kelas" height="450">
      <br>
      <sub><b>Data Pengeluaran Kelas</b></sub>
    </td>
  </tr>
</table>

<p align="center"><i>Semua gambar resolusi penuh dapat ditemukan di folder <b>screenshot/</b> pada repository ini.</i></p>

---

## 📁 Struktur Folder

```text
bendeharaKu/
├── app/
│   ├── Controllers/   # Logika aplikasi (AuthController, BaseController)
│   ├── Models/        # Logika Database (Database.php)
│   └── Views/         # Tampilan Antarmuka (Home, Students, Reports)
├── config/            # File konfigurasi database
├── public/            # Aset publik (Index.php, CSS, JS, Images)
├── db_bendehara.sql   # Dump Database MySQL
└── index.php          # Entry point utama aplikasi

```

## ⚙️ Instalasi & Setup

1. **Clone Repository**
```bash
git clone [https://github.com/raditt10/bendeharaku.git](https://github.com/raditt10/bendeharaku.git)
cd bendeharaku

```


2. **Pindahkan ke Server Lokal**
Pindahkan folder project ke `htdocs` (XAMPP) atau `www` (Laragon).
3. **Setup Database**
* Buka phpMyAdmin.
* Buat database baru dengan nama `db_bendehara`.
* Import file `db_bendehara.sql` yang tersedia di root folder.


4. **Konfigurasi Koneksi**
Sesuaikan kredensial database Anda di file `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_bendehara');

```


5. **Jalankan Aplikasi**
Buka browser dan akses: `http://localhost/bendeharaku`

## 🤝 Kontribusi

Aplikasi ini bersifat open-source. Jika Anda ingin memperbaiki bug atau menambahkan fitur baru (seperti ekspor PDF):

1. Fork repository ini.
2. Buat branch fitur (`git checkout -b fitur-baru`).
3. Commit perubahan.
4. Push ke branch.
5. Buat Pull Request.

## 📄 Lisensi

Project ini dilisensikan di bawah **MIT License**.

---

*Hak cipta sepenuhnya milik pengembang Raditt10.*
