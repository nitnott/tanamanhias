<?php
/**
 * config/koneksi.php
 * Konfigurasi koneksi database menggunakan MySQLi
 * Sistem Data Tanaman Hias
 */

// Konfigurasi koneksi
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_tanaman_hias');

// Buat koneksi
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Periksa koneksi
if (!$koneksi) {
    die('<div style="font-family:sans-serif;padding:20px;color:red;">
        <strong>Koneksi Database Gagal:</strong> ' . mysqli_connect_error() . '
        <br>Pastikan MySQL sudah berjalan dan database <em>' . DB_NAME . '</em> sudah dibuat.
    </div>');
}

// Set charset UTF-8 untuk menghindari masalah encoding
mysqli_set_charset($koneksi, 'utf8mb4');
