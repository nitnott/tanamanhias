<?php
/**
 * tanaman/detail.php
 * Halaman detail lengkap satu data tanaman hias.
 */

$base_url   = '../';
$page_title = 'Detail Tanaman';
require_once '../config/koneksi.php';
require_once '../includes/header.php';

// Validasi ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'ID tidak valid.';
    header('Location: index.php');
    exit();
}

// Ambil data tanaman
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tanaman WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    $_SESSION['flash_error'] = 'Data tanaman tidak ditemukan.';
    header('Location: index.php');
    exit();
}
mysqli_stmt_close($stmt);
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Data Tanaman</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
</nav>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-flower2 me-2 text-success"></i>Detail Tanaman
    </h4>
    <div class="d-flex gap-2">
        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="hapus.php?id=<?php echo $row['id']; ?>"
           class="btn btn-danger btn-sm btn-hapus"
           data-nama="<?php echo htmlspecialchars($row['nama_tanaman']); ?>">
            <i class="bi bi-trash me-1"></i>Hapus
        </a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">

    <!-- Kolom Gambar -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center p-3">
            <?php if ($row['gambar'] && file_exists('../assets/upload/' . $row['gambar'])): ?>
                <img src="../assets/upload/<?php echo htmlspecialchars($row['gambar']); ?>"
                     alt="<?php echo htmlspecialchars($row['nama_tanaman']); ?>"
                     class="img-tanaman-lg mb-3">
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center bg-light rounded"
                     style="height:280px; font-size:4rem; color:#ccc;">
                    <i class="bi bi-image"></i>
                </div>
                <p class="text-muted small mt-2">Belum ada foto</p>
            <?php endif; ?>

            <span class="badge-kategori d-inline-block mt-2">
                <?php echo htmlspecialchars($row['kategori']); ?>
            </span>
        </div>
    </div>

    <!-- Kolom Info Detail -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h3 class="fw-bold text-success mb-1">
                    <?php echo htmlspecialchars($row['nama_tanaman']); ?>
                </h3>
                <p class="text-muted mb-4">
                    <code><?php echo htmlspecialchars($row['kode_tanaman']); ?></code>
                </p>

                <!-- Info Grid -->
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1"><i class="bi bi-currency-dollar me-1"></i>Harga</div>
                            <div class="fw-bold fs-5 text-success">
                                Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1"><i class="bi bi-box-seam me-1"></i>Stok</div>
                            <?php
                            $stok = (int)$row['stok'];
                            $stok_class = $stok == 0 ? 'text-danger' : ($stok <= 10 ? 'text-warning' : 'text-success');
                            ?>
                            <div class="fw-bold fs-5 <?php echo $stok_class; ?>">
                                <?php echo number_format($stok); ?> unit
                                <?php if ($stok == 0): ?>
                                <span class="badge bg-danger ms-1 fs-6">Habis</span>
                                <?php elseif ($stok <= 10): ?>
                                <span class="badge bg-warning text-dark ms-1 fs-6">Hampir Habis</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1"><i class="bi bi-calendar me-1"></i>Tanggal Input</div>
                            <div class="fw-semibold">
                                <?php echo date('d F Y', strtotime($row['tanggal_input'])); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1"><i class="bi bi-calculator me-1"></i>Nilai Stok</div>
                            <div class="fw-semibold">
                                Rp<?php echo number_format($row['harga'] * $row['stok'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /row info -->

                <!-- Deskripsi -->
                <div class="mt-4">
                    <h6 class="fw-bold text-muted">
                        <i class="bi bi-card-text me-1"></i>Deskripsi
                    </h6>
                    <p class="text-secondary">
                        <?php echo $row['deskripsi']
                            ? nl2br(htmlspecialchars($row['deskripsi']))
                            : '<em class="text-muted">Tidak ada deskripsi.</em>'; ?>
                    </p>
                </div>

                <!-- Metadata -->
                <div class="mt-3 pt-3 border-top text-muted small">
                    <i class="bi bi-clock me-1"></i>
                    Ditambahkan: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                    &nbsp;|&nbsp;
                    <i class="bi bi-pencil me-1"></i>
                    Diperbarui: <?php echo date('d/m/Y H:i', strtotime($row['updated_at'])); ?>
                </div>
            </div>
        </div>
    </div>

</div><!-- /row -->

<?php require_once '../includes/footer.php'; ?>
