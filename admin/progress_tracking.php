<?php
// admin/progress_tracking.php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireRole(['admin', 'guru']);

$db = Database::getInstance();
$conn = $db->getConnection();

// Build query for student progress
$query = "
    SELECT 
        u.id,
        u.full_name,
        s.class,
        COUNT(DISTINCT sb.id) as total_submissions,
        COUNT(DISTINCT CASE WHEN sb.status = 'graded' THEN sb.id END) as graded_submissions,
        AVG(sb.score) as average_score,
        MAX(sb.submitted_at) as last_submission
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    LEFT JOIN submissions sb ON u.id = sb.student_id 
    WHERE u.role = 'siswa' AND s.status = 'active'
    GROUP BY u.id, u.full_name, s.class
    ORDER BY u.full_name ASC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Hardcode satu siswa untuk contoh (Budi Santoso)
$example_student = [
    'id' => 1,
    'full_name' => 'Budi Santoso',
    'class' => 'VIII',
    'total_submissions' => 0,
    'graded_submissions' => 0,
    'average_score' => null,
    'last_submission' => null
];

// Jika tidak ada siswa di database, gunakan contoh
if (empty($students)) {
    $students = [$example_student];
}

// Calculate statistics
$total_subs = array_sum(array_column($students, 'total_submissions'));
$graded_subs = array_sum(array_column($students, 'graded_submissions'));
$completion_rate = 0;

// Filter average scores (exclude null values)
$avg_scores = array_filter(array_column($students, 'average_score'), function($score) {
    return $score !== null;
});
$overall_avg = !empty($avg_scores) ? round(array_sum($avg_scores) / count($avg_scores), 1) : 0;

