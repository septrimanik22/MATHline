<?php
// admin/dashboard.php

// Hapus semua output buffer yang ada
while (ob_get_level()) {
    ob_end_clean();
}

// Mulai output buffering
ob_start();

session_start();
require_once '../includes/db_connection.php';

// Cek apakah user sudah login dan role admin/guru
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Hapus semua output buffer sebelum redirect
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ../login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'guru') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: ../student/dashboard.php');
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Flush output buffer sebelum mengirim HTML
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Dashboard - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #10b981;
            --light-green: #d1fae5;
            --medium-green: #34d399;
            --dark-green: #059669;
            --soft-green: #ecfdf5;
            --accent-green: #065f46;
            --soft-gray: #f8fafc;
            --card-blue: #3b82f6;
            --card-purple: #8b5cf6;
            --card-orange: #f59e0b;
            --card-red: #ef4444;
            --card-pink: #ec4899;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--soft-gray);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #374151;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            padding: 0.25rem 0;
        }
        
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
        }
        
        /* Main Container - PERBAIKAN UTAMA */
        .main-container {
            padding: clamp(0.75rem, 2vw, 1.5rem);
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            border-radius: 16px;
            color: white;
            overflow: hidden;
            position: relative;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        
        .welcome-banner h3 {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .welcome-banner p {
            font-size: clamp(0.85rem, 1.8vw, 1rem);
            opacity: 0.9;
            line-height: 1.4;
        }
        
        /* Action Cards Grid - Responsif */
        .action-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr));
            gap: clamp(0.75rem, 1.5vw, 1.25rem);
            margin-top: clamp(1rem, 2vw, 1.5rem);
            width: 100%;
        }
        
        .action-card {
            background: white;
            border-radius: 16px;
            padding: clamp(1.25rem, 2.5vw, 1.75rem);
            transition: all 0.3s ease;
            text-decoration: none;
            color: #374151;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: auto;
            min-height: clamp(140px, 20vh, 180px);
            border: 2px solid #e5e7eb;
            position: relative;
            overflow: hidden;
            text-align: center;
            width: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .action-icon {
            width: clamp(50px, 10vw, 65px);
            height: clamp(50px, 10vw, 65px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: clamp(0.5rem, 1.2vw, 0.875rem);
            font-size: clamp(1.2rem, 2.5vw, 1.5rem);
            color: white;
        }
        
        .action-icon.material-icon {
            background: linear-gradient(135deg, var(--card-blue), #1d4ed8);
        }
        
        .action-icon.student-icon {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        }
        
        .action-icon.progress-icon {
            background: linear-gradient(135deg, var(--card-red), #dc2626);
        }
        
        .action-title {
            font-size: clamp(1rem, 2vw, 1.2rem);
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            line-height: 1.3;
        }
        
        .action-card small {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
            margin-top: 0.25rem;
        }
        
        /* Date Box */
        .date-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: clamp(0.5rem, 1.5vw, 0.875rem) clamp(0.75rem, 2vw, 1.25rem);
            text-align: center;
            min-width: 0;
            margin: 0 auto;
        }
        
        .date-box .date-label {
            font-size: clamp(0.7rem, 1.5vw, 0.8rem);
            opacity: 0.8;
            margin-bottom: 0.125rem;
        }
        
        .date-box .date-value {
            font-size: clamp(0.85rem, 1.8vw, 1rem);
            font-weight: 600;
            line-height: 1.3;
        }
        
        /* Navigation improvements */
        .nav-link {
            padding: 0.5rem 0.875rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
            margin: 0.125rem 0;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
        }
        
        .navbar-collapse {
            padding: 0.5rem 0;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            margin-top: auto;
            border-top: 1px solid #e5e7eb;
            padding: clamp(1rem, 1.5vw, 1.25rem) 0 !important;
        }
        
        footer p {
            font-size: clamp(0.75rem, 1.5vw, 0.9rem);
            margin: 0;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        
        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .main-container {
                padding: clamp(0.5rem, 1.5vw, 1rem);
            }
            
            .action-cards-grid {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
            
            .welcome-banner {
                border-radius: 14px;
                padding: 1.25rem !important;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 0.375rem 0.75rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar-brand div:first-child {
                padding: 0.375rem;
            }
            
            .navbar-toggler {
                padding: 0.375rem 0.5rem;
            }
            
            .main-container {
                padding: 0.75rem;
            }
            
            .welcome-banner {
                border-radius: 12px;
                padding: 1rem !important;
                margin-bottom: 1rem;
            }
            
            .welcome-banner h3 {
                font-size: 1.2rem;
            }
            
            .action-cards-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .action-card {
                padding: 1.25rem;
                min-height: 140px;
                border-radius: 14px;
            }
            
            .action-icon {
                width: 55px;
                height: 55px;
                font-size: 1.4rem;
            }
            
            .action-title {
                font-size: 1.1rem;
            }
            
            .navbar-collapse {
                background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
                border-radius: 0 0 8px 8px;
                padding: 0.75rem;
                margin-top: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                font-size: 14px;
            }
            
            .main-container {
                padding: 0.5rem;
            }
            
            .welcome-banner {
                padding: 0.875rem !important;
                border-radius: 10px;
            }
            
            .welcome-banner h3 {
                font-size: 1.1rem;
                text-align: center;
            }
            
            .welcome-banner p {
                font-size: 0.85rem;
                text-align: center;
            }
            
            .action-card {
                padding: 1.125rem;
                min-height: 130px;
                border-radius: 12px;
            }
            
            .action-icon {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
                border-radius: 10px;
            }
            
            .action-title {
                font-size: 1rem;
            }
            
            .date-box {
                padding: 0.5rem 0.75rem;
                border-radius: 10px;
            }
            
            .date-box .date-label {
                font-size: 0.7rem;
            }
            
            .date-box .date-value {
                font-size: 0.85rem;
            }
            
            footer {
                padding: 0.875rem 0 !important;
            }
        }
        
        @media (max-width: 375px) {
            .main-container {
                padding: 0.375rem;
            }
            
            .welcome-banner {
                padding: 0.75rem !important;
            }
            
            .welcome-banner h3 {
                font-size: 1rem;
            }
            
            .action-card {
                padding: 1rem;
                min-height: 120px;
            }
            
            .action-icon {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }
            
            .action-title {
                font-size: 0.95rem;
            }
            
            .date-box {
                padding: 0.375rem 0.625rem;
            }
            
            .date-box .date-value {
                font-size: 0.8rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .action-card:hover {
                transform: scale(1.02);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            }
            
            .nav-link {
                padding: 0.625rem 0.875rem !important;
            }
            
            .navbar-toggler {
                padding: 0.5rem 0.75rem;
            }
            
            /* Larger touch targets */
            .action-card {
                min-height: 140px;
            }
            
            .action-icon {
                min-width: 56px;
                min-height: 56px;
            }
        }
        
        /* Landscape optimizations */
        @media (max-height: 500px) and (orientation: landscape) {
            .action-cards-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .action-card {
                min-height: 120px;
                padding: 1rem;
            }
            
            .welcome-banner {
                padding: 0.75rem !important;
                margin-bottom: 0.75rem;
            }
        }
        
        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--primary-green);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Utility Classes */
        .text-responsive {
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }
        
        .padding-responsive {
            padding: clamp(0.5rem, 1.5vw, 1rem);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <div class="bg-white rounded-circle p-2 me-2">
                    <i class="fas fa-chalkboard-teacher text-success" style="font-size: clamp(1rem, 2vw, 1.25rem);"></i>
                </div>
                <div>
                    <div class="fs-6">MATHLine</div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active d-flex align-items-center" href="dashboard.php">
                            <i class="fas fa-home me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-danger d-flex align-items-center" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            <span>Keluar</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container mt-2 mt-lg-3">
        
        <!-- Welcome Banner -->
        <div class="row mb-2 mb-lg-3 fade-in">
            <div class="col-12">
                <div class="welcome-banner position-relative">
                    <div class="row align-items-center">
                        <div class="col-lg-8 col-md-7 col-12 mb-3 mb-md-0">
                            <h3 class="fw-bold mb-2 text-center text-lg-start">
                                Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋
                            </h3>
                        </div>
                        <div class="col-lg-4 col-md-5 col-12">
                            <div class="date-box">
                                <div class="date-label">Hari ini</div>
                                <div class="date-value">
                                    <?php 
                                    date_default_timezone_set('Asia/Jakarta');
                                    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    $today = getdate();
                                    echo $hari[$today['wday']] . ', ' . $today['mday'] . ' ' . $bulan[$today['mon']-1] . ' ' . $today['year'];
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 3 Action Cards (hanya Materi, Siswa, Progress) -->
        <div class="action-cards-grid">
            <!-- Card 1: Materi -->
            <a href="materials.php" class="action-card material-card fade-in delay-1">
                <div class="action-icon material-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="action-title">Materi</div>
                <small class="text-muted mt-1 d-none d-md-block">Kelola materi pembelajaran</small>
            </a>
            
            <!-- Card 2: Siswa -->
            <a href="students.php" class="action-card student-card fade-in delay-2">
                <div class="action-icon student-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-title">Siswa</div>
                <small class="text-muted mt-1 d-none d-md-block">Kelola data siswa</small>
            </a>
            
            <!-- Card 3: Progress -->
            <a href="progress_tracking.php" class="action-card progress-card fade-in delay-3">
                <div class="action-icon progress-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-title">Progress</div>
                <small class="text-muted mt-1 d-none d-md-block">Pantau perkembangan belajar</small>
            </a>
        </div>
        
    </div>
    
    <!-- Footer -->
    <footer class="mt-3 mt-lg-4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-dark mb-0">
                        &copy; <?php echo date('Y'); ?> MATHLine | Pendidikan Matematika | Universitas Negeri Medan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle responsive behavior
            function handleResponsive() {
                const cards = document.querySelectorAll('.action-card');
                const isMobile = window.innerWidth <= 768;
                
                cards.forEach(card => {
                    if (isMobile) {
                        card.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
                    } else {
                        card.style.transition = 'all 0.3s ease';
                    }
                });
            }
            
            // Initialize
            handleResponsive();
            
            // Add animation classes
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(el => {
                el.style.animationPlayState = 'running';
            });
            
            // Handle card clicks
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (this.classList.contains('loading')) {
                        e.preventDefault();
                    }
                });
            });
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const fadeElements = document.querySelectorAll('.fade-in');
                fadeElements.forEach(el => {
                    el.style.animation = 'none';
                    setTimeout(() => {
                        el.style.animation = '';
                    }, 10);
                });
            }, 250);
        });
    </script>
</body>
</html>