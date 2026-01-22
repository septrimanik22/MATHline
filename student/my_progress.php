<?php
// student/my_progress.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$class = $_SESSION['class'] ?? 'VIII';

// Gunakan struktur masalah yang sama dengan my_submissions.php
$pertemuan_structure = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'problems' => [
                    [
                        'id' => 'p1_f1_prob1',
                        'title' => 'Jalan Desa Petani',
                        'points' => 100
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p1_f2_prob1',
                        'title' => 'Analisis Peta Desa',
                        'points' => 80
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p1_f3_prob1',
                        'title' => 'Investigasi Rumus Gradien',
                        'points' => 90
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p1_f4_prob1',
                        'title' => 'Presentasi Solusi Petani',
                        'points' => 120
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p1_f5_prob1',
                        'title' => 'Refleksi Pembelajaran',
                        'points' => 100
                    ]
                ]
            ]
        ]
    ],
    
    'Pertemuan 2: Kemiringan (Gradien) Garis Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'problems' => [
                    [
                        'id' => 'p2_f1_prob1',
                        'title' => 'Tangga Darurat',
                        'points' => 100
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p2_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Gradien',
                        'points' => 80
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p2_f3_prob1',
                        'title' => 'Investigasi Jenis Gradien',
                        'points' => 90
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p2_f4_prob1',
                        'title' => 'Presentasi Aplikasi Gradien',
                        'points' => 120
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p2_f5_prob1',
                        'title' => 'Evaluasi Pemahaman Gradien',
                        'points' => 100
                    ]
                ]
            ]
        ]
    ],
    
    'Pertemuan 3: Garis Sejajar dan Tegak Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'problems' => [
                    [
                        'id' => 'p3_f1_prob1',
                        'title' => 'Desain Taman Sekolah',
                        'points' => 100
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p3_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Desain',
                        'points' => 80
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p3_f3_prob1',
                        'title' => 'Investigasi Hubungan Gradien',
                        'points' => 90
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p3_f4_prob1',
                        'title' => 'Presentasi Desain Final',
                        'points' => 150
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p3_f5_prob1',
                        'title' => 'Evaluasi Komprehensif',
                        'points' => 200
                    ]
                ]
            ]
        ]
    ]
];

// Hitung total masalah dari struktur
$total_masalah = 0;
foreach ($pertemuan_structure as $pertemuan) {
    foreach ($pertemuan['fases'] as $fase) {
        $total_masalah += count($fase['problems']);
    }
}

// Ambil semua submission siswa
$all_submissions = [];
$status_counts = [
    'submitted' => 0,
    'graded' => 0,
    'returned' => 0
];

$total_submitted = 0;
$total_score = 0;
$graded_count = 0;

