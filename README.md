# BendeharaKelas — Struktur Proyek "Framework-like"

Instruksi singkat untuk menjalankan dan melanjutkan migrasi proyek agar terlihat/tersusun seperti memakai framework minimal.

1. Set dokument root server (Apache / Laragon) ke folder `public`.
   - Di Laragon: klik kanan > Apache > httpd-vhosts.conf (atau buat vhost baru) arahkan `DocumentRoot` ke `.../BendeharaKelas/public`.

2. Konfigurasi database
   - Sesuaikan `config/config.php` jika nama host/user/password/db berbeda.

3. Routing & struktur
   - Entry utama adalah `public/index.php`. Gunakan query param `?page=...` untuk membuka page.
   - Autentikasi di-handle oleh `app/Controllers/AuthController.php`.
   - Views berada di `app/Views` dan controllers di `app/Controllers`.
   - Model utilitas: `app/Models/Database.php` (singleton mysqli).

4. Cara migrasi halaman lain
   - Salin file halaman yang ada (mis. `data_siswa.php`) ke `app/Views/data_siswa.php`.
   - Ubah link dari root ke `?page=data_siswa`.
   - Jika halaman mengakses DB secara langsung, ganti koneksi `mysqli_connect(...)` dengan:

```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
$db = Database::getInstance()->getConnection();
```

5. Setelah migrasi
   - Hapus file lama di root secara bertahap setelah memastikan versi di `app/Views` berfungsi.

6. Pertanyaan atau mau saya lanjutkan migrasi otomatis lebih banyak? Beri tahu halaman mana yang saya pindahkan.
