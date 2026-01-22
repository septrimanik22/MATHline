<?php
// includes/init.php
require_once 'config.php';
require_once 'db_connection.php';
require_once 'auth.php';
require_once 'functions.php';

// Auto-load classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Set default timezone
date_default_timezone_set('Asia/Jakarta');

// Handle CORS for API requests
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Initialize database connection
try {
    $database = Database::getInstance();
    $GLOBALS['db'] = $database;
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("System maintenance. Please try again later.");
    }
}

// Initialize authentication
$auth = new Auth();

// Check maintenance mode
if (MAINTENANCE_MODE && !$auth->isLoggedIn()) {
    if (basename($_SERVER['PHP_SELF']) != 'maintenance.php') {
        header('Location: ' . SITE_URL . 'maintenance.php');
        exit();
    }
}

// Set global variables
$GLOBALS['auth'] = $auth;
$GLOBALS['current_user'] = $auth->getUserInfo();

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Start output buffering
ob_start();
?>