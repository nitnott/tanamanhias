<?php
/**
 * tanaman/simpan.php
 * Memproses POST dari form tambah tanaman:
 *  - Validasi server-side
 *  - Upload gambar
 *  - Insert ke database menggunakan Prepared Statement
 */

session_start();

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit();
}

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/koneksi.php';

// ── Ambil & sanitasi input ──
$kode_tanaman  = trim($_POST['kode_tanaman']  ?? '');
$nama_tanaman  = trim($_POST['nama_tanaman']  ?? '');
$kategori      = trim($_POST['kategori']      ?? '');
$harga         = trim($_POST['harga']         ?? '');
$stok          = trim($_POST['stok']          ?? '');
$tanggal_input = trim($_POST['tanggal_input'] ?? '');
$deskripsi     = trim($_POST['deskripsi']     ?? '');

// ── Validasi server-side ──
$errors = [];

if (empty($kode_tanaman))  $errors[] = 'Kode tanaman wajib diisi.';
if (empty($nama_tanaman))  $errors[] = 'Nama tanaman wajib diisi.';
if (empty($kategori))      $errors[] = 'Kategori wajib dipilih.';
if (!is_numeric($harga) || (float)$harga < 0) $errors[] = 'Harga tidak valid.';
if (!is_numeric($stok)  || (int)$stok < 0)   $errors[] = 'Stok tidak valid.';
if (empty($tanggal_input)) $errors[] = 'Tanggal input wajib diisi.';

// Cek kode unik
if (empty($errors)) {
    $stmt_cek = mysqli_prepare($koneksi, "SELECT id FROM tanaman WHERE kode_tanaman = ?");
    mysqli_stmt_bind_param($stmt_cek, 's', $kode_tanaman);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);
    if (mysqli_stmt_num_rows($stmt_cek) > 0) {
        $errors[] = "Kode tanaman '$kode_tanaman' sudah digunakan. Gunakan kode lain.";
    }
    mysqli_stmt_close($stmt_cek);
}

// ── Upload gambar (opsional) ──
$nama_file = null;

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file_tmp   = $_FILES['gambar']['tmp_name'];
    $file_name  = $_FILES['gambar']['name'];
    $file_size  = $_FILES['gambar']['size'];
    $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi tipe file
    $allowed_ext  = ['jpg', 'jpeg', 'png'];
    $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo        = finfo_open(FILEINFO_MIME_TYPE);
    $file_mime    = finfo_file($finfo, $file_tmp);
    finfo_close($finfo);

    if (!in_array($file_ext, $allowed_ext) || !in_array($file_mime, $allowed_mime)) {
        $errors[] = 'Tipe file tidak diizinkan. Gunakan JPG/JPEG/PNG.';
    } elseif ($file_size > 2 * 1024 * 1024) {
        $errors[] = 'Ukuran file melebihi batas 2 MB.';
    } else {
        // Nama file unik menggunakan uniqid + microtime
        $nama_file  = 'tanaman_' . uniqid('', true) . '.' . $file_ext;
        $upload_dir = '../assets/upload/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (!move_uploaded_file($file_tmp, $upload_dir . $nama_file)) {
            $errors[] = 'Gagal menyimpan file gambar. Periksa permission folder upload.';
            $nama_file = null;
        }
    }
}

// ── Jika ada error, redirect kembali ──
if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' | ', $errors);
    header('Location: tambah.php');
    exit();
}

// ── Insert ke database menggunakan Prepared Statement ──
$stmt = mysqli_prepare($koneksi,
    "INSERT INTO tanaman
        (kode_tanaman, nama_tanaman, kategori, harga, stok, tanggal_input, deskripsi, gambar)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

$harga_val = (float) $harga;
$stok_val  = (int)   $stok;

mysqli_stmt_bind_param($stmt, 'sssdisss',
    $kode_tanaman,
    $nama_tanaman,
    $kategori,
    $harga_val,
    $stok_val,
    $tanggal_input,
    $deskripsi,
    $nama_file
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = "Tanaman \"$nama_tanaman\" berhasil ditambahkan.";
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
    header('Location: index.php');
} else {
    // Rollback: hapus gambar yang sudah diupload jika insert gagal
    if ($nama_file && file_exists('../assets/upload/' . $nama_file)) {
        unlink('../assets/upload/' . $nama_file);
    }
    $_SESSION['flash_error'] = 'Gagal menyimpan data ke database: ' . mysqli_error($koneksi);
    mysqli_stmt_close($stmt);
    header('Location: tambah.php');
}
exit();
