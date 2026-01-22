<?php
// student/my_submissions.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['user_id'];

// Struktur masalah yang sama dengan view_problems.php (SESUAI DENGAN SKALA BARU)
$pertemuan_structure = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'problems' => [
                    [
                        'id' => 'p1_f1_prob1',
                        'title' => 'Jalan Desa Petani'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p1_f2_prob1',
                        'title' => 'Analisis Peta Desa'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p1_f3_prob1',
                        'title' => 'Investigasi Rumus Gradien'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p1_f4_prob1',
                        'title' => 'Presentasi Solusi Petani'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p1_f5_prob1',
                        'title' => 'Refleksi Pembelajaran'
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
                        'title' => 'Tangga Darurat'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p2_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Gradien'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p2_f3_prob1',
                        'title' => 'Investigasi Jenis Gradien'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p2_f4_prob1',
                        'title' => 'Presentasi Aplikasi Gradien'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p2_f5_prob1',
                        'title' => 'Evaluasi Pemahaman Gradien'
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
                        'title' => 'Desain Taman Sekolah'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'problems' => [
                    [
                        'id' => 'p3_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Desain'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'problems' => [
                    [
                        'id' => 'p3_f3_prob1',
                        'title' => 'Investigasi Hubungan Gradien'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'problems' => [
                    [
                        'id' => 'p3_f4_prob1',
                        'title' => 'Presentasi Desain Final'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'problems' => [
                    [
                        'id' => 'p3_f5_prob1',
                        'title' => 'Evaluasi Komprehensif'
                    ]
                ]
            ]
        ]
    ]
];

// Hitung total masalah
$total_masalah = 0;
foreach ($pertemuan_structure as $pertemuan) {
    foreach ($pertemuan['fases'] as $fase) {
        $total_masalah += count($fase['problems']);
    }
}

// Cek submission untuk setiap masalah
$all_submissions = [];
$total_submitted = 0;
$status_counts = [
    'submitted' => 0,
    'graded' => 0,
    'returned' => 0
];

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
                
                $all_submissions[] = [
                    'problem_id' => $problem['id'],
                    'title' => $problem['title'],
                    'pertemuan' => $pertemuan_key,
                    'fase' => $fase_key,
                    'fase_title' => $fase['title'],
                    'status' => $status,
                    'score' => $submission_data['score'] ?? null,
                    'graded_by' => null,
                    'submitted_at' => $submission_data['submitted_at'] ?? date('Y-m-d H:i:s'),
                    'graded_at' => $submission_data['graded_at'] ?? null,
                    'feedback' => $submission_data['feedback'] ?? null,
                    'file_path' => $submission_data['file_path'] ?? null
                ];
            } else {
                // Belum dikumpulkan
                $all_submissions[] = [
                    'problem_id' => $problem['id'],
                    'title' => $problem['title'],
                    'pertemuan' => $pertemuan_key,
                    'fase' => $fase_key,
                    'fase_title' => $fase['title'],
                    'status' => 'not_submitted',
                    'score' => null,
                    'graded_by' => null,
                    'submitted_at' => null,
                    'graded_at' => null,
                    'feedback' => null,
                    'file_path' => null
                ];
            }
        }
    }
}

// Urutkan berdasarkan tanggal submit (yang belum dikumpulkan di akhir)
usort($all_submissions, function($a, $b) {
    if ($a['submitted_at'] == null && $b['submitted_at'] == null) {
        return 0;
    }
    if ($a['submitted_at'] == null) {
        return 1;
    }
    if ($b['submitted_at'] == null) {
        return -1;
    }
    return strtotime($b['submitted_at']) - strtotime($a['submitted_at']);
});

// Hitung statistik
$stats = [
    'total' => $total_masalah,
    'submitted' => $status_counts['submitted'],
    'graded' => $status_counts['graded'],
    'returned' => $status_counts['returned'],
    'not_submitted' => $total_masalah - $total_submitted,
    'avg_score' => 0
];

