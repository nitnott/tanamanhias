<?php
/**
 * tanaman/index.php
 * Halaman daftar data tanaman hias dengan search & pagination.
 */

$base_url   = '../';
$page_title = 'Data Tanaman';
require_once '../config/koneksi.php';
require_once '../includes/header.php';

// ── Ambil pesan flash dari session ──
$flash_success = '';
$flash_error   = '';
if (isset($_SESSION['flash_success'])) {
    $flash_success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $flash_error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

// ── Parameter Search ──
$search = trim($_GET['search'] ?? '');

// ── Pagination ──
$per_page    = 10;
$current_page_num = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($current_page_num - 1) * $per_page;

// ── Hitung total data untuk pagination ──
if ($search !== '') {
    $stmt_count = mysqli_prepare($koneksi,
        "SELECT COUNT(*) AS total FROM tanaman WHERE nama_tanaman LIKE ? OR kategori LIKE ? OR kode_tanaman LIKE ?");
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt_count, 'sss', $like, $like, $like);
} else {
    $stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM tanaman");
}
mysqli_stmt_execute($stmt_count);
$total_data  = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['total'];
$total_pages = (int) ceil($total_data / $per_page);

// ── Ambil data ──
if ($search !== '') {
    $stmt = mysqli_prepare($koneksi,
        "SELECT * FROM tanaman
         WHERE nama_tanaman LIKE ? OR kategori LIKE ? OR kode_tanaman LIKE ?
         ORDER BY created_at DESC
         LIMIT ? OFFSET ?");
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, 'sssii', $like, $like, $like, $per_page, $offset);
} else {
    $stmt = mysqli_prepare($koneksi,
        "SELECT * FROM tanaman ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$no = $offset + 1; // nomor urut
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Tanaman</li>
    </ol>
</nav>

<!-- Alert Flash -->
<?php if ($flash_success): ?>
<div class="alert alert-success alert-dismissible fade show alert-auto-hide" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($flash_success); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($flash_error): ?>
<div class="alert alert-danger alert-dismissible fade show alert-auto-hide" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($flash_error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Judul + tombol aksi -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-flower2 me-2 text-success"></i>Data Tanaman Hias
        <span class="badge bg-success ms-1"><?php echo number_format($total_data); ?></span>
    </h4>
    <div class="d-flex gap-2">
        <a href="tambah.php" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Tambah
        </a>
        <a href="report.php" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
    </div>
</div>

<!-- Tabel Container -->
<div class="table-container">

    <!-- Form Search -->
    <form method="GET" action="" class="mb-3">
        <div class="input-group" style="max-width:360px;">
            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Cari nama, kode, atau kategori..."
                   value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-success" type="submit">Cari</button>
            <?php if ($search): ?>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
    <?php if ($search): ?>
    <p class="text-muted small mb-2">
        Hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
        — ditemukan <?php echo number_format($total_data); ?> data.
    </p>
    <?php endif; ?>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th style="width:45px;">No</th>
                    <th style="width:80px;">Gambar</th>
                    <th>Kode</th>
                    <th>Nama Tanaman</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Tgl Input</th>
                    <th style="width:150px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="text-center"><?php echo $no++; ?></td>
                        <td class="text-center">
                            <?php if ($row['gambar'] && file_exists('../assets/upload/' . $row['gambar'])): ?>
                                <img src="../assets/upload/<?php echo htmlspecialchars($row['gambar']); ?>"
                                     alt="<?php echo htmlspecialchars($row['nama_tanaman']); ?>"
                                     class="img-tanaman">
                            <?php else: ?>
                                <div class="img-placeholder mx-auto">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo htmlspecialchars($row['kode_tanaman']); ?></code></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_tanaman']); ?></td>
                        <td>
                            <span class="badge-kategori"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        </td>
                        <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php
                            $stok_class = '';
                            if ($row['stok'] == 0)       $stok_class = 'text-danger fw-bold';
                            elseif ($row['stok'] <= 10)  $stok_class = 'text-warning fw-bold';
                            ?>
                            <span class="<?php echo $stok_class; ?>">
                                <?php echo number_format($row['stok']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_input'])); ?></td>
                        <td class="text-center">
                            <a href="detail.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-info text-white me-1"
                               data-bs-toggle="tooltip" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-warning me-1"
                               data-bs-toggle="tooltip" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="hapus.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-danger btn-hapus"
                               data-nama="<?php echo htmlspecialchars($row['nama_tanaman']); ?>"
                               data-bs-toggle="tooltip" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <?php echo $search ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data tanaman.'; ?>
                            <?php if (!$search): ?>
                            <br><a href="tambah.php" class="btn btn-success btn-sm mt-2">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Sekarang
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Pagination" class="mt-3">
        <ul class="pagination pagination-sm mb-0 justify-content-center">
            <!-- Previous -->
            <li class="page-item <?php echo ($current_page_num <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $current_page_num - 1; ?>&search=<?php echo urlencode($search); ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <li class="page-item <?php echo ($p == $current_page_num) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $p; ?>&search=<?php echo urlencode($search); ?>">
                    <?php echo $p; ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?php echo ($current_page_num >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $current_page_num + 1; ?>&search=<?php echo urlencode($search); ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
        <p class="text-muted small text-center mt-1">
            Halaman <?php echo $current_page_num; ?> dari <?php echo $total_pages; ?>
            (Total: <?php echo number_format($total_data); ?> data)
        </p>
    </nav>
    <?php endif; ?>

</div><!-- /table-container -->

<?php
mysqli_stmt_close($stmt);
require_once '../includes/footer.php';
?>
