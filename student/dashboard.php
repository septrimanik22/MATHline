<?php
// student/dashboard.php - VERSI RESPONSIVE DENGAN FOOTER FIXED

require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!hasRole('siswa')) {
    setFlashMessage('error', 'Akses ditolak. Hanya untuk siswa.');
    redirect('../index.php');
}

require_once '../includes/db_connection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

$student_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Siswa';
$class = $_SESSION['class'] ?? 'VIII';

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Dashboard Siswa - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-blue: #1976d2;
            --secondary-blue: #64b5f6;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.08);
            --biru-muda-1: #e3f2fd;
            --biru-muda-3: #90caf9;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        
        .dashboard-container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
        }
        
        /* Header */
        .dashboard-header {
            margin-bottom: 25px;
        }
        
        .dashboard-header h1 {
            color: #1e293b;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        /* Welcome Card */
        .welcome-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .welcome-card h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .welcome-card h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 12px;
            opacity: 0.9;
        }
        
        .welcome-card p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0;
            line-height: 1.5;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }
        
        @media (min-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        /* Motivation Card */
        .motivation-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            height: 100%;
            border-left: 5px solid var(--primary-blue);
        }
        
        .motivation-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .motivation-header i {
            font-size: 1.5rem;
            color: var(--primary-blue);
            margin-right: 10px;
        }
        
        .motivation-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .quote-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 3px solid var(--primary-blue);
        }
        
        .quote-text {
            font-size: 1.1rem;
            font-style: italic;
            color: #2d3748;
            line-height: 1.6;
            margin: 0 0 10px 0;
        }
        
        .quote-author {
            font-size: 0.9rem;
            color: #64748b;
            text-align: right;
            font-weight: 500;
        }
        
        /* FOOTER - DIUBAH SEPERTI DI VIEW_PROBLEMS */
        .dashboard-footer {
            background: linear-gradient(135deg, var(--biru-muda-1), var(--biru-muda-3));
            padding: 20px 0;
            margin-top: 40px;
            width: 100%;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .footer-content {
            text-align: center;
            color: #2d3748;
        }
        
        .footer-content p {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .footer-content small {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }
            
            .welcome-card {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .welcome-card h2 {
                font-size: 1.4rem;
            }
            
            .welcome-card h4 {
                font-size: 1rem;
            }
            
            .menu-header,
            .menu-content {
                padding: 15px;
            }
            
            .motivation-card {
                padding: 20px;
            }
            
            .quote-text {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            
            .welcome-card {
                padding: 15px;
            }
            
            .welcome-card h2 {
                font-size: 1.3rem;
            }
            
            .content-grid {
                gap: 15px;
            }
            
            .menu-item {
                padding: 12px;
                flex-direction: column;
                text-align: center;
            }
            
            .menu-item i {
                margin-right: 0;
                margin-bottom: 10px;
                width: 50px;
                height: 50px;
            }
            
            .menu-text h4 {
                font-size: 0.9rem;
            }
            
            .menu-text p {
                font-size: 0.8rem;
            }
            
            .dashboard-footer {
                padding: 15px 0;
                margin-top: 30px;
            }
        }
        
        @media (max-width: 375px) {
            .dashboard-container {
                padding: 8px;
            }
            
            .welcome-card h2 {
                font-size: 1.2rem;
            }
            
            .welcome-card p {
                font-size: 0.9rem;
            }
            
            .menu-header h3,
            .motivation-header h3 {
                font-size: 1.1rem;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Touch improvements */
        .menu-item {
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Print styles */
        @media print {
            .dashboard-footer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container fade-in">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>Dashboard</h1>
        </div>
        
        <!-- Welcome Card -->
        <div class="welcome-card fade-in">
            <h2>Selamat Datang, <?php echo htmlspecialchars($full_name); ?>! 🎉</h2>
            <h4>Siswa Kelas <?php echo htmlspecialchars($class); ?></h4>
            <p>Mari tingkatkan pemahamanmu tentang Persamaan Garis Lurus melalui pembelajaran berbasis masalah!</p>
        </div>
        
            
            <!-- Right Column: Motivation -->
            <div class="motivation-card fade-in">
                <div class="motivation-header">
                    <i class="fas fa-quote-left"></i>
                    <h3>Motto MATHLine</h3>
                </div>
                <div class="quote-box">
                    <p class="quote-text">
                        "Belajar matematika bukan hanya menghafal rumus, tetapi memahami pola dan menyelesaikan masalah."
                    </p>
                    <p class="quote-author">- MATHLine Motto</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FOOTER - SEPERTI DI VIEW_PROBLEMS -->
    <footer class="dashboard-footer">
        <div class="footer-container">
            <div class="footer-content">
                <p class="mb-1">
                    &copy; <?php echo date('Y'); ?> MATHLine | Pendidikan Matematika
                </p>
                <small>
                    Universitas Negeri Medan
                </small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi untuk menu items (hanya desktop)
        if (window.innerWidth > 768) {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'none';
                });
            });
        }
        
        // Touch device optimization
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (isTouchDevice) {
            document.body.classList.add('touch-device');
            
            // Remove hover effects on touch devices
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.transition = 'none';
            });
        }
        
        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            // Force reflow to fix layout issues
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        });
    </script>
</body>
</html>
<?php
$db->close();
?>