<?php
// student/profile.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Get student data
$stmt = $conn->prepare("SELECT u.*, s.class, s.year, s.status 
                       FROM users u 
                       JOIN students s ON u.id = s.user_id 
                       WHERE u.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: dashboard.php');
    exit();
}

$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Profil Saya - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --medium-blue: #60a5fa;
            --dark-blue: #1d4ed8;
            --soft-blue: #eff6ff;
            --accent-blue: #1e40af;
            --soft-gray: #f8fafc;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--soft-gray);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            color: #374151;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        
        /* Main container */
        .main-container {
            padding: clamp(1rem, 3vw, 2rem);
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }
        
        /* Cards */
        .card {
            border-radius: clamp(10px, 2vw, 16px);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: clamp(1rem, 2vw, 1.25rem);
        }
        
        .card-body {
            padding: clamp(1rem, 2vw, 1.5rem);
        }
        
        /* User Avatar */
        .user-avatar {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            width: clamp(80px, 20vw, 100px);
            height: clamp(80px, 20vw, 100px);
            font-size: clamp(2rem, 5vw, 2.5rem);
            margin: 0 auto;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .page-header h4 {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            font-size: clamp(0.9rem, 2vw, 1rem);
            color: #6b7280;
            margin-bottom: 0;
        }
        
        /* Back button */
        .btn-outline-secondary {
            border-radius: 8px;
            padding: clamp(0.5rem, 1.5vw, 0.625rem) clamp(1rem, 2vw, 1.25rem);
            font-size: clamp(0.85rem, 1.8vw, 0.9rem);
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Form controls */
        .form-label {
            font-size: clamp(0.85rem, 1.8vw, 0.9rem);
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: clamp(0.625rem, 1.5vw, 0.75rem) clamp(0.75rem, 2vw, 1rem);
            font-size: clamp(0.9rem, 2vw, 1rem);
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #6b7280;
        }
        
        .form-control:read-only {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }
        
        .form-text {
            font-size: clamp(0.75rem, 1.6vw, 0.8rem);
            color: #9ca3af;
            margin-top: 0.375rem;
        }
        
        /* Badge */
        .badge {
            font-size: clamp(0.75rem, 1.6vw, 0.8rem);
            padding: clamp(0.25rem, 0.8vw, 0.375rem) clamp(0.75rem, 1.5vw, 0.875rem);
            border-radius: 12px;
            font-weight: 500;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--light-blue), var(--medium-blue));
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
            padding: clamp(1rem, 2vw, 1.5rem) 0;
        }
        
        footer p {
            font-size: clamp(0.8rem, 1.8vw, 0.9rem);
            color: #1e293b;
            margin: 0;
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .main-container {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 992px) {
            .main-container {
                padding: 1.25rem;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .page-header {
                margin-bottom: 1.5rem;
            }
            
            .page-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .btn-outline-secondary {
                align-self: stretch;
                text-align: center;
                width: 100%;
            }
            
            .user-avatar {
                width: 90px;
                height: 90px;
                font-size: 2.25rem;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .form-control {
                padding: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .main-container {
                padding: 0.875rem;
            }
            
            .page-header h4 {
                font-size: 1.1rem;
            }
            
            .page-header p {
                font-size: 0.85rem;
            }
            
            .user-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .card {
                border-radius: 10px;
            }
            
            .row.g-3 {
                --bs-gutter-y: 1rem;
            }
            
            .form-label {
                font-size: 0.85rem;
            }
            
            .form-control {
                font-size: 0.9rem;
                padding: 0.675rem 0.75rem;
            }
            
            footer {
                padding: 1rem 0;
            }
            
            footer p {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 375px) {
            .main-container {
                padding: 0.75rem;
            }
            
            .page-header h4 {
                font-size: 1rem;
            }
            
            .user-avatar {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
            
            .btn-outline-secondary {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }
            
            .btn-outline-secondary {
                min-height: 44px;
            }
            
            .form-control {
                min-height: 44px;
            }
        }
        
        /* Landscape optimizations */
        @media (max-height: 500px) and (orientation: landscape) {
            .page-header {
                margin-bottom: 1rem;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            footer {
                padding: 0.75rem 0;
            }
        }
        
        /* Print styles */
        @media print {
            footer, .btn-outline-secondary {
                display: none !important;
            }
            
            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
            }
            
            body {
                background: white !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="main-container">
        
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="user-avatar mb-3">
                            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                        </div>
                        
                        <h4 class="fw-bold mb-2" style="font-size: clamp(1.1rem, 2.5vw, 1.5rem);">
                            <?php echo htmlspecialchars($student['full_name']); ?>
                        </h4>
                        <p class="text-muted mb-3">
                            <i class="fas fa-graduation-cap me-1"></i>
                            Kelas <?php echo htmlspecialchars($student['class']); ?>
                        </p>
                        
                        <div class="border-top pt-3">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-muted small">Username</div>
                                    <div class="fw-bold" style="font-size: clamp(0.9rem, 2vw, 1rem);">
                                        @<?php echo htmlspecialchars($student['username']); ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Status</div>
                                    <div>
                                        <?php if ($student['status'] == 'active'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8 col-md-6 col-12">
                <!-- Profile Update Form -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0" style="font-size: clamp(1rem, 2.2vw, 1.25rem);">
                            <i class="fas fa-user-edit me-2 text-primary"></i>
                            Informasi Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label for="full_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="full_name" 
                                       value="<?php echo htmlspecialchars($student['full_name']); ?>" 
                                       readonly>
                                <div class="form-text">Nama tidak dapat diubah</div>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <label for="nim_nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nim_nis" 
                                       value="<?php echo htmlspecialchars($student['nim_nis'] ?? 'Belum diatur'); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <label for="class" class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="class" 
                                       value="Kelas <?php echo htmlspecialchars($student['class']); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <label for="year" class="form-label">Tahun Ajaran</label>
                                <input type="text" class="form-control" id="year" 
                                       value="<?php echo htmlspecialchars($student['year'] ?? date('Y')); ?>" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-4 mt-lg-5">
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
        // Handle responsive behavior
        function handleResponsive() {
            const isMobile = window.innerWidth <= 768;
            const cards = document.querySelectorAll('.card');
            const backButton = document.querySelector('.btn-outline-secondary');
            
            cards.forEach(card => {
                if (isMobile) {
                    card.style.transition = 'box-shadow 0.2s ease';
                } else {
                    card.style.transition = 'all 0.3s ease';
                }
            });
            
            // Adjust back button width on mobile
            if (backButton) {
                if (isMobile) {
                    backButton.classList.add('w-100');
                    backButton.classList.remove('w-md-auto');
                } else {
                    backButton.classList.remove('w-100');
                    backButton.classList.add('w-md-auto');
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            handleResponsive();
            
            // Add animation for page load
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Add click effect to back button
            const backButton = document.querySelector('.btn-outline-secondary');
            if (backButton) {
                backButton.addEventListener('click', function(e) {
                    // Add loading state
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memuat...';
                    this.classList.add('disabled');
                    
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('disabled');
                    }, 1000);
                });
            }
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResponsive, 250);
        });
        
        // Print functionality (optional)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                alert('Untuk mencetak profil, gunakan menu print browser.');
            }
        });
        
        // Add confirmation for leaving page (optional)
        window.addEventListener('beforeunload', function(e) {
            // Optional: Add confirmation if user has unsaved changes
            return null;
        });
    </script>
</body>
</html>