<?php
/**
 * index.php (root)
 * Entry point: redirect ke dashboard jika sudah login, atau ke login.
 */

session_start();

if (isset($_SESSION['status']) && $_SESSION['status'] === 'login') {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
