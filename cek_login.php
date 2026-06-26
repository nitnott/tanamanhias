<?php
/**
 * cek_login.php
 * Memproses autentikasi login menggunakan Prepared Statement
 * dan password_verify() untuk keamanan hash bcrypt.
 */

session_start();

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

require_once 'config/koneksi.php';

// Ambil & bersihkan input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input tidak boleh kosong
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit();
}

// Cari user berdasarkan username menggunakan Prepared Statement
$stmt = mysqli_prepare($koneksi, "SELECT id, nama, username, password, role FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Verifikasi password menggunakan password_verify (bcrypt)
    if (password_verify($password, $row['password'])) {
        // Login berhasil – simpan data di session
        $_SESSION['user_id']  = $row['id'];
        $_SESSION['nama']     = $row['nama'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];
        $_SESSION['status']   = 'login';

        // Regenerasi session ID untuk mencegah session fixation
        session_regenerate_id(true);

        header('Location: dashboard.php');
        exit();
    }
}

// Login gagal
$_SESSION['login_error'] = 'Username atau password salah. Silakan coba lagi.';
mysqli_stmt_close($stmt);
mysqli_close($koneksi);

header('Location: login.php');
exit();
