<?php
/**
 * includes/header.php
 * Header umum: session check + HTML head + navbar + sidebar
 * Di-include di setiap halaman yang membutuhkan autentikasi.
 */

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id']) || $_SESSION['status'] !== 'login') {
    header('Location: ' . $base_url . 'login.php');
    exit();
}

// Ambil info user dari session
$session_nama     = htmlspecialchars($_SESSION['nama'] ?? 'User');
$session_role     = htmlspecialchars($_SESSION['role'] ?? 'petugas');
$session_username = htmlspecialchars($_SESSION['username'] ?? '');

// Tentukan halaman aktif untuk highlight sidebar
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' – ' : ''; ?>Tanaman Hias</title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:#2e7d32; height:60px;">
    <div class="container-fluid px-3">

        <!-- Toggle sidebar (mobile) -->
        <button id="sidebarToggle" class="btn btn-sm btn-outline-light me-2 d-lg-none">
            <i class="bi bi-list"></i>
        </button>

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $base_url; ?>dashboard.php">
            <i class="bi bi-flower1 fs-4"></i>
            <span class="navbar-brand-text d-none d-sm-inline">Tanaman Hias</span>
        </a>

        <!-- Kanan: user info + logout -->
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 d-none d-md-inline small">
                <i class="bi bi-person-circle me-1"></i>
                <?php echo $session_nama; ?>
                <span class="badge bg-light text-success ms-1"><?php echo ucfirst($session_role); ?></span>
            </span>
            <a href="<?php echo $base_url; ?>logout.php"
               class="btn btn-sm btn-outline-light"
               onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline ms-1">Logout</span>
            </a>
        </div>

    </div>
</nav>
<!-- / NAVBAR -->

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<nav id="sidebar" class="sidebar">

    <div class="pt-2 pb-1">
        <div class="sidebar-heading">Menu Utama</div>

        <a href="<?php echo $base_url; ?>dashboard.php"
           class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Manajemen</div>

        <a href="<?php echo $base_url; ?>tanaman/index.php"
           class="nav-link <?php echo ($current_dir === 'tanaman') ? 'active' : ''; ?>">
            <i class="bi bi-flower2"></i> Data Tanaman
        </a>

        <a href="<?php echo $base_url; ?>tanaman/tambah.php"
           class="nav-link <?php echo ($current_page === 'tambah.php' && $current_dir === 'tanaman') ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i> Tambah Tanaman
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">Laporan</div>

        <a href="<?php echo $base_url; ?>tanaman/report.php" target="_blank" class="nav-link">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?php echo $base_url; ?>logout.php"
           class="nav-link"
           onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

</nav>
<!-- / SIDEBAR -->

<!-- ═══════════════ KONTEN UTAMA (dibuka di sini, ditutup di footer) ═══════════════ -->
<div class="main-content">