foreach ($pertemuan_structure as $pertemuan_key => $pertemuan) {
    foreach ($pertemuan['fases'] as $fase_key => $fase) {
        foreach ($fase['problems'] as $problem) {
            // Cek di database
            $stmt = $conn->prepare("SELECT * FROM submissions WHERE student_id = ? AND problem_id = ?");
            $stmt->bind_param("is", $student_id, $problem['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $submission_data = $result->fetch_assoc();
                $status = $submission_data['status'];
                $status_counts[$status]++;
                $total_submitted++;
                
                if ($status == 'graded' && $submission_data['score'] !== null) {
                    $total_score += $submission_data['score'];
                    $graded_count++;
                }
                
                $all_submissions[] = [
                    'problem_id' => $problem['id'],
                    'title' => $problem['title'],
                    'points' => $problem['points'],
                    'pertemuan' => $pertemuan_key,
                    'fase' => $fase_key,
                    'fase_title' => $fase['title'],
                    'status' => $status,
                    'score' => $submission_data['score'] ?? null,
                    'submitted_at' => $submission_data['submitted_at'] ?? null,
                    'graded_at' => $submission_data['graded_at'] ?? null
                ];
            }
        }
    }
}

// Hitung progress per fase
$phase_progress = [];
$phases = ['Fase 1', 'Fase 2', 'Fase 3', 'Fase 4', 'Fase 5'];

foreach ($phases as $phase) {
    $phase_problems = 0;
    $phase_submitted = 0;
    $phase_scores = [];
    
    // Hitung dari struktur
    foreach ($pertemuan_structure as $pertemuan_key => $pertemuan) {
        if (isset($pertemuan['fases'][$phase])) {
            $fase_data = $pertemuan['fases'][$phase];
            $phase_problems += count($fase_data['problems']);
            
            foreach ($fase_data['problems'] as $problem) {
                foreach ($all_submissions as $sub) {
                    if ($sub['problem_id'] == $problem['id']) {
                        $phase_submitted++;
                        if ($sub['score'] !== null) {
                            $phase_scores[] = $sub['score'];
                        }
                    }
                }
            }
        }
    }
    
    
    $avg_score = 0;
    $completion_rate = 0;
    
    if ($phase_problems > 0) {
        $completion_rate = round(($phase_submitted / $phase_problems) * 100);
    }
    
    if (!empty($phase_scores)) {
        $avg_score = round(array_sum($phase_scores) / count($phase_scores), 1);
    }
    
    $phase_progress[$phase] = [
        'phase' => $phase,
        'total_problems' => $phase_problems,
        'submitted_problems' => $phase_submitted,
        'avg_score' => $avg_score,
        'completion_rate' => $completion_rate
    ];
}


$progress_percent = $total_masalah > 0 ? round(($total_submitted / $total_masalah) * 100) : 0;
$average_score = $graded_count > 0 ? round($total_score / $graded_count, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Progress Belajar - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-light: #e3f2fd;
            --primary-blue: #1976d2;
            --light-blue: #f0f8ff;
            --mobile-breakpoint: 768px;
            --fase1-color: #1976d2;
            --fase2-color: #2e7d32;
            --fase3-color: #f57c00;
            --fase4-color: #0288d1;
            --fase5-color: #7b1fa2;
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--light-blue);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            line-height: 1.5;
        }
        
        .container-fluid {
            padding: 0.75rem;
            max-width: 1400px;
            flex: 1;
        }
        
        @media (min-width: 576px) {
            .container-fluid {
                padding: 1rem;
            }
        }
        
        @media (min-width: 768px) {
            .container-fluid {
                padding: 1.25rem;
            }
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #1e293b;
        }
        
        .page-header p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0;
        }
        
        @media (max-width: 576px) {
            .page-header h4 {
                font-size: 1.25rem;
            }
        }
        
        /* User Profile Card */
        .user-profile-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), #64b5f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        /* Overview Cards Grid */
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 576px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .stat-subtext {
            font-size: 0.75rem;
            color: #adb5bd;
        }
        
        /* Phase Progress Cards */
        .phase-progress-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 576px) {
            .phase-progress-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .phase-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.05);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .phase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .phase-card.fase-1 { border-left-color: var(--fase1-color); }
        .phase-card.fase-2 { border-left-color: var(--fase2-color); }
        .phase-card.fase-3 { border-left-color: var(--fase3-color); }
        .phase-card.fase-4 { border-left-color: var(--fase4-color); }
        .phase-card.fase-5 { border-left-color: var(--fase5-color); }
        
        .phase-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .phase-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .phase-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            color: white;
        }
        
        .phase-badge.fase-1 { background-color: var(--fase1-color); }
        .phase-badge.fase-2 { background-color: var(--fase2-color); }
        .phase-badge.fase-3 { background-color: var(--fase3-color); }
        .phase-badge.fase-4 { background-color: var(--fase4-color); }
        .phase-badge.fase-5 { background-color: var(--fase5-color); }
        
        /* Progress Bar */
        .progress-container {
            margin: 0.75rem 0;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .progress-bar-wrapper {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease;
        }
        
        .progress-fill.fase-1 { background: linear-gradient(90deg, var(--fase1-color), #64b5f6); }
        .progress-fill.fase-2 { background: linear-gradient(90deg, var(--fase2-color), #66bb6a); }
        .progress-fill.fase-3 { background: linear-gradient(90deg, var(--fase3-color), #ffb74d); }
        .progress-fill.fase-4 { background: linear-gradient(90deg, var(--fase4-color), #29b6f6); }
        .progress-fill.fase-5 { background: linear-gradient(90deg, var(--fase5-color), #ba68c8); }
        
        /* Phase Stats */
        .phase-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .phase-stat {
            text-align: center;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
        }
        
        .phase-stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .phase-stat-label {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        /* Progress Chart */
        .progress-chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }
        
        .chart-item {
            text-align: center;
            padding: 0.75rem;
        }
        
        .chart-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .chart-label {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        /* Table Container */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .table-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1.25rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table-header h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        /* Mobile Cards Layout */
        .mobile-phase-cards {
            display: none;
        }
        
        @media (max-width: 767.98px) {
            .table-responsive {
                display: none;
            }
            
            .mobile-phase-cards {
                display: block;
            }
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            background-color: #f8f9fa;
        }
        
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        
        /* Score Display */
        .score-display {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .score-excellent { color: #198754; }
        .score-good { color: #0dcaf0; }
        .score-average { color: #ffc107; }
        .score-poor { color: #dc3545; }
        
        /* Footer */
        .page-footer {
            background: linear-gradient(135deg, var(--primary-light), var(--fase1-color));
            padding: 1.25rem 1rem;
            margin-top: 2rem;
        }
        
        .page-footer p {
            margin: 0;
            font-size: 0.85rem;
            color: #1e293b;
            text-align: center;
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
                color: #e2e8f0;
            }
            
            .stat-card,
            .phase-card,
            .progress-chart-container,
            .table-container,
            .user-profile-card {
                background: #1e293b;
                border-color: #334155;
                color: #e2e8f0;
            }
            
            .table-header {
                background: linear-gradient(135deg, #334155, #475569);
                border-color: #475569;
            }
            
            .phase-stat {
                background: #334155;
            }
            
            .progress-bar-wrapper {
                background: #334155;
            }
            
            .stat-number,
            .phase-title,
            .chart-title,
            .phase-stat-value,
            .chart-value {
                color: #e2e8f0;
            }
            
            .stat-label,
            .stat-subtext,
            .progress-label,
            .phase-stat-label,
            .chart-label {
                color: #94a3b8;
            }
            
            .page-footer {
                background: linear-gradient(135deg, #1e293b, #334155);
                color: #e2e8f0;
            }
        }
        
        /* Touch Optimizations */
        .stat-card,
        .phase-card {
            cursor: pointer;
        }
        
        .stat-card:active,
        .phase-card:active {
            transform: scale(0.98);
            transition: transform 0.1s;
        }
        
        /* Loading Animation */
        .loading {
            position: relative;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
        }
        
        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <h4>Progress Belajar</h4>
                    <p>Pantau perkembangan belajar Anda secara detail</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">
                        <?php echo $progress_percent; ?>% Selesai
                    </span>
                </div>
            </div>
        </div>
        
        <!-- User Profile Card -->
        <div class="user-profile-card">
            <div class="d-flex align-items-center">
                <div class="user-avatar-large">
                    <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                </div>
                <div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($full_name); ?></h5>
                    <p class="text-muted mb-0">Siswa Kelas <?php echo htmlspecialchars($class); ?></p>
                </div>
            </div>
        </div>
        
        
        <!-- Phase Progress Cards (Mobile) -->
        <div class="mobile-phase-cards">
            <?php foreach ($phase_progress as $phase => $data): 
                $fase_num = substr($phase, -1);
                $completion_rate = $data['completion_rate'];
            ?>
            <div class="phase-card fase-<?php echo $fase_num; ?> mb-3">
                <div class="phase-header">
                    <div class="phase-title">
                        <span class="phase-badge fase-<?php echo $fase_num; ?>"><?php echo $phase; ?></span>
                        <span><?php echo $phase; ?></span>
                    </div>
                    <div class="phase-percentage">
                        <span class="fw-bold"><?php echo $completion_rate; ?>%</span>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span><?php echo $data['submitted_problems']; ?>/<?php echo $data['total_problems']; ?></span>
                    </div>
                    <div class="progress-bar-wrapper">
                        <div class="progress-fill fase-<?php echo $fase_num; ?>" 
                             style="width: <?php echo $completion_rate; ?>%"></div>
                    </div>
                </div>
                
                <div class="phase-stats">
                    <div class="phase-stat">
                        <div class="phase-stat-value"><?php echo $data['total_problems']; ?></div>
                        <div class="phase-stat-label">Total</div>
                    </div>
                    <div class="phase-stat">
                        <div class="phase-stat-value"><?php echo $data['submitted_problems']; ?></div>
                        <div class="phase-stat-label">Terkumpul</div>
                    </div>
                    <div class="phase-stat">
                        <div class="phase-stat-value">
                            <?php echo $data['avg_score'] > 0 ? $data['avg_score'] : '-'; ?>
                        </div>
                        <div class="phase-stat-label">Nilai Rata-rata</div>
                    </div>
                    <div class="phase-stat">
                        <div class="phase-stat-value"><?php echo $completion_rate; ?>%</div>
                        <div class="phase-stat-label">Selesai</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        
        <!-- Phase Progress Table (Desktop) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="15%">Fase</th>
                            <th width="20%">Total Masalah</th>
                            <th width="20%">Dikumpulkan</th>
                            <th width="25%">Progress</th>
                            <th width="20%">Nilai Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phase_progress as $phase => $data): 
                            $fase_num = substr($phase, -1);
                            $completion_rate = $data['completion_rate'];
                            
                            // Determine score color
                            $score_class = '';
                            if ($data['avg_score'] >= 80) {
                                $score_class = 'score-excellent';
                            } elseif ($data['avg_score'] >= 70) {
                                $score_class = 'score-good';
                            } elseif ($data['avg_score'] >= 60) {
                                $score_class = 'score-average';
                            } elseif ($data['avg_score'] > 0) {
                                $score_class = 'score-poor';
                            }
                        ?>
                        <tr>
                            <td>
                                <span class="phase-badge fase-<?php echo $fase_num; ?>">
                                    <?php echo $phase; ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo $data['total_problems']; ?></div>
                                <small class="text-muted">masalah</small>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo $data['submitted_problems']; ?></div>
                                <small class="text-muted">terkumpul</small>
                            </td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-label">
                                        <span><?php echo $completion_rate; ?>%</span>
                                        <span><?php echo $data['submitted_problems']; ?>/<?php echo $data['total_problems']; ?></span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-fill fase-<?php echo $fase_num; ?>" 
                                             style="width: <?php echo $completion_rate; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($data['avg_score'] > 0): ?>
                                    <div class="score-display <?php echo $score_class; ?>">
                                        <?php echo $data['avg_score']; ?>
                                        <small class="text-muted">/100</small>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    
    <!-- Footer -->
    <footer class="page-footer">
        <div class="container-fluid">
            <p>&copy; 2026 MATHLine | Pendidikan Matematika | Universitas Negeri Medan</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate progress bars on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease';
                    bar.style.width = width;
                }, 300);
            });
            
            // Add click animations to cards
            const cards = document.querySelectorAll('.stat-card, .phase-card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Handle touch feedback
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.opacity = '0.9';
                });
                
                card.addEventListener('touchend', function() {
                    this.style.opacity = '1';
                });
            });
            
            // Update progress percentage in header
            const progressPercent = <?php echo $progress_percent; ?>;
            const progressBadge = document.querySelector('.badge.bg-primary.fs-6');
            if (progressBadge) {
                progressBadge.textContent = `${progressPercent}% Selesai`;
            }
            
            // Add loading animation to action cards
            const actionCards = document.querySelectorAll('.stat-card.text-decoration-none');
            actionCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const link = this.getAttribute('href');
                    if (link) {
                        // Add loading state
                        const icon = this.querySelector('i');
                        if (icon) {
                            const originalClass = icon.className;
                            icon.className = 'fas fa-spinner fa-spin me-3';
                            
                            setTimeout(() => {
                                icon.className = originalClass;
                            }, 1000);
                        }
                    }
                });
            });
        });
        
        // Handle window resize for responsive adjustments
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                // Update table visibility based on screen size
                const mobileCards = document.querySelector('.mobile-phase-cards');
                const tableContainer = document.querySelector('.table-responsive');
                
                if (window.innerWidth < 768) {
                    if (mobileCards) mobileCards.style.display = 'block';
                    if (tableContainer) tableContainer.style.display = 'none';
                } else {
                    if (mobileCards) mobileCards.style.display = 'none';
                    if (tableContainer) tableContainer.style.display = 'block';
                }
            }, 250);
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>