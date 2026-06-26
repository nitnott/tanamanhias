/**
 * assets/js/script.js
 * JavaScript utama - Sistem Data Tanaman Hias
 */

/* ─── Sidebar Toggle (Mobile) ─── */
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar       = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    /* ─── Auto-hide alert setelah 4 detik ─── */
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 4000);
    });

    /* ─── Konfirmasi hapus data ─── */
    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const url  = this.getAttribute('href');
            const nama = this.getAttribute('data-nama') || 'data ini';
            if (confirm('Apakah Anda yakin ingin menghapus "' + nama + '"?\nTindakan ini tidak dapat dibatalkan.')) {
                window.location.href = url;
            }
        });
    });

    /* ─── Preview gambar sebelum upload ─── */
    const inputGambar = document.getElementById('gambar');
    const previewImg  = document.getElementById('previewGambar');

    if (inputGambar && previewImg) {
        inputGambar.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                // Validasi tipe
                const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowed.includes(file.type)) {
                    alert('Tipe file tidak didukung. Gunakan JPG/JPEG/PNG.');
                    this.value = '';
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    return;
                }
                // Validasi ukuran (2 MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file melebihi 2 MB. Pilih file yang lebih kecil.');
                    this.value = '';
                    previewImg.src = '';
                    previewImg.classList.add('d-none');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '';
                previewImg.classList.add('d-none');
            }
        });
    }

    /* ─── Bootstrap Form Validation ─── */
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    /* ─── Format harga otomatis (ribuan) ─── */
    const hargaInput = document.getElementById('harga');
    if (hargaInput) {
        hargaInput.addEventListener('input', function () {
            // Hanya izinkan angka
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    /* ─── Tooltip Bootstrap ─── */
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
