<?php
/**
 * tanaman/edit.php
 * Form edit data tanaman hias yang sudah ada.
 */

$base_url   = '../';
$page_title = 'Edit Tanaman';
require_once '../config/koneksi.php';
require_once '../includes/header.php';

// Ambil id dari URL, validasi
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'ID tanaman tidak valid.';
    header('Location: index.php');
    exit();
}

// Ambil data tanaman yang akan diedit
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

// Daftar kategori
$kategori_list = ['Daun Lebar', 'Daun Indah', 'Sukulen', 'Bunga', 'Kaktus', 'Paku-pakuan', 'Anggrek', 'Bromelia', 'Lainnya'];
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Data Tanaman</a></li>
        <li class="breadcrumb-item active">Edit Tanaman</li>
    </ol>
</nav>

<h4 class="fw-bold mb-4">
    <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Tanaman Hias
    <small class="fs-6 text-muted fw-normal ms-2">
        <?php echo htmlspecialchars($row['nama_tanaman']); ?>
    </small>
</h4>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="form-card">

            <form action="update.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- Hidden: ID tanaman -->
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <!-- Hidden: Gambar lama (untuk keperluan hapus saat update) -->
                <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($row['gambar'] ?? ''); ?>">

                <div class="row g-3">

                    <!-- Kode Tanaman -->
                    <div class="col-md-4">
                        <label for="kode_tanaman" class="form-label">
                            Kode Tanaman <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="kode_tanaman"
                               name="kode_tanaman"
                               class="form-control"
                               value="<?php echo htmlspecialchars($row['kode_tanaman']); ?>"
                               maxlength="20"
                               required>
                        <div class="invalid-feedback">Kode tanaman wajib diisi.</div>
                    </div>

                    <!-- Nama Tanaman -->
                    <div class="col-md-8">
                        <label for="nama_tanaman" class="form-label">
                            Nama Tanaman <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="nama_tanaman"
                               name="nama_tanaman"
                               class="form-control"
                               value="<?php echo htmlspecialchars($row['nama_tanaman']); ?>"
                               maxlength="150"
                               required>
                        <div class="invalid-feedback">Nama tanaman wajib diisi.</div>
                    </div>

                    <!-- Kategori -->
                    <div class="col-md-6">
                        <label for="kategori" class="form-label">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select id="kategori" name="kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?php echo htmlspecialchars($kat); ?>"
                                <?php echo ($row['kategori'] === $kat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kat); ?>
                            </option>
                            <?php endforeach; ?>
                            <!-- Jika kategori tidak ada di list, tetap tampil -->
                            <?php if (!in_array($row['kategori'], $kategori_list)): ?>
                            <option value="<?php echo htmlspecialchars($row['kategori']); ?>" selected>
                                <?php echo htmlspecialchars($row['kategori']); ?>
                            </option>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Pilih kategori tanaman.</div>
                    </div>

                    <!-- Tanggal Input -->
                    <div class="col-md-6">
                        <label for="tanggal_input" class="form-label">
                            Tanggal Input <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               id="tanggal_input"
                               name="tanggal_input"
                               class="form-control"
                               value="<?php echo htmlspecialchars($row['tanggal_input']); ?>"
                               required>
                        <div class="invalid-feedback">Tanggal input wajib diisi.</div>
                    </div>

                    <!-- Harga -->
                    <div class="col-md-6">
                        <label for="harga" class="form-label">
                            Harga (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number"
                                   id="harga"
                                   name="harga"
                                   class="form-control"
                                   value="<?php echo (int)$row['harga']; ?>"
                                   min="0"
                                   step="500"
                                   required>
                        </div>
                        <div class="invalid-feedback">Harga wajib diisi.</div>
                    </div>

                    <!-- Stok -->
                    <div class="col-md-6">
                        <label for="stok" class="form-label">
                            Stok <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               id="stok"
                               name="stok"
                               class="form-control"
                               value="<?php echo (int)$row['stok']; ?>"
                               min="0"
                               required>
                        <div class="invalid-feedback">Stok wajib diisi.</div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  class="form-control"
                                  rows="4"><?php echo htmlspecialchars($row['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <!-- Gambar Saat Ini -->
                    <div class="col-12">
                        <label class="form-label">Foto Saat Ini</label>
                        <div class="mb-2">
                            <?php if ($row['gambar'] && file_exists('../assets/upload/' . $row['gambar'])): ?>
                                <img src="../assets/upload/<?php echo htmlspecialchars($row['gambar']); ?>"
                                     alt="Foto saat ini"
                                     class="img-thumbnail"
                                     style="max-width:180px; max-height:180px; object-fit:cover;">
                                <p class="text-muted small mt-1">
                                    <i class="bi bi-image me-1"></i><?php echo htmlspecialchars($row['gambar']); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted small"><i class="bi bi-image me-1"></i>Belum ada foto.</p>
                            <?php endif; ?>
                        </div>

                        <label for="gambar" class="form-label">Ganti Foto (Opsional)</label>
                        <input type="file"
                               id="gambar"
                               name="gambar"
                               class="form-control"
                               accept=".jpg,.jpeg,.png">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Kosongkan jika tidak ingin mengganti foto. Format: JPG/JPEG/PNG. Maks. 2 MB.
                            Foto lama akan dihapus otomatis saat diganti.
                        </div>
                        <!-- Preview gambar baru -->
                        <div class="mt-2">
                            <img id="previewGambar"
                                 src=""
                                 alt="Preview baru"
                                 class="img-thumbnail d-none"
                                 style="max-width:180px; max-height:180px; object-fit:cover;">
                        </div>
                    </div>

                </div><!-- /row g-3 -->

                <!-- Tombol -->
                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning px-4 fw-semibold">
                        <i class="bi bi-save me-2"></i>Update Data
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Batal
                    </a>
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-info ms-auto px-4">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                </div>

            </form>
        </div><!-- /form-card -->
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
