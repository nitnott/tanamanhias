<?php
/**
 * tanaman/tambah.php
 * Form tambah data tanaman hias baru.
 */

$base_url   = '../';
$page_title = 'Tambah Tanaman';
require_once '../config/koneksi.php';
require_once '../includes/header.php';

// Generate kode tanaman otomatis
$q_last = mysqli_query($koneksi, "SELECT kode_tanaman FROM tanaman ORDER BY id DESC LIMIT 1");
$kode_baru = 'TH-001';
if ($row_last = mysqli_fetch_assoc($q_last)) {
    $last_num  = (int) substr($row_last['kode_tanaman'], 3);
    $kode_baru = 'TH-' . str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
}

// Daftar kategori yang tersedia
$kategori_list = ['Daun Lebar', 'Daun Indah', 'Sukulen', 'Bunga', 'Kaktus', 'Paku-pakuan', 'Anggrek', 'Bromelia', 'Lainnya'];
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Data Tanaman</a></li>
        <li class="breadcrumb-item active">Tambah Tanaman</li>
    </ol>
</nav>

<h4 class="fw-bold mb-4">
    <i class="bi bi-plus-circle me-2 text-success"></i>Tambah Tanaman Hias
</h4>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="form-card">

            <form action="simpan.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

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
                               value="<?php echo htmlspecialchars($kode_baru); ?>"
                               maxlength="20"
                               required>
                        <div class="valid-feedback">Kode siap digunakan.</div>
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
                               placeholder="Contoh: Monstera Deliciosa"
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
                            <option value="<?php echo htmlspecialchars($kat); ?>">
                                <?php echo htmlspecialchars($kat); ?>
                            </option>
                            <?php endforeach; ?>
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
                               value="<?php echo date('Y-m-d'); ?>"
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
                                   placeholder="0"
                                   min="0"
                                   step="500"
                                   required>
                        </div>
                        <div class="invalid-feedback">Harga wajib diisi (minimal 0).</div>
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
                               placeholder="0"
                               min="0"
                               required>
                        <div class="invalid-feedback">Stok wajib diisi (minimal 0).</div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Tuliskan deskripsi singkat tentang tanaman ini..."></textarea>
                        <div class="form-text">Opsional. Maksimal 1000 karakter.</div>
                    </div>

                    <!-- Upload Gambar -->
                    <div class="col-12">
                        <label for="gambar" class="form-label">
                            Foto Tanaman
                        </label>
                        <input type="file"
                               id="gambar"
                               name="gambar"
                               class="form-control"
                               accept=".jpg,.jpeg,.png">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Format: JPG/JPEG/PNG. Maksimal 2 MB. Opsional.
                        </div>
                        <!-- Preview gambar -->
                        <div class="mt-2">
                            <img id="previewGambar"
                                 src=""
                                 alt="Preview"
                                 class="img-thumbnail d-none"
                                 style="max-width:200px; max-height:200px; object-fit:cover;">
                        </div>
                    </div>

                </div><!-- /row g-3 -->

                <!-- Tombol -->
                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-2"></i>Simpan Data
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Batal
                    </a>
                </div>

            </form>

        </div><!-- /form-card -->
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
