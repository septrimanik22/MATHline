<?php
// teacher_login.php - VERSI SIMPLE DAN RAPI
session_start();

require_once 'includes/db_connection.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>");
}

// Daftar guru yang diizinkan (nama unik)
$allowed_teachers = [
    'Septriana Manik' => 'admin',
    'Budi Santoso' => 'guru',
    'Siti Rahayu' => 'guru',
    'Agus Supriyadi' => 'guru',
    'Dewi Lestari' => 'guru',
    'Rina Wulandari' => 'guru'
];

// ========== CEK JIKA SUDAH LOGIN ==========
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'guru' || $_SESSION['role'] === 'admin') {
        header('Location: ' . ($_SESSION['role'] === 'admin' ? './admin/dashboard.php' : './teacher/dashboard.php'));
        exit();
    }
}

$error = '';
$success = '';

// Cek jika ada pesan dari redirect
if (isset($_GET['message'])) {
    $success = urldecode($_GET['message']);
}

// ========== PROSES LOGIN ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    
    if (empty($full_name)) {
        $error = "Nama tidak boleh kosong!";
    } else {
        // Cek apakah nama ada di daftar guru yang diizinkan
        if (array_key_exists($full_name, $allowed_teachers)) {
            $role = $allowed_teachers[$full_name];
            
            // Cek apakah guru sudah ada di database
            $stmt = $conn->prepare("SELECT * FROM users WHERE full_name = ? AND role IN ('guru', 'admin')");
            $stmt->bind_param("s", $full_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Guru sudah ada di database
                $user = $result->fetch_assoc();
            } else {
                // Guru belum ada, buat user baru secara otomatis
                $username = strtolower(str_replace(' ', '.', $full_name));
                $email = $username . '@mathline.sch.id';
                $hashed_password = password_hash('guru123', PASSWORD_DEFAULT);
                
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, full_name, role, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $insert_stmt->bind_param("sssss", $username, $email, $full_name, $role, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $user_id = $conn->insert_id;
                    
                    // Ambil data user yang baru dibuat
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                } else {
                    $error = "Gagal membuat akun guru. Silahkan hubungi administrator.";
                }
            }
            
            if (isset($user) && $user) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_teacher'] = true;
                
                // Log aktivitas login
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address, user_agent) VALUES (?, 'teacher_login', ?, ?)");
                $log_stmt->bind_param("iss", $user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                $log_stmt->execute();
                
                // Redirect ke dashboard yang sesuai
                if ($user['role'] === 'admin') {
                    header('Location: ./admin/dashboard.php');
                } else {
                    header('Location: ./teacher/dashboard.php');
                }
                exit();
            }
        } else {
            $error = "Nama '<strong>" . htmlspecialchars($full_name) . "</strong>' tidak terdaftar sebagai guru/admin.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Login Guru - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --teacher-color: #4e73df;
            --teacher-dark: #224abe;
            --teacher-light: #e8eaf6;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --border-color: #e5e7eb;
            --gray-light: #f8f9fa;
            --gray-medium: #dee2e6;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: clamp(0.5rem, 2vw, 1rem);
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Container Responsive */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: clamp(0.5rem, 2vw, 1rem);
        }
        
        .login-container {
            width: 100%;
            max-width: min(420px, 90vw);
            margin: 0 auto;
        }
        
        /* Login Card */
        .login-card {
            border: none;
            border-radius: clamp(12px, 2.5vw, 20px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            width: 100%;
            min-height: 480px;
            display: flex;
            flex-direction: column;
        }
        
        /* Login Header */
        .login-header {
            background: linear-gradient(135deg, var(--teacher-color) 0%, var(--teacher-dark) 100%);
            color: white;
            padding: clamp(1.5rem, 3vw, 2.5rem);
            text-align: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,100 L100,0 L100,100 Z" fill="white" opacity="0.1"/></svg>');
        }
        
        /* Teacher Icon */
        .teacher-icon {
            font-size: clamp(3rem, 6vw, 4rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        /* Logo Text */
        .logo-text {
            font-weight: 800;
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            background: linear-gradient(135deg, white, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        /* Login Body */
        .login-body {
            padding: clamp(1.25rem, 2.5vw, 2rem);
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-section {
            flex: 1;
        }
        
        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            border: 2px solid var(--border-color);
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 15px);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            transition: all 0.3s;
            width: 100%;
            min-height: 50px;
        }
        
        .form-control:focus {
            border-color: var(--teacher-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            outline: none;
        }
        
        /* Button Container - FIXED LAYOUT */
        .button-container {
            margin-top: auto;
            padding-top: clamp(1.5rem, 3vw, 2rem);
            display: flex;
            flex-direction: column;
            gap: clamp(0.75rem, 1.5vw, 1rem);
            width: 100%;
        }
        
        /* Login Button */
        .btn-login {
            background: linear-gradient(135deg, var(--teacher-color) 0%, var(--teacher-dark) 100%);
            border: none;
            color: white;
            padding: clamp(10px, 2vw, 12px);
            border-radius: clamp(8px, 1.5vw, 10px);
            font-weight: 600;
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            transition: all 0.3s;
            width: 100%;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            order: 1;
        }
        
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
        }
        
        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }
        
        /* Kembali ke Home Button */
        .btn-back-home {
            background: white;
            border: 2px solid var(--gray-medium);
            color: var(--text-gray);
            padding: clamp(10px, 2vw, 12px);
            border-radius: clamp(8px, 1.5vw, 10px);
            font-weight: 600;
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            transition: all 0.3s;
            width: 100%;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            order: 2;
        }
        
        .btn-back-home:hover {
            background: var(--gray-light);
            border-color: var(--text-gray);
            color: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-back-home:active {
            transform: translateY(0);
        }
        
        /* Demo Info */
        .demo-info {
            margin-top: clamp(1.5rem, 3vw, 2rem);
            padding: clamp(0.75rem, 1.5vw, 1rem);
            background: var(--gray-light);
            border-radius: clamp(8px, 1.5vw, 10px);
            border-left: 4px solid var(--teacher-color);
            order: 3;
        }
        
        .demo-info p {
            margin: 0;
            font-size: clamp(0.8rem, 1.6vw, 0.9rem);
            color: var(--text-gray);
            line-height: 1.5;
        }
        
        .demo-info strong {
            color: var(--text-dark);
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
            gap: 1rem;
        }
        
        .spinner-border {
            width: clamp(2.5rem, 5vw, 3rem);
            height: clamp(2.5rem, 5vw, 3rem);
            color: var(--teacher-color);
        }
        
        .loading-text {
            color: var(--teacher-dark);
            font-size: clamp(1rem, 2vw, 1.1rem);
            font-weight: 600;
        }
        
        /* Alerts */
        .alert {
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(0.75rem, 1.5vw, 1rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            font-size: clamp(0.85rem, 1.6vw, 0.9rem);
        }
        
        /* Responsive Breakpoints */
        @media (min-width: 768px) {
            .login-container {
                max-width: 420px;
            }
            
            .button-container {
                flex-direction: row;
                gap: clamp(0.75rem, 1.5vw, 1rem);
            }
            
            .btn-login, .btn-back-home {
                flex: 1;
                width: auto;
            }
        }
        
        @media (max-width: 992px) {
            body {
                padding: 0.75rem;
            }
            
            .login-header {
                padding: 1.75rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .logo-text {
                font-size: 1.8rem;
            }
            
            .teacher-icon {
                font-size: 3.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            
            .login-container {
                max-width: 95vw;
                margin: auto;
            }
            
            .login-card {
                border-radius: 16px;
                min-height: auto;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-body {
                padding: 1.25rem;
            }
            
            .logo-text {
                font-size: 1.6rem;
            }
            
            .teacher-icon {
                font-size: 3rem;
                margin-bottom: 0.5rem;
            }
            
            .form-control {
                min-height: 48px;
                font-size: 1rem;
            }
            
            .button-container {
                padding-top: 1.5rem;
            }
            
            .btn-login, .btn-back-home {
                min-height: 50px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
                align-items: center;
                justify-content: center;
            }
            
            .login-card {
                border-radius: 14px;
            }
            
            .login-header {
                padding: 1.25rem;
            }
            
            .login-body {
                padding: 1rem;
            }
            
            .logo-text {
                font-size: 1.4rem;
            }
            
            .teacher-icon {
                font-size: 2.5rem;
            }
            
            .form-label {
                font-size: 0.95rem;
            }
            
            .form-control {
                padding: 10px 12px;
                min-height: 46px;
                font-size: 0.95rem;
            }
            
            .button-container {
                padding-top: 1.25rem;
                gap: 0.75rem;
            }
            
            .btn-login, .btn-back-home {
                min-height: 48px;
                font-size: 0.95rem;
            }
            
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
            
            .demo-info {
                margin-top: 1.25rem;
                padding: 0.75rem;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 375px) {
            .login-header {
                padding: 1rem;
            }
            
            .login-body {
                padding: 0.875rem;
            }
            
            .logo-text {
                font-size: 1.3rem;
            }
            
            .teacher-icon {
                font-size: 2.2rem;
            }
            
            .form-control {
                padding: 9px 11px;
                min-height: 44px;
                font-size: 0.9rem;
            }
            
            .button-container {
                padding-top: 1rem;
                gap: 0.625rem;
            }
            
            .btn-login, .btn-back-home {
                min-height: 46px;
                font-size: 0.9rem;
                padding: 9px;
            }
            
            .alert {
                padding: 0.625rem;
                font-size: 0.85rem;
            }
            
            .demo-info {
                padding: 0.625rem;
                font-size: 0.8rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn-login:hover:not(:disabled),
            .btn-back-home:hover {
                transform: none;
                box-shadow: none;
            }
            
            .btn-login:active:not(:disabled),
            .btn-back-home:active {
                opacity: 0.8;
                transform: scale(0.98);
            }
            
            /* Larger touch targets */
            .form-control, .btn-login, .btn-back-home {
                min-height: 52px;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            body {
                align-items: flex-start;
                padding-top: 0.5rem;
            }
            
            .login-container {
                max-width: 90vw;
                margin: 0 auto;
            }
            
            .login-card {
                min-height: auto;
            }
            
            .login-header {
                padding: 1rem;
            }
            
            .login-body {
                padding: 1rem;
            }
            
            .teacher-icon {
                font-size: 2rem;
                margin-bottom: 0.25rem;
            }
            
            .logo-text {
                font-size: 1.3rem;
                margin-bottom: 0;
            }
            
            .form-control, .btn-login, .btn-back-home {
                min-height: 44px;
                padding: 8px 12px;
            }
            
            .demo-info {
                display: none;
            }
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white !important;
            }
            
            .login-card {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .login-header {
                background: white !important;
                color: black !important;
                border-bottom: 2px solid #000;
            }
            
            .logo-text {
                -webkit-text-fill-color: black;
                color: black;
            }
            
            .btn-login, .btn-back-home {
                display: none !important;
            }
        }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .teacher-icon {
                animation: none;
            }
            
            .btn-login, .btn-back-home {
                transition: none;
            }
            
            .form-control {
                transition: none;
            }
        }
        
        /* Helper class to prevent content shift */
        .content-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="loading-text">Memproses login...</div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <div class="login-card">
                        <div class="login-header">
                            <div class="teacher-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h1 class="logo-text">MATHLine</h1>
                        </div>
                        
                        <div class="login-body">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $success; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-section">
                                <form method="POST" id="loginForm" novalidate>
                                    <div class="mb-4">
                                        <label for="full_name" class="form-label">
                                            <i class="fas fa-user-tie"></i>
                                            <span>Nama Lengkap</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="full_name" 
                                               name="full_name" 
                                               placeholder="Contoh: Budi Santoso"
                                               required
                                               autofocus
                                               autocomplete="name"
                                               autocapitalize="words">
                                        <div class="form-text text-muted mt-1">
                                            Masukkan nama lengkap
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="button-container">
                                <button type="submit" form="loginForm" class="btn btn-login" id="loginButton">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Masuk</span>
                                </button>
                                
                                <a href="index.php" class="btn-back-home">
                                    <i class="fas fa-home"></i>
                                    <span>Home</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto capitalize first letter of each word
        document.getElementById('full_name').addEventListener('input', function(e) {
            this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
        });
        
        // Form validation and submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nameInput = document.getElementById('full_name');
            const loginButton = document.getElementById('loginButton');
            const name = nameInput.value.trim();
            
            // Reset previous error states
            nameInput.classList.remove('is-invalid');
            
            if (name.length < 3) {
                nameInput.classList.add('is-invalid');
                showAlert('error', 'Nama harus minimal 3 karakter!');
                nameInput.focus();
                return false;
            }
            
            // Check for at least two words (first name and last name)
            const nameParts = name.split(' ').filter(part => part.length > 0);
            if (nameParts.length < 2) {
                nameInput.classList.add('is-invalid');
                showAlert('error', 'Mohon masukkan nama lengkap (minimal 2 kata)!');
                nameInput.focus();
                return false;
            }
            
            // Disable button and show loading
            loginButton.disabled = true;
            loginButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Submit form after a short delay for visual feedback
            setTimeout(() => {
                this.submit();
            }, 500);
            
            return true;
        });
        
        // Function to show alert
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert-dismissible');
            existingAlerts.forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert after the header
            const loginBody = document.querySelector('.login-body');
            const formSection = loginBody.querySelector('.form-section');
            loginBody.insertBefore(alertDiv, formSection);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode && !document.body.contains(document.querySelector('.show'))) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Auto focus on name field with delay for mobile
        setTimeout(() => {
            const nameField = document.getElementById('full_name');
            if (nameField && !nameField.value) {
                nameField.focus();
            }
        }, 300);
        
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+Enter or Cmd+Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                document.getElementById('loginForm').requestSubmit();
            }
            
            // Escape to clear form
            if (e.key === 'Escape') {
                document.getElementById('full_name').value = '';
            }
        });
        
        // Handle window resize for responsive adjustments
        function adjustLayout() {
            const isMobile = window.innerWidth < 768;
            const isLandscape = window.innerWidth > window.innerHeight;
            
            if (isMobile) {
                // Center align on mobile
                document.body.style.alignItems = 'center';
                document.body.style.justifyContent = 'center';
                
                if (isLandscape) {
                    // Adjust for landscape on mobile
                    document.body.style.alignItems = 'flex-start';
                    document.body.style.paddingTop = '0.5rem';
                    
                    // Hide demo info in landscape to save space
                    const demoInfo = document.querySelector('.demo-info');
                    if (demoInfo) {
                        demoInfo.style.display = 'none';
                    }
                } else {
                    // Show demo info in portrait
                    const demoInfo = document.querySelector('.demo-info');
                    if (demoInfo) {
                        demoInfo.style.display = 'block';
                    }
                }
            } else {
                // Desktop layout
                document.body.style.alignItems = 'center';
                document.body.style.justifyContent = 'center';
            }
        }
        
        // Initial adjustment
        adjustLayout();
        
        // Adjust on resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(adjustLayout, 250);
        });
        
        // Touch device detection
        if ('ontouchstart' in window || navigator.maxTouchPoints) {
            document.body.classList.add('touch-device');
            
            // Add touch feedback
            const buttons = document.querySelectorAll('.btn-login, .btn-back-home');
            buttons.forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.classList.add('active');
                });
                
                btn.addEventListener('touchend', function() {
                    this.classList.remove('active');
                });
            });
        }
        
        // Ensure proper vertical centering on page load
        window.addEventListener('load', function() {
            const vh = window.innerHeight;
            const cardHeight = document.querySelector('.login-card').offsetHeight;
            
            if (cardHeight < vh * 0.8) {
                document.body.style.alignItems = 'center';
            } else {
                document.body.style.alignItems = 'flex-start';
                document.body.style.paddingTop = '1rem';
            }
        });
    </script>
</body>
</html>