// Hitung rata-rata nilai jika ada yang sudah dinilai
if ($status_counts['graded'] > 0) {
    $total_score = 0;
    $count_graded = 0;
    foreach ($all_submissions as $sub) {
        if ($sub['status'] == 'graded' && $sub['score'] !== null) {
            $total_score += $sub['score'];
            $count_graded++;
        }
    }
    $stats['avg_score'] = $count_graded > 0 ? round($total_score / $count_graded, 1) : 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Tugas Saya - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-light: #e3f2fd;
            --primary-blue: #1976d2;
            --light-blue: #f0f8ff;
            --fase1-color: #1976d2;
            --fase2-color: #2e7d32;
            --fase3-color: #f57c00;
            --fase4-color: #0288d1;
            --fase5-color: #7b1fa2;
            --mobile-breakpoint: 768px;
            --tablet-breakpoint: 992px;
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            background-color: var(--light-blue);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        
        .container-fluid {
            padding: 1rem;
            max-width: 1400px;
            flex: 1;
        }
        
        /* Mobile First Padding */
        @media (min-width: 576px) {
            .container-fluid {
                padding: 1.25rem;
            }
        }
        
        @media (min-width: 768px) {
            .container-fluid {
                padding: 1.5rem;
            }
        }
        
        /* Header Section Mobile Optimized */
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .page-header p {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        @media (max-width: 576px) {
            .page-header h4 {
                font-size: 1.25rem;
            }
            
            .page-header p {
                font-size: 0.85rem;
            }
        }
        
        /* Statistics Cards Mobile Optimized */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 375px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
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
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .stat-subtext {
            font-size: 0.75rem;
            color: #adb5bd;
        }
        
        /* Progress Section Mobile Optimized */
        .progress-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .progress-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .progress-description {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .progress-bar-container {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-blue), #64b5f6);
            border-radius: 5px;
            transition: width 1s ease;
        }
        
        /* Progress Ring Mobile Optimized */
        .progress-ring-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .progress-ring {
            width: 80px;
            height: 80px;
        }
        
        @media (min-width: 576px) {
            .progress-ring {
                width: 100px;
                height: 100px;
            }
        }
        
        .progress-ring-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-blue);
        }
        
        /* Table Container Mobile Optimized */
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
        
        /* Pertemuan Section Mobile Optimized */
        .pertemuan-section {
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .pertemuan-title {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .pertemuan-title h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0;
            color: #333;
        }
        
        /* Table Mobile Optimized */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            margin-bottom: 0;
            width: 100%;
            min-width: 600px; /* Minimum width for table on mobile */
        }
        
        .table th {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            background-color: #f8f9fa;
            white-space: nowrap;
        }
        
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        
        /* Mobile Stacked Layout for Table Rows */
        @media (max-width: 767.98px) {
            .table-mobile-card {
                display: block;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                background: white;
            }
            
            .table-mobile-card:last-child {
                margin-bottom: 0;
            }
            
            .table-mobile-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 0.75rem;
            }
            
            .table-mobile-row:last-child {
                margin-bottom: 0;
            }
            
            .table-mobile-label {
                font-weight: 600;
                color: #666;
                font-size: 0.85rem;
                min-width: 80px;
                margin-right: 1rem;
            }
            
            .table-mobile-value {
                flex: 1;
                text-align: right;
                font-size: 0.9rem;
            }
        }
        
        /* Badges Mobile Optimized */
        .badge-fase {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .badge-fase-1 { background-color: var(--fase1-color); }
        .badge-fase-2 { background-color: var(--fase2-color); }
        .badge-fase-3 { background-color: var(--fase3-color); }
        .badge-fase-4 { background-color: var(--fase4-color); }
        .badge-fase-5 { background-color: var(--fase5-color); }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }
        
        .status-not-submitted { 
            background-color: #6c757d; 
            color: #ffffff;
        }
        
        .status-submitted { 
            background-color: #0d6efd; 
            color: #ffffff;
        }
        
        .status-graded { 
            background-color: #198754; 
            color: #ffffff;
        }
        
        .status-returned { 
            background-color: #fd7e14; 
            color: #ffffff;
        }
        
        /* Score Display Mobile Optimized */
        .score-display {
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
        }
        
        /* Empty State Mobile Optimized */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }
        
        @media (max-width: 576px) {
            .empty-state {
                padding: 2rem 1rem;
            }
        }
        
        .empty-icon {
            font-size: 3rem;
            opacity: 0.2;
            margin-bottom: 1rem;
        }
        
        .empty-state h4 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: #666;
        }
        
        .empty-state p {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        
        /* Button Mobile Optimized */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), #64b5f6);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        /* Info Alert Mobile Optimized */
        .info-alert {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        
        .info-alert small {
            font-size: 0.85rem;
            line-height: 1.5;
        }
        
        /* Footer Mobile Optimized */
        .page-footer {
            background: linear-gradient(135deg, #e3f2fd, #90caf9);
            padding: 1.5rem 1rem;
            margin-top: 2rem;
        }
        
        .page-footer p {
            margin: 0;
            font-size: 0.85rem;
            color: #333;
            text-align: center;
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #e0e0e0;
            }
            
            .stat-card,
            .progress-card,
            .table-container,
            .table-mobile-card {
                background: #1e1e1e;
                border-color: #333;
                color: #e0e0e0;
            }
            
            .table-header,
            .pertemuan-title {
                background: linear-gradient(135deg, #2d2d2d, #1a1a1a);
                border-color: #333;
                color: #e0e0e0;
            }
            
            .table th {
                background-color: #2d2d2d;
                color: #e0e0e0;
                border-color: #444;
            }
            
            .table td {
                border-color: #333;
                color: #e0e0e0;
            }
            
            .info-alert {
                background: #2d2d2d;
                border-color: #444;
                color: #e0e0e0;
            }
            
            .page-footer {
                background: linear-gradient(135deg, #2d3748, #4a5568);
                color: #e0e0e0;
            }
            
            .progress-description,
            .stat-label,
            .stat-subtext,
            .empty-state h4,
            .empty-state p {
                color: #b0b0b0;
            }
            
            .table-mobile-label {
                color: #aaa;
            }
        }
        
        /* Animation for Progress Fill */
        @keyframes progressFill {
            from { width: 0; }
            to { width: var(--progress-width); }
        }
        
        /* Touch Optimizations */
        .stat-card,
        .pertemuan-section,
        .table-mobile-card {
            cursor: pointer;
        }
        
        .stat-card:active,
        .table-mobile-card:active {
            transform: scale(0.98);
            transition: transform 0.1s;
        }
        
        /* Safe Area for Notch Phones */
        @supports (padding: max(0px)) {
            .container-fluid {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
            }
            
            .page-footer {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
            }
        }
        
        /* Print Styles */
        @media print {
            .page-footer {
                display: none;
            }
            
            .stat-card,
            .progress-card,
            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        
        <!-- Progress Section -->
        <div class="progress-card">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h5 class="progress-title">Progress Pengumpulan</h5>
                    <p class="progress-description">
                        <?php 
                        $progress_percent = $total_submitted > 0 ? round(($total_submitted / $total_masalah) * 100) : 0;
                        echo "Anda telah mengumpulkan {$total_submitted} dari {$total_masalah} masalah";
                        ?>
                    </p>
                    <div class="progress-bar-container">
                        <div class="progress-fill" 
                             style="--progress-width: <?php echo $progress_percent; ?>%;
                                    width: <?php echo $progress_percent; ?>%;
                                    animation: progressFill 1s ease;">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="progress-ring-container">
                        <div class="position-relative">
                            <svg class="progress-ring" viewBox="0 0 36 36">
                                <path class="circle-bg"
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="#e9ecef"
                                    stroke-width="3"/>
                                <path class="circle"
                                    stroke-dasharray="<?php echo $progress_percent; ?>, 100"
                                    d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none"
                                    stroke="#1976d2"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    id="progressCircle"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <span class="progress-ring-text"><?php echo $progress_percent; ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submissions List -->
        <div class="row">
            <div class="col-12">
                <?php if (count($all_submissions) > 0): ?>
                        
                        <!-- Kelompokkan berdasarkan pertemuan -->
                        <?php 
                        $pertemuan_groups = [];
                        foreach ($all_submissions as $sub) {
                            $pertemuan_groups[$sub['pertemuan']][] = $sub;
                        }
                        ?>
                        
                        <!-- Desktop Table (min-width: 768px) -->
                        <div class="d-none d-md-block">
                            <?php foreach ($pertemuan_groups as $pertemuan_key => $submissions): ?>
                            <div class="pertemuan-section">
                                <div class="pertemuan-title">
                                    <h5><?php echo $pertemuan_key; ?></h5>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Masalah</th>
                                                <th>Fase</th>
                                                <th>Status</th>
                                                <th class="text-center">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            <?php foreach ($submissions as $sub): 
                                                $status_class = 'status-' . str_replace('_', '-', $sub['status']);
                                                $fase_num = substr($sub['fase'], -1);
                                                $fase_badge_class = 'badge-fase-' . $fase_num;
                                            ?>
                                            <tr>
                                                <td class="text-center text-muted"><?php echo $no++; ?></td>
                                                <td>
                                                    <div class="fw-medium"><?php echo htmlspecialchars($sub['title']); ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge-fase <?php echo $fase_badge_class; ?> me-2">
                                                            <?php echo $sub['fase']; ?>
                                                        </span>
                                                        <span class="fase-label small">
                                                            <?php echo $sub['fase_title']; ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($sub['status'] == 'not_submitted'): ?>
                                                        <span class="badge status-not-submitted badge-status">
                                                            <i class="fas fa-clock me-1"></i>Belum Dikumpulkan
                                                        </span>
                                                    <?php elseif ($sub['status'] == 'submitted'): ?>
                                                        <span class="badge status-submitted badge-status">
                                                            <i class="fas fa-hourglass-half me-1"></i>Menunggu Penilaian
                                                        </span>
                                                    <?php elseif ($sub['status'] == 'graded'): ?>
                                                        <span class="badge status-graded badge-status">
                                                            <i class="fas fa-check-circle me-1"></i>Sudah Dinilai
                                                        </span>
                                                    <?php elseif ($sub['status'] == 'returned'): ?>
                                                        <span class="badge status-returned badge-status">
                                                            <i class="fas fa-redo me-1"></i>Dikembalikan
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($sub['score'] !== null): ?>
                                                        <div class="score-display text-success fw-bold">
                                                            <?php echo $sub['score']; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php if ($sub['status'] == 'not_submitted'): ?>
                                                            <span class="text-muted">-</span>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Belum dinilai</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Mobile Cards (max-width: 767.98px) -->
                        <div class="d-md-none">
                            <?php $no = 1; ?>
                            <?php foreach ($pertemuan_groups as $pertemuan_key => $submissions): ?>
                            <div class="pertemuan-section">
                                <div class="pertemuan-title">
                                    <h5><?php echo $pertemuan_key; ?></h5>
                                </div>
                                
                                <div class="p-3">
                                    <?php foreach ($submissions as $sub): 
                                        $status_class = 'status-' . str_replace('_', '-', $sub['status']);
                                        $fase_num = substr($sub['fase'], -1);
                                        $fase_badge_class = 'badge-fase-' . $fase_num;
                                    ?>
                                    <div class="table-mobile-card">
                                        <!-- Row 1: No and Title -->
                                        <div class="table-mobile-row">
                                            <div class="table-mobile-label">No</div>
                                            <div class="table-mobile-value"><?php echo $no++; ?></div>
                                        </div>
                                        
                                        <!-- Row 2: Masalah -->
                                        <div class="table-mobile-row">
                                            <div class="table-mobile-label">Masalah</div>
                                            <div class="table-mobile-value fw-medium text-end">
                                                <?php echo htmlspecialchars($sub['title']); ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Row 3: Fase -->
                                        <div class="table-mobile-row">
                                            <div class="table-mobile-label">Fase</div>
                                            <div class="table-mobile-value">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="badge-fase <?php echo $fase_badge_class; ?> me-2">
                                                        <?php echo $sub['fase']; ?>
                                                    </span>
                                                    <span class="small text-end">
                                                        <?php echo $sub['fase_title']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Row 4: Status -->
                                        <div class="table-mobile-row">
                                            <div class="table-mobile-label">Status</div>
                                            <div class="table-mobile-value">
                                                <?php if ($sub['status'] == 'not_submitted'): ?>
                                                    <span class="badge status-not-submitted badge-status">
                                                        <i class="fas fa-clock me-1"></i>Belum
                                                    </span>
                                                <?php elseif ($sub['status'] == 'submitted'): ?>
                                                    <span class="badge status-submitted badge-status">
                                                        <i class="fas fa-hourglass-half me-1"></i>Menunggu
                                                    </span>
                                                <?php elseif ($sub['status'] == 'graded'): ?>
                                                    <span class="badge status-graded badge-status">
                                                        <i class="fas fa-check-circle me-1"></i>Sudah
                                                    </span>
                                                <?php elseif ($sub['status'] == 'returned'): ?>
                                                    <span class="badge status-returned badge-status">
                                                        <i class="fas fa-redo me-1"></i>Dikembalikan
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Row 5: Nilai -->
                                        <div class="table-mobile-row mb-0">
                                            <div class="table-mobile-label">Nilai</div>
                                            <div class="table-mobile-value">
                                                <?php if ($sub['score'] !== null): ?>
                                                    <div class="score-display text-success fw-bold">
                                                        <?php echo $sub['score']; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <?php if ($sub['status'] == 'not_submitted'): ?>
                                                        <span class="text-muted">-</span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Belum dinilai</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Info Alert -->
                    <div class="info-alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle me-3 text-primary mt-1"></i>
                            <div>
                                <small class="text-muted">
                                    Untuk mengerjakan tugas, silakan buka halaman 
                                    <a href="view_problems.php" class="text-decoration-none fw-medium">Masalah</a>. 
                                    Setiap masalah memiliki tombol "Kerjakan Sekarang" untuk memulai pengumpulan.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="table-container">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4>Belum Ada Pengumpulan Tugas</h4>
                            <p class="mb-4">
                                Anda belum mengerjakan dan mengumpulkan solusi untuk masalah apapun.<br>
                                Mulai dengan mengerjakan masalah pertama dan lihat kemajuan Anda di sini!
                            </p>
                            <a href="view_problems.php" class="btn btn-primary">
                                <i class="fas fa-play-circle me-2"></i>Mulai Kerjakan Masalah Pertama
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="page-footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p>&copy; 2026 MATHLine | Pendidikan Matematika | Universitas Negeri Medan</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate progress circle on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressCircle = document.getElementById('progressCircle');
            if (progressCircle) {
                const progressPercent = <?php echo $progress_percent; ?>;
                const radius = 15.9155;
                const circumference = 2 * Math.PI * radius;
                
                progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
                progressCircle.style.strokeDashoffset = circumference;
                
                const offset = circumference - (progressPercent / 100 * circumference);
                
                setTimeout(() => {
                    progressCircle.style.transition = 'stroke-dashoffset 1s ease';
                    progressCircle.style.strokeDashoffset = offset;
                }, 300);
            }
            
            // Add touch feedback to cards on mobile
            const mobileCards = document.querySelectorAll('.table-mobile-card');
            mobileCards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.opacity = '0.9';
                });
                
                card.addEventListener('touchend', function() {
                    this.style.opacity = '1';
                });
            });
            
            // Handle window resize for responsive adjustments
            let resizeTimer;
            window.addEventListener('resize', function() {
                document.body.classList.add('resizing');
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    document.body.classList.remove('resizing');
                }, 250);
            });
            
            // Initialize tooltips if any
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>