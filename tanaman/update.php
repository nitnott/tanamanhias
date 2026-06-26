<?php
/**
 * tanaman/update.php
 * Memproses POST dari form edit tanaman:
 *  - Validasi server-side
 *  - Upload gambar baru & hapus gambar lama
 *  - UPDATE database menggunakan Prepared Statement
 */

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/koneksi.php';

// ── Ambil & sanitasi input ──
$id            = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$kode_tanaman  = trim($_POST['kode_tanaman']  ?? '');
$nama_tanaman  = trim($_POST['nama_tanaman']  ?? '');
$kategori      = trim($_POST['kategori']      ?? '');
$harga         = trim($_POST['harga']         ?? '');
$stok          = trim($_POST['stok']          ?? '');
$tanggal_input = trim($_POST['tanggal_input'] ?? '');
$deskripsi     = trim($_POST['deskripsi']     ?? '');
$gambar_lama   = trim($_POST['gambar_lama']   ?? '');

// ── Validasi ──
$errors = [];

if (!$id)                   $errors[] = 'ID tidak valid.';
if (empty($kode_tanaman))   $errors[] = 'Kode tanaman wajib diisi.';
if (empty($nama_tanaman))   $errors[] = 'Nama tanaman wajib diisi.';
if (empty($kategori))       $errors[] = 'Kategori wajib dipilih.';
if (!is_numeric($harga) || (float)$harga < 0) $errors[] = 'Harga tidak valid.';
if (!is_numeric($stok)  || (int)$stok < 0)   $errors[] = 'Stok tidak valid.';
if (empty($tanggal_input))  $errors[] = 'Tanggal input wajib diisi.';

// Cek kode unik (exclude ID saat ini)
if (empty($errors) && $id) {
    $stmt_cek = mysqli_prepare($koneksi,
        "SELECT id FROM tanaman WHERE kode_tanaman = ? AND id != ?");
    mysqli_stmt_bind_param($stmt_cek, 'si', $kode_tanaman, $id);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);
    if (mysqli_stmt_num_rows($stmt_cek) > 0) {
        $errors[] = "Kode tanaman '$kode_tanaman' sudah digunakan oleh data lain.";
    }
    mysqli_stmt_close($stmt_cek);
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' | ', $errors);
    header("Location: edit.php?id=$id");
    exit();
}

// ── Upload gambar baru (jika ada) ──
$nama_file_baru = null; // null = tidak ada gambar baru

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file_tmp   = $_FILES['gambar']['tmp_name'];
    $file_name  = $_FILES['gambar']['name'];
    $file_size  = $_FILES['gambar']['size'];
    $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi MIME type
    $allowed_ext  = ['jpg', 'jpeg', 'png'];
    $allowed_mime = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo        = finfo_open(FILEINFO_MIME_TYPE);
    $file_mime    = finfo_file($finfo, $file_tmp);
    finfo_close($finfo);

    if (!in_array($file_ext, $allowed_ext) || !in_array($file_mime, $allowed_mime)) {
        $_SESSION['flash_error'] = 'Tipe file tidak diizinkan. Gunakan JPG/JPEG/PNG.';
        header("Location: edit.php?id=$id");
        exit();
    }
    if ($file_size > 2 * 1024 * 1024) {
        $_SESSION['flash_error'] = 'Ukuran file melebihi 2 MB.';
        header("Location: edit.php?id=$id");
        exit();
    }

    $nama_file_baru = 'tanaman_' . uniqid('', true) . '.' . $file_ext;
    $upload_dir     = '../assets/upload/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($file_tmp, $upload_dir . $nama_file_baru)) {
        $_SESSION['flash_error'] = 'Gagal menyimpan file gambar.';
        header("Location: edit.php?id=$id");
        exit();
    }

    // Hapus gambar lama jika ada dan berbeda
    if ($gambar_lama && file_exists($upload_dir . $gambar_lama)) {
        unlink($upload_dir . $gambar_lama);
    }
}

// Tentukan gambar yang dipakai: baru (jika ada) atau lama
$gambar_final = $nama_file_baru ?? ($gambar_lama ?: null);

// ── UPDATE database ──
$harga_val = (float) $harga;
$stok_val  = (int)   $stok;

$stmt = mysqli_prepare($koneksi,
    "UPDATE tanaman
     SET kode_tanaman = ?, nama_tanaman = ?, kategori = ?,
         harga = ?, stok = ?, tanggal_input = ?, deskripsi = ?, gambar = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, 'sssdisssi',
    $kode_tanaman,
    $nama_tanaman,
    $kategori,
    $harga_val,
    $stok_val,
    $tanggal_input,
    $deskripsi,
    $gambar_final,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = "Data tanaman \"$nama_tanaman\" berhasil diperbarui.";
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
    header('Location: index.php');
} else {
    // Rollback: hapus gambar baru jika update gagal
    if ($nama_file_baru && file_exists('../assets/upload/' . $nama_file_baru)) {
        unlink('../assets/upload/' . $nama_file_baru);
    }
    $_SESSION['flash_error'] = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    mysqli_stmt_close($stmt);
    header("Location: edit.php?id=$id");
}
exit();
