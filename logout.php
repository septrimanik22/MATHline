<?php
// logout.php - VERSI DIPERBAIKI

// 1. Mulai session
session_start();

// 2. Hapus semua data session
$_SESSION = array();

// 3. Hapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 4. Hancurkan session
session_destroy();

// 5. Clear browser cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 6. ✅ DIUBAH: Redirect ke index.php bukan login.php
header('Location: index.php?message=' . urlencode('Anda telah berhasil logout.'));
exit();
?>