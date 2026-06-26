<?php
/**
 * tanaman/hapus.php
 * Menghapus satu data tanaman beserta file gambarnya.
 * Dipanggil via GET ?id=X setelah konfirmasi dari JavaScript.
 */

session_start();

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/koneksi.php';

// Validasi ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'ID tanaman tidak valid.';
    header('Location: index.php');
    exit();
}

// Ambil nama & gambar sebelum dihapus (untuk pesan & hapus file)
$stmt_get = mysqli_prepare($koneksi, "SELECT nama_tanaman, gambar FROM tanaman WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt_get, 'i', $id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);

if (!$row = mysqli_fetch_assoc($result_get)) {
    $_SESSION['flash_error'] = 'Data tanaman tidak ditemukan.';
    mysqli_stmt_close($stmt_get);
    header('Location: index.php');
    exit();
}
mysqli_stmt_close($stmt_get);

$nama_tanaman = $row['nama_tanaman'];
$gambar       = $row['gambar'];

// ── Hapus dari database menggunakan Prepared Statement ──
$stmt_del = mysqli_prepare($koneksi, "DELETE FROM tanaman WHERE id = ?");
mysqli_stmt_bind_param($stmt_del, 'i', $id);

if (mysqli_stmt_execute($stmt_del)) {
    // Hapus file gambar dari server jika ada
    if ($gambar) {
        $path_gambar = '../assets/upload/' . $gambar;
        if (file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }
    $_SESSION['flash_success'] = "Tanaman \"$nama_tanaman\" berhasil dihapus.";
} else {
    $_SESSION['flash_error'] = 'Gagal menghapus data: ' . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt_del);
mysqli_close($koneksi);

header('Location: index.php');
exit();
