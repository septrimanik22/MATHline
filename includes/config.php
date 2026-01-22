<?php
// includes/config.php - VERSI DIPERBAIKI

/* ===============================
   DATABASE CONFIGURATION
================================ */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'MATHLine');

/* ===============================
   SITE CONFIGURATION
================================ */
define('SITE_URL', 'http://localhost/MATHLine/');
define('SITE_NAME', 'MATHLine - PBL Learning System');
define('ROOT_PATH', dirname(dirname(__FILE__)));

/* ===============================
   SECURITY CONFIGURATION
================================ */
define('MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_TIMEOUT', 3600);
define('CSRF_TOKEN_EXPIRY', 1800);

/* ===============================
   UPLOAD CONFIGURATION
================================ */
define('UPLOAD_MAX_SIZE', 10485760);
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf,doc,docx,ppt,pptx,zip,rar');

/* ===============================
   SYSTEM CONFIGURATION
================================ */
define('DEBUG_MODE', true);
define('MAINTENANCE_MODE', false);
define('TIMEZONE', 'Asia/Jakarta');

/* ===============================
   APPLICATION SETTINGS
================================ */
define('ITEMS_PER_PAGE', 10);
define('PASSWORD_MIN_LENGTH', 6);
define('USERNAME_MIN_LENGTH', 3);
define('MAX_FILE_UPLOADS', 5);
define('DEFAULT_AVATAR', SITE_URL . 'assets/images/default-avatar.png');

/* ===============================
   ERROR HANDLING
================================ */
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');
}

/* ===============================
   TIMEZONE SETUP
================================ */
date_default_timezone_set(TIMEZONE);

/* ===============================
   SESSION CONFIGURATION
   - Hanya set parameter, TIDAK mulai session di sini
================================ */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set true jika pakai HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

/* ===============================
   HELPER FUNCTIONS
================================ */

/**
 * Sanitize input untuk mencegah XSS
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect ke URL tertentu
 */
function redirect($path) {
    $url = SITE_URL . ltrim($path, '/');
    header('Location: ' . $url);
    exit;
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah user memiliki role tertentu
 */
function hasRole($role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Require login - jika belum login, redirect ke login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Require role - jika tidak punya role, redirect sesuai role
 */
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        if (hasRole('guru')) {
            redirect('admin/dashboard.php');
        } else {
            redirect('student/dashboard.php');
        }
    }
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Format tanggal Indonesia
 */
function formatDateID($date, $show_time = false) {
    if (empty($date)) return '-';
    
    $timestamp = strtotime($date);
    if ($timestamp === false) return $date;
    
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $format = $hari[date('w', $timestamp)] . ', ' . 
              date('d', $timestamp) . ' ' . 
              $bulan[date('n', $timestamp) - 1] . ' ' . 
              date('Y', $timestamp);
    
    if ($show_time) {
        $format .= ' ' . date('H:i', $timestamp);
    }
    
    return $format;
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
        'timestamp' => time()
    ];
}

/**
 * Get dan clear flash message
 */
function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    
    if ($flash) {
        $type = $flash['type'];
        $message = $flash['message'];
        
        $alert_classes = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $alert_class = $alert_classes[$type] ?? 'alert-info';
        
        return '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
    
    return '';
}

// ===============================
// CREATE DIRECTORIES JIKA BELUM ADA
// ===============================
if (!file_exists(ROOT_PATH . '/logs')) {
    @mkdir(ROOT_PATH . '/logs', 0755, true);
}

if (!file_exists(UPLOAD_PATH)) {
    @mkdir(UPLOAD_PATH, 0755, true);
    @mkdir(UPLOAD_PATH . 'materials', 0755, true);
    @mkdir(UPLOAD_PATH . 'submissions', 0755, true);
    @mkdir(UPLOAD_PATH . 'avatars', 0755, true);
}
?>