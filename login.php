<?php
/**
 * login.php
 * Halaman login pengguna
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['status']) && $_SESSION['status'] === 'login') {
    header('Location: dashboard.php');
    exit();
}

// Ambil pesan error jika ada (dikirim dari cek_login.php)
$error_msg = '';
if (isset($_SESSION['login_error'])) {
    $error_msg = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Sistem Tanaman Hias</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-5 col-lg-4">

                <!-- Logo / Icon -->
                <div class="login-logo mb-3">
                    <i class="bi bi-flower1"></i>
                </div>

                <!-- Judul -->
                <h4 class="text-center text-white fw-bold mb-1">Tanaman Hias</h4>
                <p class="text-center text-white-50 mb-4 small">Sistem Manajemen Data Tanaman</p>

                <!-- Card Login -->
                <div class="card login-card p-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-center fw-semibold">Masuk ke Sistem</h5>

                        <!-- Tampilkan error jika ada -->
                        <?php if ($error_msg): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error_msg); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Form Login -->
                        <form action="cek_login.php" method="POST" class="needs-validation" novalidate>

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-1"></i>Username
                                </label>
                                <input type="text"
                                       id="username"
                                       name="username"
                                       class="form-control"
                                       placeholder="Masukkan username"
                                       maxlength="50"
                                       required
                                       autofocus>
                                <div class="invalid-feedback">Username wajib diisi.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           placeholder="Masukkan password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                            data-bs-toggle="tooltip" title="Tampilkan/sembunyikan password">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Password wajib diisi.</div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                            </div>

                        </form>

                        <!-- Info akun default -->
                        <div class="mt-4 p-3 bg-light rounded small text-muted">
                            <strong><i class="bi bi-info-circle me-1"></i>Akun Default:</strong><br>
                            Username: <code>admin</code> &nbsp;|&nbsp; Password: <code>password</code>
                        </div>
                    </div>
                </div>

                <p class="text-center text-white-50 mt-3 small">
                    &copy; <?php echo date('Y'); ?> UAS Pemrograman Web 2
                </p>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle show/hide password
    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Bootstrap Form Validation
    (function () {
        'use strict';
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
    })();
</script>
</body>
</html>
