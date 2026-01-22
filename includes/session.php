<?php
// includes/session.php

// Session configuration harus dipanggil SEBELUM session_start()
session_set_cookie_params([
    'lifetime' => 3600, // 1 jam
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session hanya jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    session_unset();
    session_destroy();
    header('Location: ' . SITE_URL . 'login.php?timeout=1');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>