<?php
// admin/student_add.php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireRole(['admin', 'guru']);

$db = Database::getInstance();
$conn = $db->getConnection();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    
    // Validation
    if (empty($username)) $errors[] = "Username harus diisi";
    if (empty($full_name)) $errors[] = "Nama lengkap harus diisi";
    
    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    if (empty($errors)) {
        // Generate default password: "siswa123"
        $default_password = 'siswa123';
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
        
        // Generate email from username
        $email = $username . '@mathline.sch.id';
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert into users table
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, full_name, role, created_at) 
                VALUES (?, ?, ?, ?, 'siswa', NOW())
            ");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);
            $stmt->execute();
            
            $user_id = $conn->insert_id;
            
            // Insert into students table - CLASS FIXED TO "VIII"
            $stmt = $conn->prepare("
                INSERT INTO students (user_id, class, status) 
                VALUES (?, 'VIII', 'inactive')
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Siswa berhasil ditambahkan! Username: $username, Password default: siswa123";
            header("Location: students.php");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction
            $conn->rollback();
            $errors[] = "Gagal menambahkan siswa: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Tambah Siswa - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #10b981;
            --light-green: #d1fae5;
            --dark-green: #059669;
            --soft-green: #ecfdf5;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Main Container */
        .main-container {
            padding: clamp(0.75rem, 2vw, 1.5rem);
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Back Button */
        .back-to-dashboard {
            position: relative;
            top: 0;
            left: 0;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            width: 100%;
            display: flex;
            justify-content: center;
        }
        
        .btn-back {
            background: white;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            padding: clamp(8px, 1.5vw, 10px) clamp(12px, 2vw, 16px);
            border-radius: clamp(8px, 1.5vw, 10px);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: clamp(4px, 0.8vw, 8px);
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            min-height: 44px;
            max-width: 300px;
            width: 100%;
        }
        
        .btn-back:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }
        
        /* Header Section */
        .header-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            padding: clamp(1.5rem, 3vw, 2.5rem) clamp(0.5rem, 2vw, 1rem);
            margin-bottom: clamp(1.5rem, 3vw, 2.5rem);
            border-radius: 0 0 clamp(12px, 2.5vw, 20px) clamp(12px, 2.5vw, 20px);
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.1);
            width: 100%;
        }
        
        .header-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 clamp(0.5rem, 2vw, 1rem);
        }
        
        .header-title {
            font-size: clamp(1.5rem, 3.5vw, 2.2rem);
            font-weight: 800;
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            color: white;
            line-height: 1.3;
        }
        
        .header-subtitle {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            line-height: 1.5;
        }
        
        /* Form Container */
        .form-container {
            background: var(--card-bg);
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1.25rem, 2.5vw, 2rem);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            max-width: min(500px, 95vw);
            margin: 0 auto;
            width: 100%;
            border: 1px solid var(--border-color);
        }
        
        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
            display: block;
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
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }
        
        .form-text {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            color: var(--text-gray);
            margin-top: 0.5rem;
        }
        
        /* Info Card */
        .info-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #bbf7d0;
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(1rem, 1.8vw, 1.25rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .info-card h6 {
            color: #065f46;
            font-weight: 600;
            font-size: clamp(0.95rem, 1.8vw, 1.05rem);
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-card ul {
            margin: 0;
            padding-left: clamp(1rem, 2vw, 1.25rem);
        }
        
        .info-card li {
            color: var(--text-dark);
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            margin-bottom: clamp(0.25rem, 0.5vw, 0.5rem);
            line-height: 1.5;
        }
        
        /* Section Title */
        .section-title {
            color: var(--dark-green);
            font-size: clamp(1.1rem, 2.2vw, 1.25rem);
            font-weight: 600;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            padding-bottom: clamp(0.5rem, 1vw, 0.75rem);
            border-bottom: 2px solid var(--light-green);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Fixed Field (Kelas) */
        .fixed-field {
            background: var(--soft-green);
            border: 2px solid var(--border-color);
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 15px);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            min-height: 50px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Buttons */
        .button-group {
            display: flex;
            flex-direction: column;
            gap: clamp(0.75rem, 1.5vw, 1rem);
            margin-top: clamp(2rem, 4vw, 3rem);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
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
            order: 2;
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .btn-cancel {
            background: white;
            border: 2px solid var(--border-color);
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
            order: 1;
        }
        
        .btn-cancel:hover {
            background: #f8f9fa;
            border-color: var(--text-gray);
            color: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Alerts */
        .alert {
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(0.75rem, 1.5vw, 1rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            font-size: clamp(0.85rem, 1.6vw, 0.95rem);
        }
        
        .alert ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.5rem;
        }
        
        .alert li {
            margin-bottom: 0.25rem;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding: clamp(1rem, 2vw, 1.5rem) 0;
            width: 100%;
            margin-top: clamp(2rem, 4vw, 3rem);
        }
        
        footer p {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin: 0;
            text-align: center;
            color: var(--text-dark);
        }
        
        /* Responsive Breakpoints */
        @media (min-width: 768px) {
            .button-group {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .btn-cancel {
                width: auto;
                flex: 1;
                order: 1;
            }
            
            .btn-submit {
                width: auto;
                flex: 1;
                order: 2;
            }
        }
        
        @media (max-width: 992px) {
            .header-section {
                padding: clamp(1.25rem, 2.5vw, 1.75rem) clamp(0.75rem, 1.5vw, 1rem);
                border-radius: 0 0 16px 16px;
            }
            
            .header-title {
                font-size: 1.8rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-bottom: 2rem;
            }
            
            .main-container {
                padding: 0.75rem;
            }
            
            .header-section {
                padding: 1.5rem 0.75rem;
                border-radius: 0 0 14px 14px;
                margin-bottom: 1.5rem;
            }
            
            .header-title {
                font-size: 1.6rem;
            }
            
            .header-subtitle {
                font-size: 1rem;
            }
            
            .back-to-dashboard {
                margin-bottom: 1.25rem;
            }
            
            .form-container {
                padding: 1.25rem;
                border-radius: 12px;
            }
            
            .section-title {
                font-size: 1.1rem;
                margin-bottom: 1.25rem;
            }
            
            .form-control, .fixed-field {
                min-height: 48px;
                font-size: 1rem;
            }
            
            .info-card {
                padding: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .button-group {
                margin-top: 2rem;
            }
            
            .btn-submit, .btn-cancel {
                min-height: 50px;
                font-size: 1rem;
            }
            
            footer {
                padding: 1.25rem 0;
                margin-top: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .header-section {
                padding: 1.25rem 0.5rem;
                border-radius: 0 0 12px 12px;
            }
            
            .header-title {
                font-size: 1.4rem;
            }
            
            .header-subtitle {
                font-size: 0.9rem;
            }
            
            .btn-back {
                font-size: 0.9rem;
                padding: 8px 12px;
                min-height: 42px;
                max-width: 250px;
            }
            
            .form-container {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .form-label {
                font-size: 0.95rem;
            }
            
            .form-control, .fixed-field {
                padding: 10px 12px;
                min-height: 46px;
                font-size: 0.95rem;
            }
            
            .section-title {
                font-size: 1rem;
                margin-bottom: 1rem;
            }
            
            .info-card {
                padding: 0.875rem;
                margin-bottom: 1.25rem;
            }
            
            .info-card h6 {
                font-size: 0.95rem;
            }
            
            .info-card li {
                font-size: 0.85rem;
            }
            
            .button-group {
                margin-top: 1.5rem;
                gap: 0.75rem;
            }
            
            .btn-submit, .btn-cancel {
                min-height: 48px;
                font-size: 0.95rem;
            }
            
            .alert {
                padding: 0.75rem;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 375px) {
            .header-title {
                font-size: 1.3rem;
            }
            
            .header-subtitle {
                font-size: 0.85rem;
            }
            
            .form-container {
                padding: 0.875rem;
                border-radius: 8px;
            }
            
            .form-control, .fixed-field {
                padding: 9px 11px;
                min-height: 44px;
                font-size: 0.9rem;
            }
            
            .btn-back {
                font-size: 0.85rem;
                max-width: 220px;
            }
            
            .btn-submit, .btn-cancel {
                min-height: 46px;
                font-size: 0.9rem;
                padding: 9px;
            }
            
            .info-card {
                padding: 0.75rem;
            }
            
            .info-card h6 {
                font-size: 0.9rem;
            }
            
            .info-card li {
                font-size: 0.8rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn-back:hover,
            .btn-submit:hover:not(:disabled),
            .btn-cancel:hover {
                transform: none;
                box-shadow: none;
            }
            
            .btn-back:active,
            .btn-submit:active:not(:disabled),
            .btn-cancel:active {
                opacity: 0.8;
                transform: scale(0.98);
            }
            
            /* Larger touch targets */
            .form-control, .fixed-field,
            .btn-submit, .btn-cancel {
                min-height: 52px;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .header-section {
                padding: 1rem 0.75rem;
                margin-bottom: 1rem;
            }
            
            .form-container {
                padding: 1rem;
            }
            
            .info-card {
                display: none; /* Hide info card in landscape to save space */
            }
        }
        
        /* Print Styles */
        @media print {
            .header-section {
                background: white !important;
                color: black !important;
                border-bottom: 2px solid #000;
            }
            
            .header-title {
                color: black !important;
            }
            
            .form-container {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .btn-back, .btn-cancel, .btn-submit {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-container">
        <!-- Back to Dashboard Button -->
        <div class="back-to-dashboard">
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
        
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-content">
                <h1 class="header-title">
                    <i class="fas fa-user-plus me-2"></i>Tambah Siswa Baru
                </h1>
                <p class="header-subtitle">
                    Tambahkan akun siswa baru ke sistem MATHLine
                </p>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="container">
            <div class="info-card">
                <h6>
                    <i class="fas fa-info-circle"></i>
                    <span>Informasi Penting</span>
                </h6>
                <ul>
                    <li>Password default untuk siswa baru: <strong>siswa123</strong></li>
                    <li>Siswa akan otomatis dimasukkan ke Kelas <strong>VIII</strong></li>
                    <li>Status awal siswa adalah <strong>Tidak Aktif</strong></li>
                    <li>Email otomatis dibuat dari username (contoh: username@mathline.sch.id)</li>
                </ul>
            </div>
        </div>
        
        <!-- Alerts -->
        <div class="container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong class="me-1">Terjadi kesalahan:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Form -->
        <div class="container">
            <div class="form-container">
                <h5 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Informasi Siswa</span>
                </h5>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   placeholder="contoh: siswa001"
                                   required
                                   autocomplete="off"
                                   autocapitalize="off">
                            <div class="form-text">
                                Username untuk login siswa (huruf kecil, tanpa spasi)
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="full_name" 
                                   name="full_name" 
                                   placeholder="Contoh: Andi Pratama"
                                   required
                                   autocomplete="name"
                                   autocapitalize="words">
                            <div class="form-text">
                                Nama lengkap siswa
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Kelas</label>
                            <div class="fixed-field">
                                <i class="fas fa-graduation-cap text-success"></i>
                                <span class="fw-bold">VIII</span>
                                <small class="text-muted ms-auto">(Otomatis ditetapkan)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <a href="students.php" class="btn-cancel">
                            <i class="fas fa-times me-2"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i>
                            <span>Simpan Siswa</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> MATHLine | Pendidikan Matematika | Universitas Negeri Medan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto lowercase for username
        document.getElementById('username').addEventListener('input', function(e) {
            this.value = this.value.toLowerCase().replace(/\s/g, '');
        });
        
        // Auto capitalize for full name
        document.getElementById('full_name').addEventListener('input', function(e) {
            this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const fullName = document.getElementById('full_name').value.trim();
            
            // Reset previous error styles
            document.getElementById('username').classList.remove('is-invalid');
            document.getElementById('full_name').classList.remove('is-invalid');
            
            let isValid = true;
            
            // Username validation
            if (username.length < 3) {
                document.getElementById('username').classList.add('is-invalid');
                showAlert('Username harus minimal 3 karakter');
                isValid = false;
            }
            
            if (!/^[a-z0-9._]+$/.test(username)) {
                document.getElementById('username').classList.add('is-invalid');
                showAlert('Username hanya boleh huruf kecil, angka, titik, dan underscore');
                isValid = false;
            }
            
            // Full name validation
            const nameParts = fullName.split(' ').filter(part => part.length > 0);
            if (nameParts.length < 2) {
                document.getElementById('full_name').classList.add('is-invalid');
                showAlert('Nama lengkap harus minimal 2 kata');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showAlert(message) {
            // Remove existing custom alerts
            const existingAlerts = document.querySelectorAll('.custom-alert');
            existingAlerts.forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger custom-alert alert-dismissible fade show';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert after header
            const header = document.querySelector('.header-section');
            header.parentNode.insertBefore(alertDiv, header.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Focus on first field
        document.getElementById('username').focus();
        
        // Handle responsive adjustments
        function adjustLayout() {
            const isMobile = window.innerWidth < 768;
            const isLandscape = window.innerWidth > window.innerHeight;
            
            if (isMobile && isLandscape) {
                // Hide info card in landscape to save space
                const infoCard = document.querySelector('.info-card');
                if (infoCard) {
                    infoCard.style.display = 'none';
                }
            } else {
                // Show info card
                const infoCard = document.querySelector('.info-card');
                if (infoCard) {
                    infoCard.style.display = 'block';
                }
            }
        }
        
        // Initial adjustment
        adjustLayout();
        
        // Adjust on resize
        window.addEventListener('resize', function() {
            adjustLayout();
        });
    </script>
</body>
</html>