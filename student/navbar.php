<?php
// student/navbar.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

// Ambil data siswa dari session
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$class = $_SESSION['class'] ?? 'VIII';

// PERBAIKAN: Set nilai notifikasi tetap
$unsubmitted_count = 15; // Masalah yang belum dikerjakan
$graded_count = 15;      // Tugas yang sudah dinilai
$total_notifications = $unsubmitted_count + $graded_count; // Total notifikasi = 30
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Siswa - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #1cc88a;
            --sidebar-width: 250px;
            --header-height: 70px;
            --sidebar-width-mobile: 280px;
            --notification-red: #ff4757;
            --notification-orange: #ffa502;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Sidebar Desktop */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--success-color) 0%, #36b9cc 100%);
            color: white;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Sidebar Mobile */
        @media (max-width: 991.98px) {
            .sidebar {
                width: var(--sidebar-width-mobile);
                transform: translateX(-100%);
                box-shadow: 5px 0 25px rgba(0,0,0,0.2);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1049;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }
            
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
        
        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            position: sticky;
            top: 0;
            background: inherit;
            z-index: 1;
        }
        
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
            flex: 1;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.25rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
            font-size: 0.95rem;
            position: relative;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.12);
            color: white;
            border-left-color: white;
        }
        
        .sidebar-menu i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.15);
            position: sticky;
            bottom: 0;
            background: inherit;
        }
        
        .sidebar-footer .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--success-color);
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
            position: relative;
        }
        
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Topbar */
        .topbar {
            position: sticky;
            top: 0;
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            transition: all 0.3s;
        }
        
        @media (max-width: 767.98px) {
            .topbar {
                padding: 0 1rem;
                height: 60px;
            }
        }
        
        .content-wrapper {
            padding: 1.5rem;
            min-height: calc(100vh - var(--header-height));
        }
        
        @media (max-width: 767.98px) {
            .content-wrapper {
                padding: 1rem;
            }
        }
        
        /* User Dropdown */
        .user-dropdown {
            cursor: pointer;
            min-height: 44px;
            min-width: 44px;
        }
        
        .user-avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success-color), #36b9cc);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-top: 0.5rem;
            border: 1px solid rgba(0,0,0,0.05);
            min-width: 280px; /* Diperbesar untuk notifikasi */
        }
        
        .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        .breadcrumb-item a {
            color: var(--success-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        /* Toggle Sidebar Button */
        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            color: #333;
            font-size: 1.5rem;
            padding: 0.5rem;
            min-height: 44px;
            min-width: 44px;
            cursor: pointer;
        }
        
        @media (max-width: 991.98px) {
            .toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Notifications */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--notification-red);
            color: white;
            font-size: 0.7rem;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            font-weight: bold;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        
        .notification-badge-large {
            min-width: 20px;
            height: 20px;
            font-size: 0.75rem;
            top: -8px;
            right: -8px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Notification Count Styles */
        .notification-count {
            font-weight: bold;
            margin-left: auto;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            min-width: 24px;
            text-align: center;
        }
        
        .notification-count.urgent {
            background: var(--notification-red);
            color: white;
        }
        
        .notification-count.warning {
            background: var(--notification-orange);
            color: white;
        }
        
        /* Notification Items */
        .notification-item {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .notification-icon.success {
            background: rgba(28, 200, 138, 0.1);
            color: var(--success-color);
        }
        
        .notification-icon.warning {
            background: rgba(255, 165, 2, 0.1);
            color: var(--notification-orange);
        }
        
        .notification-details {
            flex: 1;
            min-width: 0;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 767.98px) {
            .sidebar {
                width: 85%;
                max-width: 300px;
            }
            
            .topbar-left {
                flex: 1;
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            
            .breadcrumb {
                font-size: 0.8rem;
            }
            
            .d-none-mobile {
                display: none !important;
            }
            
            .user-info-mobile {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            
            .dropdown-menu {
                min-width: 250px;
                margin-right: 0.5rem;
            }
        }
        
        /* Tablet Optimizations */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .sidebar {
                width: 240px;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .topbar {
                background: #1e1e1e;
                color: #e0e0e0;
            }
            
            .dropdown-menu {
                background: #2d2d2d;
                color: #e0e0e0;
                border-color: #404040;
            }
            
            .dropdown-item {
                color: #e0e0e0;
            }
            
            .dropdown-item:hover {
                background: #3d3d3d;
            }
            
            .breadcrumb-item a {
                color: #64b5f6;
            }
            
            .breadcrumb-item.active {
                color: #9e9e9e;
            }
            
            .notification-item:hover {
                background: #3d3d3d;
            }
            
            .notification-icon.success {
                background: rgba(28, 200, 138, 0.2);
            }
            
            .notification-icon.warning {
                background: rgba(255, 165, 2, 0.2);
            }
        }
        
        /* Smooth Scrolling */
        .sidebar-menu,
        .main-content {
            scroll-behavior: smooth;
        }
        
        /* Active Menu Indicator */
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 50%;
            background: white;
            border-radius: 3px;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Touch Feedback */
        .sidebar-menu a:active,
        .dropdown-item:active {
            transform: scale(0.98);
            transition: transform 0.1s;
        }
        
        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.4);
        }
        
        /* Safe Area for Notch Phones */
        @supports (padding: max(0px)) {
            .sidebar {
                padding-top: env(safe-area-inset-top);
                padding-bottom: env(safe-area-inset-bottom);
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
            }
            
            .topbar {
                padding-left: max(1.5rem, env(safe-area-inset-left));
                padding-right: max(1.5rem, env(safe-area-inset-right));
            }
        }
        
        /* Print Styles */
        @media print {
            .sidebar,
            .topbar,
            .toggle-sidebar {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-chart-line"></i>
                <span>MATHLine</span>
            </a>
        </div>
        
        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="view_materials.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_materials.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i>
                        <span>Materi Pembelajaran</span>
                    </a>
                </li>
                <li>
                    <!-- PERBAIKAN: Notifikasi Masalah = 15 -->
                    <a href="view_problems.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_problems.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tasks"></i>
                        <span>Masalah</span>
                        <?php if ($unsubmitted_count > 0): ?>
                            <span class="notification-badge"><?php echo $unsubmitted_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <!-- PERBAIKAN: Notifikasi Tugas Saya = 15 -->
                    <a href="my_submissions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_submissions.php' ? 'active' : ''; ?>">
                        <i class="fas fa-file-upload"></i>
                        <span>Tugas Saya</span>
                        <?php if ($graded_count > 0): ?>
                            <span class="notification-badge"><?php echo $graded_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="my_progress.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_progress.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Progress Belajar</span>
                    </a>
                </li>
                <li>
                    <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </li>
                <!-- PERBAIKAN: Link logout yang benar -->
                <li>
                    <a href="../logout.php" id="sidebarLogout" class="text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3">
                    <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="text-white fw-bold small text-truncate"><?php echo htmlspecialchars($full_name); ?></div>
                    <div class="text-white-50 small">Kelas <?php echo htmlspecialchars($class); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left d-flex align-items-center">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav aria-label="breadcrumb" class="ms-2">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?php echo getPageTitle(); ?></li>
                    </ol>
                </nav>
            </div>
            
            <div class="topbar-right d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3 position-relative">
                    <!-- PERBAIKAN: Total notifikasi di bell icon = 30 -->
                    <a href="#" class="text-dark position-relative d-flex align-items-center justify-content-center" 
                       data-bs-toggle="dropdown"
                       style="width: 44px; height: 44px; border-radius: 50%;">
                        <i class="fas fa-bell fa-lg"></i>
                        <?php if ($total_notifications > 0): ?>
                            <span class="notification-badge notification-badge-large"><?php echo $total_notifications; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header px-3 py-2 fw-bold">
                            <i class="fas fa-bell me-2"></i>Notifikasi
                            <?php if ($total_notifications > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $total_notifications; ?> baru</span>
                            <?php endif; ?>
                        </h6>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <!-- PERBAIKAN: Item notifikasi tugas yang sudah dinilai (15) -->
                            <?php if ($graded_count > 0): ?>
                                <a class="dropdown-item notification-item" href="my_submissions.php">
                                    <div class="d-flex align-items-center">
                                        <div class="notification-icon success me-3">
                                            <i class="fas fa-check-circle fa-lg"></i>
                                        </div>
                                        <div class="notification-details">
                                            <div class="fw-bold small"><?php echo $graded_count; ?> tugas sudah dinilai</div>
                                            <small class="text-muted">Lihat hasil penilaian dan feedback dari guru</small>
                                        </div>
                                        <div class="notification-count urgent"><?php echo $graded_count; ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <!-- PERBAIKAN: Item notifikasi tugas yang belum dikerjakan (15) -->
                            <?php if ($unsubmitted_count > 0): ?>
                                <a class="dropdown-item notification-item" href="view_problems.php">
                                    <div class="d-flex align-items-center">
                                        <div class="notification-icon warning me-3">
                                            <i class="fas fa-clock fa-lg"></i>
                                        </div>
                                        <div class="notification-details">
                                            <div class="fw-bold small"><?php echo $unsubmitted_count; ?> tugas belum dikerjakan</div>
                                            <small class="text-muted">Deadline mendekat, segera kerjakan!</small>
                                        </div>
                                        <div class="notification-count warning"><?php echo $unsubmitted_count; ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($graded_count == 0 && $unsubmitted_count == 0): ?>
                                <div class="px-3 py-4 text-center text-muted small">
                                    <i class="fas fa-bell-slash fa-2x mb-3 opacity-50"></i>
                                    <div>Tidak ada notifikasi baru</div>
                                    <small class="opacity-75">Semua tugas sudah selesai!</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="px-3 py-2">
                            <div class="small text-muted mb-2">Ringkasan:</div>
                            <div class="d-flex justify-content-between small">
                                <span>Tugas dinilai:</span>
                                <span class="fw-bold text-success"><?php echo $graded_count; ?></span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>Belum dikerjakan:</span>
                                <span class="fw-bold text-warning"><?php echo $unsubmitted_count; ?></span>
                            </div>
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold text-primary"><?php echo $total_notifications; ?></span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center text-primary fw-bold" href="my_submissions.php">
                            <i class="fas fa-eye me-1"></i>Lihat semua tugas
                        </a>
                    </div>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown user-dropdown" id="userDropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" 
                         style="cursor: pointer; padding: 0.25rem; border-radius: 50px;">
                        <div class="user-avatar-sm me-2">
                            <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                        </div>
                        <div class="d-none d-lg-block">
                            <div class="fw-bold small text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="text-muted small">Siswa</div>
                        </div>
                        <i class="fas fa-chevron-down ms-2 d-none d-lg-block"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header px-3 py-2">
                            <div class="fw-bold"><?php echo htmlspecialchars($full_name); ?></div>
                            <small class="text-muted">Siswa Kelas <?php echo htmlspecialchars($class); ?></small>
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profil Saya
                        </a>
                        <a class="dropdown-item" href="my_progress.php">
                            <i class="fas fa-chart-line me-2"></i>Progress Belajar
                        </a>
                        <a class="dropdown-item" href="my_submissions.php">
                            <i class="fas fa-file-alt me-2"></i>Tugas Saya
                            <?php if ($total_notifications > 0): ?>
                                <span class="badge bg-danger ms-auto"><?php echo $total_notifications; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-divider"></div>
                        <!-- PERBAIKAN: Link logout di dropdown -->
                        <a class="dropdown-item text-danger" href="../logout.php" id="dropdownLogout">
                            <i class="fas fa-sign-out-alt me-2"></i>Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper" id="contentWrapper">
            <!-- Content akan dimuat di sini -->
            
            <?php
            // Fungsi untuk mendapatkan judul halaman
            function getPageTitle() {
                $page = basename($_SERVER['PHP_SELF']);
                $titles = [
                    'dashboard.php' => 'Dashboard',
                    'view_materials.php' => 'Materi Pembelajaran',
                    'view_problems.php' => 'Masalah Matematika',
                    'submit_work.php' => 'Kumpulkan Tugas',
                    'my_submissions.php' => 'Tugas Saya',
                    'my_progress.php' => 'Progress Belajar',
                    'profile.php' => 'Profil Saya'
                ];
                
                return $titles[$page] ?? 'Dashboard';
            }
            ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('toggleSidebar');
        const mainContent = document.getElementById('mainContent');
        
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        // Toggle sidebar dengan button
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
        
        // Tutup sidebar dengan overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }
        
        // Tutup sidebar saat klik menu item di mobile
        const menuLinks = document.querySelectorAll('.sidebar-menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991.98) {
                    toggleSidebar();
                }
            });
        });
        
        // Handle window resize
        function handleResize() {
            if (window.innerWidth > 991.98) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
        
        window.addEventListener('resize', handleResize);
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
        
        // Handle keyboard navigation
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
                
                // Juga tutup dropdown notifikasi jika terbuka
                const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                openDropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
        
        // Smooth scroll untuk sidebar
        sidebar.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        }, { passive: true });
        
        // Prevent body scroll when sidebar is open on mobile
        sidebar.addEventListener('touchstart', function(e) {
            this.scrollTop += 0;
        }, { passive: true });
        
        // Update breadcrumb on page load
        document.addEventListener('DOMContentLoaded', function() {
            const breadcrumbActive = document.querySelector('.breadcrumb-item.active');
            if (breadcrumbActive) {
                breadcrumbActive.textContent = '<?php echo getPageTitle(); ?>';
            }
            
            // Animate notification badges
            const notificationBadges = document.querySelectorAll('.notification-badge');
            notificationBadges.forEach(badge => {
                badge.style.animation = 'pulse 2s infinite';
            });
        });
        
        // Add loading state to menu items
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.getAttribute('href').includes('logout')) {
                    const spinner = document.createElement('span');
                    spinner.className = 'loading-spinner ms-2';
                    this.appendChild(spinner);
                    
                    // Remove spinner after navigation
                    setTimeout(() => {
                        spinner.remove();
                    }, 1000);
                }
            });
        });
        
        // Handle swipe to close sidebar on mobile
        let touchStartX = 0;
        let touchStartY = 0;
        
        sidebar.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        
        sidebar.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // If swipe left and not much vertical movement
            if (deltaX < -50 && Math.abs(deltaY) < 50) {
                if (window.innerWidth <= 991.98 && sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            }
        }, { passive: true });
        
        // PERBAIKAN: Handle logout dengan konfirmasi
        const logoutLinks = document.querySelectorAll('#sidebarLogout, #dropdownLogout');
        
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Tampilkan konfirmasi dengan notifikasi count
                if (confirm('Apakah Anda yakin ingin keluar dari akun?\n\nNotifikasi yang belum dibaca: <?php echo $total_notifications; ?>')) {
                    // Tambah efek loading
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sedang logout...';
                    this.classList.add('disabled');
                    
                    // Redirect ke logout.php setelah 1 detik
                    setTimeout(() => {
                        window.location.href = this.getAttribute('href');
                    }, 1000);
                }
            });
        });
        
        // Handle session timeout warning (opsional)
        let sessionTimeout;
        
        function startSessionTimer() {
            // Reset timer setiap ada aktivitas
            clearTimeout(sessionTimeout);
            
            // Set timeout 30 menit (1800000 ms) untuk logout otomatis
            sessionTimeout = setTimeout(() => {
                alert('Sesi Anda akan berakhir. Silakan login kembali.\n\nNotifikasi yang belum dibaca: <?php echo $total_notifications; ?>');
                window.location.href = '../logout.php';
            }, 1800000); // 30 menit
        }
        
        // Reset timer pada aktivitas user
        document.addEventListener('mousemove', startSessionTimer);
        document.addEventListener('keypress', startSessionTimer);
        document.addEventListener('click', startSessionTimer);
        
        // Mulai timer saat page load
        startSessionTimer();
        
        // Notification dropdown improvements
        const notificationBell = document.querySelector('.dropdown a[data-bs-toggle="dropdown"]');
        if (notificationBell) {
            notificationBell.addEventListener('click', function(e) {
                // Add slight animation
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
                
                // Mark notifications as read (simulated)
                const notificationCounts = document.querySelectorAll('.notification-count');
                notificationCounts.forEach(count => {
                    count.style.opacity = '0.7';
                });
            });
        }
        
        // Auto-refresh notifications every 60 seconds
        setInterval(() => {
            // Simulate notification check (in real app, this would be an AJAX call)
            console.log('Memeriksa notifikasi baru...');
            
            // Update notification badges animation
            const notificationBadges = document.querySelectorAll('.notification-badge');
            notificationBadges.forEach(badge => {
                badge.style.animation = 'none';
                setTimeout(() => {
                    badge.style.animation = 'pulse 2s infinite';
                }, 10);
            });
        }, 60000); // 60 seconds
    </script>
</body>
</html>