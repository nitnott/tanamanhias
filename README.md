# Sistem Data Tanaman Hias
**UAS Pemrograman Web 2 – PHP Native**

---

## Teknologi
- PHP Native (tanpa framework)
- MySQL / MariaDB
- Bootstrap 5.3 + Bootstrap Icons
- TCPDF (laporan PDF)
- JavaScript Vanilla
- Session-based Login

---

## Instalasi

### 1. Clone / Salin Project
Salin folder `tanaman_hias/` ke:
- **XAMPP**: `C:/xampp/htdocs/tanaman_hias/`
- **Laragon**: `C:/laragon/www/tanaman_hias/`

### 2. Buat Database
1. Buka **phpMyAdmin** atau MySQL CLI
2. Jalankan file `database.sql`:
   ```sql
   SOURCE /path/to/tanaman_hias/database.sql;
   ```
   Atau import via phpMyAdmin → **Import** → pilih `database.sql`.

### 3. Pasang TCPDF (untuk fitur PDF)
Pastikan **Composer** sudah terpasang, lalu jalankan di folder root project:
```bash
cd tanaman_hias/
composer require tecnickcom/tcpdf
```

### 4. Konfigurasi Koneksi
Edit file `config/koneksi.php` jika diperlukan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Sesuaikan password MySQL Anda
define('DB_NAME', 'db_tanaman_hias');
```

### 5. Jalankan
Buka browser: `http://localhost/tanaman_hias/`

---

## Akun Default
| Username | Password   | Role    |
|----------|------------|---------|
| admin    | password   | admin   |
| petugas  | password   | petugas |

> Password di-hash dengan `password_hash()` menggunakan bcrypt (cost 12).

---

## Struktur Folder
```
tanaman_hias/
│
├── assets/
│   ├── css/
│   │   └── style.css          ← Custom CSS
│   ├── js/
│   │   └── script.js          ← Custom JavaScript
│   ├── img/                   ← Gambar statis aplikasi
│   └── upload/                ← Foto tanaman (di-generate otomatis)
│
├── config/
│   └── koneksi.php            ← Konfigurasi database
│
├── includes/
│   ├── header.php             ← Header, navbar, sidebar
│   └── footer.php             ← Footer + script JS
│
├── tanaman/
│   ├── index.php              ← Daftar tanaman (search + pagination)
│   ├── tambah.php             ← Form tambah tanaman
│   ├── simpan.php             ← Proses insert tanaman
│   ├── edit.php               ← Form edit tanaman
│   ├── update.php             ← Proses update tanaman
│   ├── hapus.php              ← Proses hapus tanaman
│   ├── detail.php             ← Detail satu tanaman
│   └── report.php             ← Generate PDF laporan
│
├── vendor/                    ← Composer packages (TCPDF)
├── index.php                  ← Entry point (redirect)
├── login.php                  ← Halaman login
├── cek_login.php              ← Proses autentikasi
├── logout.php                 ← Proses logout
├── dashboard.php              ← Dashboard statistik
├── database.sql               ← Script SQL
└── README.md                  ← Dokumentasi ini
```

---

## Fitur
- ✅ Login / Logout dengan session
- ✅ Password bcrypt (`password_hash` / `password_verify`)
- ✅ Dashboard statistik (jumlah tanaman, stok, kategori, nilai inventaris)
- ✅ CRUD lengkap data tanaman
- ✅ Pencarian nama tanaman (real-time via query)
- ✅ Pagination (10 data per halaman)
- ✅ Upload gambar JPG/PNG maks. 2 MB + validasi MIME
- ✅ Hapus gambar lama saat update
- ✅ Generate laporan PDF via TCPDF
- ✅ Prepared Statement (anti SQL Injection)
- ✅ Bootstrap 5 Form Validation
- ✅ Konfirmasi hapus via JavaScript
- ✅ Responsive (Bootstrap 5 + sidebar mobile toggle)

---

## Catatan
- Folder `assets/upload/` akan dibuat otomatis saat pertama kali upload foto.
- Jika TCPDF belum terpasang, halaman report akan menampilkan instruksi instalasi.
- Pastikan PHP memiliki ekstensi `fileinfo` aktif untuk validasi MIME type file.
# tanamanhias
