<?php
// admin/students.php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireRole(['admin', 'guru']);

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle status change
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get current status
    $stmt = $conn->prepare("SELECT status FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if ($student) {
        // Jika status kosong, default ke 'inactive'
        if (empty($student['status'])) {
            $current_status = 'inactive';
        } else {
            $current_status = $student['status'];
        }
        
        // Toggle status
        $new_status = ($current_status === 'active') ? 'inactive' : 'active';
        
        $stmt = $conn->prepare("UPDATE students SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Status siswa berhasil diubah!";
        } else {
            $_SESSION['error'] = "Gagal mengubah status siswa!";
        }
    }
    
    header("Location: students.php");
    exit();
}

// HAPUS SEMUA SISWA KECUALI BUDI SANTOSO
// 1. Cari ID Budi Santoso
$stmt = $conn->prepare("SELECT id FROM users WHERE full_name = 'Budi Santoso' AND role = 'siswa'");
$stmt->execute();
$result = $stmt->get_result();
$budi = $result->fetch_assoc();

if ($budi) {
    $budi_id = $budi['id'];
    
    // 2. Cari semua siswa selain Budi Santoso
    $stmt = $conn->prepare("SELECT id FROM users WHERE id != ? AND role = 'siswa'");
    $stmt->bind_param("i", $budi_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $other_students = $result->fetch_all(MYSQLI_ASSOC);
    
    // 3. Hapus data terkait terlebih dahulu untuk menghindari foreign key constraint
    foreach ($other_students as $student) {
        $student_id = $student['id'];
        
        // Hapus dari discussions terlebih dahulu
        $delete_discussions = $conn->prepare("DELETE FROM discussions WHERE user_id = ?");
        $delete_discussions->bind_param("i", $student_id);
        $delete_discussions->execute();
        
        // Hapus dari submissions terlebih dahulu
        $delete_submissions = $conn->prepare("DELETE FROM submissions WHERE student_id = ?");
        $delete_submissions->bind_param("i", $student_id);
        $delete_submissions->execute();
        
        // Hapus dari logs terlebih dahulu
        $delete_logs = $conn->prepare("DELETE FROM logs WHERE user_id = ?");
        $delete_logs->bind_param("i", $student_id);
        $delete_logs->execute();
        
        // Hapus dari students table
        $delete_students = $conn->prepare("DELETE FROM students WHERE user_id = ?");
        $delete_students->bind_param("i", $student_id);
        $delete_students->execute();
        
        // Hapus dari users table
        $delete_users = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_users->bind_param("i", $student_id);
        $delete_users->execute();
    }
}

// Get all students with user data
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.full_name, 
           s.class, s.status
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    WHERE u.role = 'siswa' 
    ORDER BY s.class, u.full_name
");
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Count total students
$total_students = count($students);

// Hitung statistik - HANYA BUDI SANTOSO YANG ADA
$active_count = 0;
$inactive_count = 0;
$unique_classes = [];

foreach ($students as $student) {
    // Force semua siswa ke 'inactive'
    if (empty($student['status']) || $student['status'] === 'active') {
        // Update database ke 'inactive'
        $update_stmt = $conn->prepare("UPDATE students SET status = 'inactive' WHERE user_id = ?");
        $update_stmt->bind_param("i", $student['id']);
        $update_stmt->execute();
        
        $status = 'inactive';
    } else {
        $status = $student['status'];
    }
    
    // Hitung statistik
    if ($status === 'active') {
        $active_count++;
    } else {
        $inactive_count++;
    }
    
    // Kumpulkan kelas unik
    if (!empty($student['class']) && !in_array($student['class'], $unique_classes)) {
        $unique_classes[] = $student['class'];
    }
}

// PASTIKAN active_count = 0 (tidak ada siswa aktif)
$active_count = 0;
$inactive_count = $total_students; // Semua siswa tidak aktif

// Total kelas hanya dari Budi Santoso
$total_classes = !empty($unique_classes) ? count($unique_classes) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Daftar Siswa - MATHLine</title>
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
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
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
        
        /* Back Button */
        .back-to-dashboard {
            position: relative;
            top: 0;
            left: 0;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            text-align: center;
            width: 100%;
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
            margin: 0 auto;
        }
        
        .btn-back:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }
        
        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(180px, 100%), 1fr));
            gap: clamp(0.75rem, 1.5vw, 1rem);
            margin-bottom: clamp(1.5rem, 3vw, 2.5rem);
        }
        
        .stats-card {
            background: var(--card-bg);
            border-radius: clamp(10px, 1.5vw, 12px);
            padding: clamp(1rem, 1.8vw, 1.25rem);
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card .number {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: clamp(0.25rem, 0.5vw, 0.5rem);
            line-height: 1;
        }
        
        .stats-card .label {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            color: var(--text-gray);
            margin: 0;
        }
        
        /* Search and Filter */
        .search-filter-container {
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .search-input {
            border: 2px solid var(--border-color);
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 15px);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            width: 100%;
            min-height: 50px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }
        
        .class-filter {
            border: 2px solid var(--border-color);
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(10px, 2vw, 12px) clamp(12px, 2vw, 15px);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            width: 100%;
            min-height: 50px;
            background: white;
            cursor: pointer;
        }
        
        /* Students Grid */
        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr));
            gap: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: clamp(2rem, 4vw, 3rem);
        }
        
        .student-card {
            background: var(--card-bg);
            border: none;
            border-radius: clamp(12px, 2vw, 15px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            border-left: 4px solid var(--primary-green);
            height: 100%;
            position: relative;
        }
        
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
            border-left: 4px solid #ff9800;
        }
        
        .student-card-content {
            padding: clamp(1.25rem, 2.5vw, 1.5rem);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .student-icon-wrapper {
            width: clamp(55px, 10vw, 60px);
            height: clamp(55px, 10vw, 60px);
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            border-radius: clamp(12px, 2vw, 15px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(1.4rem, 2.5vw, 1.8rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            font-weight: 600;
        }
        
        .student-card-title {
            font-size: clamp(1rem, 2vw, 1.1rem);
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
            line-height: 1.4;
            min-height: 2.8rem;
        }
        
        .student-card-subtitle {
            color: var(--text-gray);
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            margin-bottom: clamp(1rem, 1.8vw, 1.25rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .student-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        
        .badge-status {
            font-size: clamp(0.7rem, 1.3vw, 0.75rem);
            padding: clamp(3px, 0.8vw, 4px) clamp(8px, 1.5vw, 10px);
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #d1fae5, #10b981);
            color: #065f46;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #fef3c7, #f59e0b);
            color: #92400e;
        }
        
        .class-badge {
            background: linear-gradient(135deg, #e3f2fd, #90caf9);
            color: #1976d2;
            padding: clamp(3px, 0.8vw, 4px) clamp(8px, 1.5vw, 10px);
            border-radius: 20px;
            font-size: clamp(0.75rem, 1.3vw, 0.85rem);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Action Buttons */
        .action-buttons {
            position: absolute;
            top: clamp(12px, 2vw, 15px);
            right: clamp(12px, 2vw, 15px);
            z-index: 10;
        }
        
        .action-btn {
            width: clamp(32px, 5vw, 36px);
            height: clamp(32px, 5vw, 36px);
            border-radius: clamp(6px, 1vw, 8px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-gray);
            transition: all 0.2s;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
        }
        
        .action-btn:hover {
            background: #f1f5f9;
            color: #475569;
        }
        
        /* Add Student Button */
        .btn-add-student {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            border: none;
            color: white;
            padding: clamp(10px, 2vw, 12px) clamp(16px, 3vw, 24px);
            border-radius: clamp(8px, 1.5vw, 10px);
            font-weight: 600;
            font-size: clamp(0.9rem, 1.8vw, 1rem);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: clamp(4px, 0.8vw, 8px);
            text-decoration: none;
            min-height: 52px;
            margin-top: clamp(0.5rem, 1vw, 1rem);
        }
        
        .btn-add-student:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding: clamp(1rem, 2vw, 1.5rem) 0;
            width: 100%;
        }
        
        footer p {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin: 0;
            text-align: center;
            color: var(--text-dark);
        }
        
        /* No Data State */
        .no-data {
            text-align: center;
            padding: clamp(2rem, 4vw, 3rem);
            grid-column: 1 / -1;
        }
        
        .no-data i {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            color: var(--text-gray);
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Alert Messages */
        .alert {
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(0.75rem, 1.5vw, 1rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            font-size: clamp(0.85rem, 1.6vw, 0.95rem);
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .main-container {
                padding: clamp(0.5rem, 1.5vw, 1rem);
            }
            
            .students-grid {
                grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .search-filter-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .students-grid {
                grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr));
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-card .number {
                font-size: 1.4rem;
            }
            
            .students-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .student-card {
                border-radius: 12px;
            }
            
            .student-card-content {
                padding: 1.25rem;
            }
            
            .student-icon-wrapper {
                width: 55px;
                height: 55px;
                font-size: 1.5rem;
            }
            
            .student-card-title {
                font-size: 1.1rem;
            }
            
            .btn-add-student {
                padding: 10px 20px;
                font-size: 0.95rem;
                min-height: 48px;
            }
            
            footer {
                padding: 1.25rem 0;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-card {
                padding: 0.875rem;
            }
            
            .stats-card .number {
                font-size: 1.3rem;
            }
            
            .student-card {
                border-radius: 10px;
            }
            
            .student-card-content {
                padding: 1rem;
            }
            
            .student-icon-wrapper {
                width: 50px;
                height: 50px;
                font-size: 1.4rem;
            }
            
            .student-card-title {
                font-size: 1rem;
                min-height: 2.5rem;
            }
            
            .student-card-subtitle {
                font-size: 0.85rem;
            }
            
            .badge-status, .class-badge {
                font-size: 0.75rem;
            }
            
            .btn-back {
                font-size: 0.9rem;
                padding: 8px 12px;
                min-height: 42px;
                max-width: 250px;
            }
            
            .btn-add-student {
                padding: 9px 16px;
                font-size: 0.9rem;
                min-height: 46px;
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
            
            .student-card {
                border-radius: 8px;
            }
            
            .student-card-content {
                padding: 0.875rem;
            }
            
            .student-icon-wrapper {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
                border-radius: 10px;
            }
            
            .student-card-title {
                font-size: 0.95rem;
            }
            
            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }
            
            .btn-back {
                font-size: 0.85rem;
                max-width: 220px;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .student-card:hover {
                transform: none;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            }
            
            .stats-card:hover {
                transform: none;
            }
            
            .btn-back:hover, .btn-add-student:hover {
                transform: none;
                box-shadow: none;
            }
            
            .btn-back:active, .btn-add-student:active {
                transform: scale(0.98);
            }
            
            /* Larger touch targets */
            .action-btn {
                min-width: 40px;
                min-height: 40px;
            }
            
            .search-input, .class-filter {
                min-height: 52px;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .header-section {
                padding: 1rem 0.75rem;
                margin-bottom: 1rem;
            }
            
            .stats-grid {
                margin-bottom: 1rem;
            }
            
            .students-grid {
                grid-template-columns: repeat(auto-fill, minmax(min(250px, 100%), 1fr));
                gap: 0.75rem;
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
            
            .student-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .btn-back, .btn-add-student, .action-buttons {
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
                    <i class="fas fa-users me-2"></i>Daftar Siswa
                </h1>
                <p class="header-subtitle">
                    Kelola data siswa MATHLine
                </p>
                <a href="student_add.php" class="btn-add-student">
                    <i class="fas fa-user-plus me-2"></i>Tambah Siswa Baru
                </a>
            </div>
        </div>
        
        <!-- Alerts -->
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistics -->
        <div class="container">
            <div class="stats-grid">
                <div class="stats-card">
                    <div class="number"><?php echo $total_students; ?></div>
                    <div class="label">Total Siswa</div>
                </div>
                <div class="stats-card">
                    <div class="number"><?php echo $active_count; ?></div>
                    <div class="label">Siswa Aktif</div>
                </div>
                <div class="stats-card">
                    <div class="number"><?php echo $inactive_count; ?></div>
                    <div class="label">Siswa Nonaktif</div>
                </div>
                <div class="stats-card">
                    <div class="number"><?php echo $total_classes; ?></div>
                    <div class="label">Kelas</div>
                </div>
            </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="container search-filter-container">
            <div class="row g-3">
                <div class="col-md-8 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               id="searchInput" 
                               class="search-input border-start-0" 
                               placeholder="Cari siswa...">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <select class="class-filter" id="classFilter">
                        <option value="">Semua Kelas</option>
                        <?php 
                        sort($unique_classes);
                        foreach ($unique_classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class); ?>">
                            Kelas <?php echo htmlspecialchars($class); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Students List -->
        <div class="container">
            <div class="students-grid" id="studentsContainer">
                <?php if (empty($students)): ?>
                    <div class="no-data">
                        <i class="fas fa-users"></i>
                        <h4 class="text-muted mb-2">Belum ada siswa terdaftar</h4>
                        <p class="text-muted mb-3">Mulai dengan menambahkan siswa baru</p>
                        <a href="student_add.php" class="btn-add-student">
                            <i class="fas fa-user-plus me-2"></i>Tambah Siswa
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                    <div class="student-item" data-class="<?php echo htmlspecialchars($student['class']); ?>">
                        <div class="student-card">
                            <div class="action-buttons">
                                <div class="dropdown">
                                    <button class="action-btn" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item text-warning" 
                                               href="?toggle_status=1&id=<?php echo $student['id']; ?>">
                                                <i class="fas fa-sync me-2"></i>
                                                Aktifkan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="student-card-content">
                                <div class="student-icon-wrapper">
                                    B
                                </div>
                                
                                <h5 class="student-card-title"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                                
                                <p class="student-card-subtitle">
                                    <i class="fas fa-id-card"></i>
                                    <span><?php echo htmlspecialchars($student['username']); ?></span>
                                </p>
                                
                                <div class="student-info-row">
                                    <span class="badge-status badge-inactive">
                                        <i class="fas fa-circle" style="font-size: 0.6rem;"></i>
                                        <span>Tidak Aktif</span>
                                    </span>
                                    
                                    <?php if (!empty($student['class'])): ?>
                                    <span class="class-badge">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Kelas <?php echo htmlspecialchars($student['class']); ?></span>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterStudents();
        });
        
        // Class filter functionality
        document.getElementById('classFilter').addEventListener('change', function() {
            filterStudents();
        });
        
        function filterStudents() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const selectedClass = document.getElementById('classFilter').value;
            const studentItems = document.querySelectorAll('.student-item');
            
            studentItems.forEach(item => {
                const name = item.querySelector('.student-card-title').textContent.toLowerCase();
                const username = item.querySelector('.student-card-subtitle').textContent.toLowerCase();
                const studentClass = item.getAttribute('data-class');
                
                const matchesSearch = name.includes(searchTerm) || 
                                     username.includes(searchTerm);
                
                const matchesClass = selectedClass === '' || 
                                    (selectedClass !== '' && studentClass === selectedClass);
                
                if (matchesSearch && matchesClass) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Confirm status change
        const toggleLinks = document.querySelectorAll('a.dropdown-item.text-warning');
        toggleLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin mengaktifkan siswa ini?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Initialize filter on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterStudents();
            
            // Handle responsive adjustments
            function adjustLayout() {
                const isMobile = window.innerWidth < 768;
                const studentsGrid = document.getElementById('studentsContainer');
                
                if (isMobile) {
                    // Mobile optimizations
                    document.querySelectorAll('.student-card').forEach(card => {
                        card.style.transition = 'transform 0.2s ease';
                    });
                }
            }
            
            adjustLayout();
            
            window.addEventListener('resize', function() {
                adjustLayout();
            });
        });
    </script>
</body>
</html>