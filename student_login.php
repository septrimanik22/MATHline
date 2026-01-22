<?php
// student_login.php
session_start();
require_once 'includes/db_connection.php';

// PERBAIKAN: Gunakan getInstance() bukan new Database()
$db = Database::getInstance();
$conn = $db->getConnection();

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'siswa') {
        header('Location: student/dashboard.php');
        exit();
    }
}

$error = '';
$success = '';

// Cek jika ada pesan dari redirect
if (isset($_GET['message'])) {
    $success = urldecode($_GET['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    
    if (empty($full_name)) {
        $error = "Nama tidak boleh kosong!";
    } else {
        // Cek apakah siswa ada di database
        $stmt = $conn->prepare("SELECT u.*, s.class FROM users u 
                                LEFT JOIN students s ON u.id = s.user_id 
                                WHERE u.full_name = ? AND u.role = 'siswa'");
        $stmt->bind_param("s", $full_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['class'] = $user['class'];
            
            // Log aktivitas login
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address, user_agent) 
                                        VALUES (?, 'login', ?, ?)");
            $log_stmt->bind_param("iss", $user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            $log_stmt->execute();
            
            // Redirect ke dashboard siswa
            header('Location: student/dashboard.php');
            exit();
        } else {
            $error = "Nama '<strong>" . htmlspecialchars($full_name) . "</strong>' tidak ditemukan. Silakan hubungi guru Anda untuk didaftarkan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Login Siswa - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #1cc88a;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 15px;
        }
        
        .login-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--success-color) 0%, #36b9cc 100%);
            color: white;
            padding: 1.5rem 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        @media (min-width: 768px) {
            .login-header {
                padding: 2.5rem;
            }
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L50,100 Z" fill="white" opacity="0.1"/></svg>');
        }
        
        .student-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        
        @media (min-width: 768px) {
            .student-icon {
                font-size: 4rem;
                margin-bottom: 1rem;
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-body {
            padding: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .login-body {
                padding: 2.5rem;
            }
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        @media (min-width: 768px) {
            .form-control {
                padding: 15px;
                font-size: 1.1rem;
            }
        }
        
        .form-control:focus {
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.25rem rgba(28, 200, 138, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--success-color) 0%, #36b9cc 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .btn-login {
                padding: 15px;
                font-size: 1.1rem;
            }
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(28, 200, 138, 0.3);
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.8rem;
            background: linear-gradient(135deg, white, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        @media (min-width: 768px) {
            .logo-text {
                font-size: 2.5rem;
                font-weight: 800;
            }
        }
        
        .example-names {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 12px;
            margin-top: 15px;
            overflow-x: auto;
        }
        
        .name-badge {
            display: inline-block;
            background: white;
            padding: 6px 12px;
            margin: 3px;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        @media (min-width: 768px) {
            .name-badge {
                padding: 8px 15px;
                margin: 5px;
                font-size: 0.9rem;
            }
        }
        
        .name-badge:hover {
            background: var(--success-color);
            color: white;
            transform: scale(1.05);
        }
        
        .student-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            text-align: center;
            margin-top: 15px;
            gap: 10px;
        }
        
        .feature-item {
            flex: 1 0 calc(50% - 10px);
            min-width: 120px;
            padding: 8px;
        }
        
        @media (min-width: 768px) {
            .feature-item {
                flex: 1;
                padding: 10px;
                min-width: auto;
            }
        }
        
        .feature-item i {
            font-size: 1.2rem;
            color: var(--success-color);
            margin-bottom: 8px;
        }
        
        @media (min-width: 768px) {
            .feature-item i {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
        }
        
        .alert {
            font-size: 0.9rem;
            padding: 12px 15px;
        }
        
        @media (min-width: 768px) {
            .alert {
                font-size: 1rem;
                padding: 15px 20px;
            }
        }
        
        .btn-outline-primary, .btn-outline-secondary {
            padding: 10px;
            font-size: 0.9rem;
        }
        
        @media (min-width: 768px) {
            .btn-outline-primary, .btn-outline-secondary {
                padding: 12px;
                font-size: 1rem;
            }
        }
        
        /* Responsive adjustments */
        .row.g-3 {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
        
        .row.g-3 > [class*="col-"] {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        /* Fix for mobile tap targets */
        .btn, .form-control, .name-badge {
            min-height: 44px;
        }
        
        .name-badge {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Prevent horizontal scrolling */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .container {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        @media (min-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
        
        /* Better touch feedback */
        @media (hover: none) and (pointer: coarse) {
            .btn-login:active {
                transform: translateY(-1px);
                opacity: 0.9;
            }
            
            .name-badge:active {
                transform: scale(0.95);
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0">
        <div class="row justify-content-center mx-0">
            <div class="col-12 col-md-8 col-lg-6 px-3 px-md-4">
                <div class="login-card">
                    <div class="login-header">
                        <div class="student-icon">
                            <i class="fas fa-user-graduate"></i>
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
                        
                        <form method="POST" id="loginForm">
                            <div class="mb-4">
                                <label for="full_name" class="form-label fw-bold fs-5">
                                    <i class="fas fa-user-circle me-2"></i>Nama Lengkap Anda
                                </label>
                                <input type="text" class="form-control" 
                                       id="full_name" name="full_name" 
                                       placeholder="Contoh: Ahmad Budi Santoso"
                                       required
                                       autofocus>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan nama lengkap seperti yang didaftarkan oleh guru
                                </div>
                            </div>
                            
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Kelas
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <a href="login.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Login Guru
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="index.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-home me-1"></i>Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Information Card -->
                <div class="card mt-3 mt-md-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-warning mt-0 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Hanya siswa yang sudah didaftarkan oleh guru yang dapat login.
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
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const nameInput = document.getElementById('full_name');
            const name = nameInput.value.trim();
            
            if (name.length < 3) {
                e.preventDefault();
                alert('Nama harus minimal 3 karakter!');
                nameInput.focus();
                return false;
            }
            
            // Check for at least two words (first name and last name)
            const nameParts = name.split(' ').filter(part => part.length > 0);
            if (nameParts.length < 2) {
                e.preventDefault();
                alert('Mohon masukkan nama lengkap (minimal 2 kata)!');
                nameInput.focus();
                return false;
            }
            
            return true;
        });
        
        // Auto focus on name field
        document.getElementById('full_name').focus();
        
        // Prevent zoom on mobile when focusing input
        document.addEventListener('touchstart', function() {}, {passive: true});
        
        // Better touch handling for mobile
        if ('ontouchstart' in window) {
            document.documentElement.style.cursor = 'pointer';
        }
    </script>
</body>
</html>