$active_students = array_filter($students, function($s) {
    return $s['total_submissions'] > 0;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Progress Siswa - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #10b981;
            --light-green: #d1fae5;
            --medium-green: #a7f3d0;
            --dark-green: #059669;
            --accent-green: #34d399;
            --card-bg: #f0fdf4;
            --hover-green: #dcfce7;
            --background-light: #f7fee7;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --border-color: #e5e7eb;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--background-light);
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        /* Container Responsive */
        .main-container {
            padding: clamp(0.75rem, 2vw, 1.5rem);
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--light-green), white);
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1rem, 2.5vw, 1.5rem);
            margin-bottom: clamp(1rem, 2.5vw, 2rem);
            border-left: clamp(3px, 0.8vw, 5px) solid var(--dark-green);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
        }
        
        .page-header h1 {
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }
        
        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
            gap: clamp(0.75rem, 1.5vw, 1rem);
            margin-bottom: clamp(1.5rem, 3vw, 2.5rem);
        }
        
        .stat-card {
            background: white;
            border-radius: clamp(10px, 1.5vw, 12px);
            padding: clamp(1rem, 1.8vw, 1.25rem);
            text-align: center;
            border: 1px solid var(--medium-green);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
        }
        
        .stat-number {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            line-height: 1;
            color: var(--dark-green);
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
        }
        
        .stat-zero {
            color: var(--text-gray);
        }
        
        .info-label {
            color: var(--text-gray);
            font-size: clamp(0.85rem, 1.5vw, 0.95rem);
            margin: 0;
        }
        
        /* Section Title */
        .section-title {
            color: var(--dark-green);
            border-bottom: 2px solid var(--light-green);
            padding-bottom: clamp(0.5rem, 1vw, 0.75rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            font-size: clamp(1.25rem, 2.5vw, 1.5rem);
            font-weight: 600;
        }
        
        /* Progress Cards */
        .progress-card {
            background: white;
            border-radius: clamp(10px, 1.5vw, 12px);
            padding: clamp(1rem, 1.8vw, 1.5rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            border: 1px solid var(--medium-green);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .progress-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
            border-color: var(--primary-green);
        }
        
        /* Student Info */
        .student-info {
            display: flex;
            align-items: center;
            margin-bottom: clamp(1rem, 1.8vw, 1.5rem);
        }
        
        .user-avatar {
            width: clamp(45px, 8vw, 50px);
            height: clamp(45px, 8vw, 50px);
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border-radius: clamp(8px, 1.5vw, 12px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin-right: clamp(0.75rem, 1.5vw, 1rem);
            flex-shrink: 0;
        }
        
        .student-details {
            flex: 1;
            min-width: 0;
        }
        
        .student-name {
            font-size: clamp(1rem, 2vw, 1.1rem);
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 0.25rem 0;
            line-height: 1.3;
        }
        
        .student-class {
            color: var(--text-gray);
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            margin: 0;
        }
        
        /* Progress Bar */
        .progress-section {
            margin-bottom: clamp(1rem, 1.8vw, 1.5rem);
        }
        
        .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: clamp(0.5rem, 1vw, 0.75rem);
        }
        
        .progress-percentage {
            font-size: clamp(1.1rem, 2vw, 1.25rem);
            font-weight: 700;
            color: var(--dark-green);
        }
        
        .progress-percentage-zero {
            color: var(--text-gray);
        }
        
        .progress-text {
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            color: var(--text-gray);
        }
        
        .progress-bar-custom {
            height: clamp(8px, 1.5vw, 10px);
            border-radius: clamp(4px, 0.8vw, 5px);
            background-color: var(--border-color);
            overflow: hidden;
            width: 100%;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: clamp(4px, 0.8vw, 5px);
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            transition: width 1s ease;
        }
        
        .task-info {
            color: var(--text-gray);
            font-size: clamp(0.8rem, 1.3vw, 0.85rem);
            margin-top: clamp(0.5rem, 1vw, 0.75rem);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: clamp(0.5rem, 1vw, 0.75rem);
            flex-wrap: wrap;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            border: none;
            padding: clamp(8px, 1.2vw, 10px) clamp(16px, 2vw, 20px);
            border-radius: clamp(6px, 1vw, 8px);
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: clamp(4px, 0.8vw, 6px);
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            min-height: 44px;
            width: 100%;
        }
        
        .btn-success-custom:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }
        
        /* No Data State */
        .no-data {
            color: var(--text-gray);
            font-style: italic;
            text-align: center;
            padding: clamp(2rem, 4vw, 3rem);
            font-size: clamp(0.9rem, 1.8vw, 1rem);
        }
        
        .no-data i {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 1rem;
            color: var(--medium-green);
        }
        
        /* Responsive Grid Layout */
        @media (min-width: 768px) {
            .progress-card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(min(350px, 100%), 1fr));
                gap: clamp(1rem, 2vw, 1.5rem);
            }
            
            .progress-card {
                margin-bottom: 0;
            }
            
            .student-info {
                margin-bottom: 1rem;
            }
            
            .btn-success-custom {
                width: auto;
                min-width: 180px;
            }
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .main-container {
                padding: clamp(0.5rem, 1.5vw, 1rem);
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));
            }
        }
        
        @media (max-width: 992px) {
            .page-header {
                border-radius: 12px;
                padding: 1.25rem;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
            
            .stats-grid {
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 0.75rem;
            }
            
            .page-header {
                padding: 1rem;
                margin-bottom: 1.5rem;
                border-radius: 10px;
            }
            
            .page-header h1 {
                font-size: 1.3rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 0.875rem;
                border-radius: 10px;
            }
            
            .stat-number {
                font-size: 1.4rem;
            }
            
            .section-title {
                font-size: 1.2rem;
                margin-bottom: 1.25rem;
            }
            
            .progress-card {
                padding: 1.25rem;
                border-radius: 10px;
            }
            
            .student-info {
                margin-bottom: 1rem;
            }
            
            .user-avatar {
                width: 42px;
                height: 42px;
                font-size: 1.1rem;
                margin-right: 0.875rem;
            }
            
            .student-name {
                font-size: 1rem;
            }
            
            .progress-percentage {
                font-size: 1.1rem;
            }
            
            .btn-success-custom {
                min-height: 42px;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 576px) {
            .main-container {
                padding: 0.5rem;
            }
            
            .page-header {
                padding: 0.875rem;
                border-radius: 8px;
                margin-bottom: 1.25rem;
            }
            
            .page-header h1 {
                font-size: 1.2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .stat-card {
                padding: 0.75rem;
                border-radius: 8px;
            }
            
            .stat-number {
                font-size: 1.3rem;
                margin-bottom: 0.5rem;
            }
            
            .info-label {
                font-size: 0.85rem;
            }
            
            .section-title {
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }
            
            .progress-card {
                padding: 1rem;
                border-radius: 8px;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                margin-right: 0.75rem;
                border-radius: 8px;
            }
            
            .student-name {
                font-size: 0.95rem;
            }
            
            .student-class {
                font-size: 0.8rem;
            }
            
            .progress-header {
                margin-bottom: 0.5rem;
            }
            
            .progress-percentage {
                font-size: 1rem;
            }
            
            .progress-text {
                font-size: 0.8rem;
            }
            
            .progress-bar-custom {
                height: 6px;
                border-radius: 3px;
            }
            
            .task-info {
                font-size: 0.75rem;
                margin-top: 0.5rem;
            }
            
            .action-buttons {
                gap: 0.5rem;
            }
            
            .btn-success-custom {
                padding: 8px 12px;
                font-size: 0.8rem;
                min-height: 40px;
                width: 100%;
                justify-content: center;
            }
            
            .no-data {
                padding: 1.5rem;
                font-size: 0.9rem;
            }
            
            .no-data i {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 375px) {
            .page-header {
                padding: 0.75rem;
            }
            
            .page-header h1 {
                font-size: 1.1rem;
            }
            
            .stat-card {
                padding: 0.625rem;
            }
            
            .stat-number {
                font-size: 1.2rem;
            }
            
            .progress-card {
                padding: 0.875rem;
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
                margin-right: 0.625rem;
            }
            
            .student-name {
                font-size: 0.9rem;
            }
            
            .section-title {
                font-size: 1rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .progress-card:hover {
                transform: none;
            }
            
            .stat-card:hover {
                transform: none;
            }
            
            .btn-success-custom:hover {
                transform: none;
                box-shadow: none;
            }
            
            .btn-success-custom:active {
                transform: scale(0.98);
            }
            
            /* Larger touch targets */
            .btn-success-custom {
                min-height: 48px;
            }
            
            .action-btn {
                min-width: 44px;
                min-height: 44px;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .main-container {
                padding: 0.5rem;
            }
            
            .page-header {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(min(180px, 100%), 1fr));
                margin-bottom: 1rem;
            }
            
            .progress-card {
                margin-bottom: 0.5rem;
                padding: 0.75rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .page-header {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .stat-card, .progress-card {
                box-shadow: none;
                border: 1px solid #000;
                break-inside: avoid;
            }
            
            .btn-success-custom {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="text-success">
                        <i class="fas fa-chart-line me-2"></i>Progress Siswa
                    </h1>
                </div>
                <a href="dashboard.php" class="btn btn-outline-success d-none d-md-inline-flex align-items-center">
                    <i class="fas fa-arrow-left me-2"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
        
        <!-- Mobile Back Button -->
        <div class="d-block d-md-none mb-3">
            <a href="dashboard.php" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center">
                <i class="fas fa-arrow-left me-2"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
        
        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($students); ?></div>
                <div class="info-label">Total Siswa</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number stat-zero">0%</div>
                <div class="info-label">Tuntas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="info-label">Rata-rata Nilai</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="info-label">Siswa Aktif</div>
            </div>
        </div>
        
        <!-- Student Progress -->
        <div class="mb-4">
            <h3 class="section-title">
                <i class="fas fa-user-graduate me-2"></i>Progress Siswa
            </h3>
        </div>
        
        <?php if (!empty($students)): ?>
            <div class="progress-card-grid">
                <?php foreach ($students as $student): ?>
                <div class="progress-card">
                    <!-- Student Info -->
                    <div class="student-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                        </div>
                        <div class="student-details">
                            <h5 class="student-name"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                            <p class="student-class">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Kelas <?php echo htmlspecialchars($student['class']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Progress Section -->
                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="progress-percentage progress-percentage-zero">0%</span>
                            <span class="progress-text">Progress Tugas</span>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                        <div class="task-info">
                            0 tugas dikerjakan
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="action-buttons">
                        <a href="student_detail.php?id=<?php echo $student['id']; ?>" 
                           class="btn btn-success-custom">
                            <i class="fas fa-chart-bar me-1"></i>
                            <span>Detail Progress</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="progress-card">
                <div class="no-data">
                    <i class="fas fa-users"></i>
                    <h5>Tidak ada data siswa</h5>
                    <p>Belum ada siswa terdaftar dalam sistem</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate progress bars on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = bar.getAttribute('style').split('width:')[1] || '0%';
                }, 100);
            });
            
            // Handle touch devices
            if ('ontouchstart' in window || navigator.maxTouchPoints) {
                // Add active states for touch
                const buttons = document.querySelectorAll('.btn-success-custom');
                buttons.forEach(btn => {
                    btn.addEventListener('touchstart', function() {
                        this.classList.add('active');
                    });
                    btn.addEventListener('touchend', function() {
                        this.classList.remove('active');
                    });
                });
            }
            
            // Responsive adjustments
            function adjustLayout() {
                const container = document.querySelector('.main-container');
                const cards = document.querySelectorAll('.progress-card');
                
                if (window.innerWidth < 768) {
                    // Mobile optimizations
                    cards.forEach(card => {
                        card.style.padding = '1rem';
                    });
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
        });
    </script>
</body>
</html>