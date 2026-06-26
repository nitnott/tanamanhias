<?php
/**
 * dashboard.php
 * Halaman dashboard utama – menampilkan statistik tanaman hias.
 */

$base_url  = '';
$page_title = 'Dashboard';
require_once 'config/koneksi.php';
require_once 'includes/header.php';

// ── Ambil statistik dari database ──

// Total jumlah tanaman (baris data)
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tanaman");
$total_tanaman = (int) mysqli_fetch_assoc($q_total)['total'];

// Total stok keseluruhan
$q_stok = mysqli_query($koneksi, "SELECT COALESCE(SUM(stok), 0) AS total_stok FROM tanaman");
$total_stok = (int) mysqli_fetch_assoc($q_stok)['total_stok'];

// Jumlah kategori unik
$q_kat = mysqli_query($koneksi, "SELECT COUNT(DISTINCT kategori) AS jml_kat FROM tanaman");
$jml_kategori = (int) mysqli_fetch_assoc($q_kat)['jml_kat'];

// Total nilai inventaris (harga × stok)
$q_nilai = mysqli_query($koneksi, "SELECT COALESCE(SUM(harga * stok), 0) AS nilai FROM tanaman");
$nilai_inventaris = (float) mysqli_fetch_assoc($q_nilai)['nilai'];

// ── 5 tanaman terakhir ditambahkan ──
$q_terbaru = mysqli_query($koneksi,
    "SELECT nama_tanaman, kategori, stok, harga, tanggal_input
     FROM tanaman
     ORDER BY created_at DESC
     LIMIT 5"
);

// ── Distribusi per kategori (untuk ringkasan) ──
$q_kategori = mysqli_query($koneksi,
    "SELECT kategori, COUNT(*) AS jumlah, SUM(stok) AS total_stok
     FROM tanaman
     GROUP BY kategori
     ORDER BY jumlah DESC"
);
?>

<!-- ══ Breadcrumb ══ -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><i class="bi bi-house-fill me-1"></i>Dashboard</li>
    </ol>
</nav>

<h4 class="fw-bold mb-4">
    <i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard
    <small class="fs-6 text-muted fw-normal ms-2">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</small>
</h4>

<!-- ══ Stat Cards ══ -->
<div class="row g-4 mb-4">

    <!-- Jumlah Tanaman -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-flower2"></i>
                </div>
                <div>
                    <div class="stat-number text-success"><?php echo number_format($total_tanaman); ?></div>
                    <div class="text-muted small fw-semibold">Jenis Tanaman</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stok -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="card-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="stat-number text-info"><?php echo number_format($total_stok); ?></div>
                    <div class="text-muted small fw-semibold">Total Stok</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jumlah Kategori -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <div class="stat-number text-warning"><?php echo number_format($jml_kategori); ?></div>
                    <div class="text-muted small fw-semibold">Kategori</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Inventaris -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="card-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="stat-number text-danger" style="font-size:1.4rem;">
                        Rp<?php echo number_format($nilai_inventaris, 0, ',', '.'); ?>
                    </div>
                    <div class="text-muted small fw-semibold">Nilai Inventaris</div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /row stat cards -->

<!-- ══ Baris bawah: Terbaru + Kategori ══ -->
<div class="row g-4">

    <!-- Tanaman Terbaru -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2 text-success"></i>Tanaman Terbaru
                </h6>
                <a href="tanaman/index.php" class="btn btn-sm btn-outline-success">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nama Tanaman</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($q_terbaru) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($q_terbaru)): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold"><?php echo htmlspecialchars($row['nama_tanaman']); ?></td>
                                    <td>
                                        <span class="badge-kategori"><?php echo htmlspecialchars($row['kategori']); ?></span>
                                    </td>
                                    <td><?php echo number_format($row['stok']); ?></td>
                                    <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada data tanaman.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Kategori -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-pie-chart me-2 text-success"></i>Distribusi Kategori
                </h6>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($q_kategori) > 0):
                    // Hitung total untuk persentase
                    $colors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary'];
                    $ci = 0;
                    while ($kat = mysqli_fetch_assoc($q_kategori)):
                        $pct = $total_tanaman > 0 ? round(($kat['jumlah'] / $total_tanaman) * 100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?php echo htmlspecialchars($kat['kategori']); ?></span>
                        <span class="small text-muted"><?php echo $kat['jumlah']; ?> tanaman (<?php echo $pct; ?>%)</span>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar bg-<?php echo $colors[$ci % count($colors)]; ?>"
                             role="progressbar"
                             style="width: <?php echo $pct; ?>%"
                             aria-valuenow="<?php echo $pct; ?>"
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                <?php $ci++; endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">
                        <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                        Belum ada data kategori.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /row bawah -->

<!-- Tombol aksi cepat -->
<div class="mt-4 d-flex gap-2 flex-wrap">
    <a href="tanaman/tambah.php" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i>Tambah Tanaman
    </a>
    <a href="tanaman/index.php" class="btn btn-outline-success">
        <i class="bi bi-table me-1"></i>Lihat Data
    </a>
    <a href="tanaman/report.php" target="_blank" class="btn btn-outline-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>
