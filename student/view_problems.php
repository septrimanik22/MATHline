<?php
// student/view_problems.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['user_id'];

// Filter parameters
$phase_filter = isset($_GET['phase']) ? $_GET['phase'] : '';
$pertemuan_filter = isset($_GET['pertemuan']) ? $_GET['pertemuan'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Data struktur PBL (Pertemuan -> Fase -> Masalah)
$pertemuan_structure = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'deskripsi' => 'Memahami konsep dasar persamaan garis lurus dan aplikasinya',
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'deskripsi' => 'Memahami masalah kontekstual dan mengidentifikasi kebutuhan belajar',
                'problems' => [
                    [
                        'id' => 'p1_f1_prob1',
                        'title' => 'Jalan Desa Petani',
                        'sub_bab' => 'Konsep Persamaan Garis Lurus',
                        'description' => 'Seorang petani ingin membuat pagar lurus dari titik A(2,3) ke titik B(6,7). Bagaimana cara menyatakan persamaan garis lurus dari kedua titik tersebut? Tentukan gradien dan persamaan garisnya!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'deskripsi' => 'Merencanakan dan mengorganisir proses pembelajaran untuk menyelesaikan masalah',
                'problems' => [
                    [
                        'id' => 'p1_f2_prob1',
                        'title' => 'Analisis Peta Desa',
                        'sub_bab' => 'Perencanaan Solusi',
                        'description' => 'Buat rencana pembelajaran untuk memahami konsep persamaan garis lurus berdasarkan masalah petani. Identifikasi materi yang perlu dipelajari!',
                        'points' => 6,
                        'max_points' => 6,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'deskripsi' => 'Melakukan investigasi mandiri dan kolaboratif untuk menemukan solusi',
                'problems' => [
                    [
                        'id' => 'p1_f3_prob1',
                        'title' => 'Investigasi Rumus Gradien',
                        'sub_bab' => 'Penemuan Konsep',
                        'description' => 'Lakukan investigasi tentang rumus gradien dan persamaan garis lurus. Kumpulkan data dan buat analisis!',
                        'points' => 6,
                        'max_points' => 6,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'deskripsi' => 'Mengembangkan solusi dan mempresentasikan hasil investigasi',
                'problems' => [
                    [
                        'id' => 'p1_f4_prob1',
                        'title' => 'Presentasi Solusi Petani',
                        'sub_bab' => 'Penyajian Hasil',
                        'description' => 'Kembangkan solusi lengkap untuk masalah petani dan buat presentasi hasil investigasi!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'deskripsi' => 'Menganalisis dan mengevaluasi proses serta hasil pembelajaran',
                'problems' => [
                    [
                        'id' => 'p1_f5_prob1',
                        'title' => 'Refleksi Pembelajaran',
                        'sub_bab' => 'Evaluasi Diri',
                        'description' => 'Lakukan evaluasi terhadap proses pembelajaran Fase 1-4. Berikan analisis dan saran perbaikan!',
                        'points' => 4,
                        'max_points' => 4,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ]
        ]
    ],
    
    'Pertemuan 2: Kemiringan (Gradien) Garis Lurus' => [
        'deskripsi' => 'Memahami konsep gradien dan aplikasinya dalam kehidupan nyata',
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'deskripsi' => 'Memahami masalah kontekstual tentang kemiringan',
                'problems' => [
                    [
                        'id' => 'p2_f1_prob1',
                        'title' => 'Tangga Darurat',
                        'sub_bab' => 'Konsep Gradien',
                        'description' => 'Sebuah tangga darurat dipasang dengan ujung bawah di (1,1) dan ujung atas di (5,9). Apakah tangga ini memenuhi standar keamanan? Analisis gradiennya!',
                        'points' => 8,
                        'max_points' => 8,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'deskripsi' => 'Merencanakan pembelajaran tentang gradien',
                'problems' => [
                    [
                        'id' => 'p2_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Gradien',
                        'sub_bab' => 'Strategi Belajar',
                        'description' => 'Buat rencana pembelajaran untuk memahami konsep gradien berdasarkan masalah tangga darurat!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'deskripsi' => 'Investigasi tentang jenis-jenis gradien',
                'problems' => [
                    [
                        'id' => 'p2_f3_prob1',
                        'title' => 'Investigasi Jenis Gradien',
                        'sub_bab' => 'Eksplorasi Konsep',
                        'description' => 'Lakukan investigasi tentang gradien positif, negatif, nol, dan tak terdefinisi. Berikan contoh nyata!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'deskripsi' => 'Menyajikan hasil investigasi gradien',
                'problems' => [
                    [
                        'id' => 'p2_f4_prob1',
                        'title' => 'Presentasi Aplikasi Gradien',
                        'sub_bab' => 'Penyajian Temuan',
                        'description' => 'Kembangkan presentasi tentang aplikasi gradien dalam kehidupan sehari-hari!',
                        'points' => 8,
                        'max_points' => 8,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'deskripsi' => 'Evaluasi pembelajaran gradien',
                'problems' => [
                    [
                        'id' => 'p2_f5_prob1',
                        'title' => 'Evaluasi Pemahaman Gradien',
                        'sub_bab' => 'Refleksi Pembelajaran',
                        'description' => 'Evaluasi pemahaman konsep gradien dan berikan analisis perkembangan belajar!',
                        'points' => 5,
                        'max_points' => 5,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ]
        ]
    ],
    
    'Pertemuan 3: Garis Sejajar dan Tegak Lurus' => [
        'deskripsi' => 'Memahami hubungan garis sejajar dan tegak lurus serta aplikasinya',
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'deskripsi' => 'Memahami masalah desain dengan garis sejajar dan tegak lurus',
                'problems' => [
                    [
                        'id' => 'p3_f1_prob1',
                        'title' => 'Desain Taman Sekolah',
                        'sub_bab' => 'Garis Sejajar dan Tegak Lurus',
                        'description' => 'Desain jalur paving di taman dengan syarat: jalur utama sejajar dengan jalan setapak (y = 2x + 1), jalur tegak lurus memotong di titik (2,5).',
                        'points' => 8,
                        'max_points' => 8,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'deskripsi' => 'Merencanakan pembelajaran garis sejajar dan tegak lurus',
                'problems' => [
                    [
                        'id' => 'p3_f2_prob1',
                        'title' => 'Perencanaan Pembelajaran Desain',
                        'sub_bab' => 'Strategi Desain',
                        'description' => 'Buat rencana pembelajaran untuk memahami konsep garis sejajar dan tegak lurus dalam desain!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'deskripsi' => 'Investigasi hubungan gradien garis sejajar dan tegak lurus',
                'problems' => [
                    [
                        'id' => 'p3_f3_prob1',
                        'title' => 'Investigasi Hubungan Gradien',
                        'sub_bab' => 'Penelitian Matematis',
                        'description' => 'Lakukan investigasi tentang hubungan m₁ = m₂ untuk garis sejajar dan m₁ × m₂ = -1 untuk garis tegak lurus!',
                        'points' => 7,
                        'max_points' => 7,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'deskripsi' => 'Mengembangkan desain final dan presentasi',
                'problems' => [
                    [
                        'id' => 'p3_f4_prob1',
                        'title' => 'Presentasi Desain Final',
                        'sub_bab' => 'Penyajian Desain',
                        'description' => 'Kembangkan desain taman sekolah lengkap dengan perhitungan matematis dan buat presentasi!',
                        'points' => 8,
                        'max_points' => 8,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'deskripsi' => 'Evaluasi proses desain dan pembelajaran',
                'problems' => [
                    [
                        'id' => 'p3_f5_prob1',
                        'title' => 'Evaluasi Komprehensif',
                        'sub_bab' => 'Refleksi Akhir',
                        'description' => 'Lakukan evaluasi menyeluruh terhadap proses pembelajaran dari Pertemuan 1-3!',
                        'points' => 5,
                        'max_points' => 5,
                        'due_date' => '2026-03-01'
                    ]
                ]
            ]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Masalah PBL - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-light: #e3f2fd;
            --primary-blue: #1976d2;
            --light-blue: #f0f8ff;
            --accent-blue: #64b5f6;
            --fase1-color: #1976d2;
            --fase2-color: #2e7d32;
            --fase3-color: #f57c00;
            --fase4-color: #0288d1;
            --fase5-color: #7b1fa2;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--light-blue);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            width: 100%;
        }
        
        .container-fluid {
            padding: 15px;
            max-width: 1400px;
            flex: 1;
            width: 100%;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-light), white);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-blue);
        }
        
        .pertemuan-section {
            margin-bottom: 30px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .pertemuan-header {
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .fase-section {
            margin-bottom: 25px;
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid;
        }
        
        .fase-section.fase-1 { border-left-color: var(--fase1-color); }
        .fase-section.fase-2 { border-left-color: var(--fase2-color); }
        .fase-section.fase-3 { border-left-color: var(--fase3-color); }
        .fase-section.fase-4 { border-left-color: var(--fase4-color); }
        .fase-section.fase-5 { border-left-color: var(--fase5-color); }
        
        .fase-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        
        .badge-fase {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            min-width: 60px;
            text-align: center;
        }
        
        .badge-fase-1 { background-color: var(--fase1-color); }
        .badge-fase-2 { background-color: var(--fase2-color); }
        .badge-fase-3 { background-color: var(--fase3-color); }
        .badge-fase-4 { background-color: var(--fase4-color); }
        .badge-fase-5 { background-color: var(--fase5-color); }
        
        .problem-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid #e0f2fe;
            transition: all 0.3s ease;
        }
        
        .problem-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            border-color: #1b5e20;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            min-height: 44px;
            min-width: 44px;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
        }
        
        .problem-description {
            line-height: 1.5;
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        
        .problem-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid #e0f2fe;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        
        .deadline-badge {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        
        .fase-description {
            color: #666;
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        .no-problems {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        
        /* Progress status */
        .progress-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
            border-radius: 10px;
            display: inline-block;
        }
        
        .progress-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .progress-submitted {
            background: #cce5ff;
            color: #004085;
        }
        
        .progress-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        /* Tambahan untuk poin */
        .poin-display {
            font-size: 0.8rem;
            font-weight: 600;
            background: linear-gradient(135deg, #ffd166, #ff9e00);
            color: #7a4c00;
            padding: 4px 10px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
        }
        
        /* Responsive Utilities */
        .mobile-hidden {
            display: block;
        }
        
        .desktop-hidden {
            display: none;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Touch improvements */
        button, .btn, a.btn {
            touch-action: manipulation;
        }
        
        /* Responsive Design - Tablet */
        @media (max-width: 992px) {
            .container-fluid {
                padding: 12px;
            }
            
            .page-header {
                padding: 18px;
                border-radius: 10px;
            }
            
            h1 {
                font-size: 1.6rem;
            }
            
            h3 {
                font-size: 1.3rem;
            }
            
            .pertemuan-section {
                padding: 18px;
                border-radius: 10px;
            }
            
            .problem-card {
                padding: 14px;
            }
        }
        
        /* Responsive Design - Mobile Landscape & Large Phones */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }
            
            .page-header {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .pertemuan-section {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 8px;
            }
            
            .fase-section {
                padding: 12px;
                margin-bottom: 20px;
                border-radius: 8px;
            }
            
            .problem-card {
                padding: 12px;
            }
            
            .fase-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .problem-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .text-end {
                text-align: left !important;
                margin-top: 10px;
                width: 100%;
            }
            
            .btn-success {
                width: 100%;
                margin-top: 8px;
                padding: 10px 16px;
            }
            
            /* Typography untuk mobile */
            h1 { 
                font-size: 1.4rem; 
                margin-bottom: 0.5rem;
            }
            
            h3 { 
                font-size: 1.2rem; 
                margin-bottom: 0.5rem;
            }
            
            h5 { 
                font-size: 1rem; 
                margin-bottom: 0.5rem;
            }
            
            h6 { 
                font-size: 0.95rem; 
                margin-bottom: 0.5rem;
            }
            
            .lead {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }
            
            .mobile-hidden {
                display: none;
            }
            
            .desktop-hidden {
                display: block;
            }
            
            /* Card hover effects off on mobile for better performance */
            .problem-card:hover {
                transform: none;
            }
            
            /* Problem card layout for mobile */
            .problem-card > .mb-3 > .d-flex.justify-content-between.align-items-start.mb-3 {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .problem-card > .mb-3 > .d-flex.justify-content-between.align-items-start.mb-3 > .text-end {
                align-self: flex-start;
                margin-top: 8px;
            }
            
            /* Form controls */
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
            
            /* Badge sizes */
            .badge {
                font-size: 0.75rem;
                padding: 0.35em 0.65em;
            }
            
            .badge-fase {
                font-size: 0.65rem;
                padding: 4px 8px;
            }
            
            .poin-display {
                font-size: 0.75rem;
                padding: 3px 8px;
            }
            
            .deadline-badge {
                font-size: 0.65rem;
                padding: 3px 6px;
            }
            
            /* Filter section adjustments */
            .card-body {
                padding: 15px;
            }
            
            .row.g-3 {
                margin-bottom: 10px;
            }
            
            .row.g-3 > [class*="col-"] {
                margin-bottom: 10px;
            }
        }
        
        /* Responsive Design - Small Phones */
        @media (max-width: 576px) {
            .container-fluid {
                padding: 8px;
            }
            
            .page-header {
                padding: 12px;
                border-radius: 8px;
            }
            
            .pertemuan-section {
                padding: 12px;
                border-radius: 8px;
            }
            
            .fase-section {
                padding: 10px;
                border-radius: 6px;
            }
            
            .problem-card {
                padding: 10px;
                border-radius: 6px;
            }
            
            h1 { 
                font-size: 1.3rem; 
            }
            
            h3 { 
                font-size: 1.1rem; 
            }
            
            .problem-description {
                font-size: 0.8rem;
                line-height: 1.4;
            }
            
            .btn-success {
                padding: 12px 16px;
                font-size: 0.85rem;
            }
            
            /* Reduce animations on very small devices */
            * {
                animation-duration: 0.3s !important;
            }
            
            /* Stack filter inputs vertically */
            .filter-section .row > div {
                width: 100%;
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            /* Footer adjustments */
            footer {
                padding: 15px 0 !important;
            }
            
            footer p {
                font-size: 0.8rem;
            }
        }
        
        /* Responsive Design - Very Small Phones */
        @media (max-width: 375px) {
            .container-fluid {
                padding: 6px;
            }
            
            .page-header {
                padding: 10px;
            }
            
            h1 { 
                font-size: 1.2rem; 
            }
            
            h3 { 
                font-size: 1rem; 
            }
            
            .problem-description {
                font-size: 0.75rem;
            }
            
            .btn-success {
                font-size: 0.8rem;
                padding: 10px 12px;
            }
            
            .poin-display {
                font-size: 0.7rem;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --light-blue: #1a1a2e;
                --primary-light: #16213e;
            }
            
            body {
                background-color: var(--light-blue);
                color: #e0e0e0;
            }
            
            .pertemuan-section,
            .problem-card,
            .fase-section {
                background: #2d3748;
                color: #e0e0e0;
            }
            
            .problem-description,
            .fase-description {
                color: #b0b0b0;
            }
            
            .text-muted {
                color: #a0a0a0 !important;
            }
            
            .text-primary {
                color: #64b5f6 !important;
            }
        }
        
        /* Print styles */
        @media print {
            .btn-success,
            footer,
            .filter-section {
                display: none !important;
            }
            
            .problem-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .page-header {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid fade-in">

        
        <!-- Filter Section -->
        <div class="row mb-4 filter-section">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-filter me-2"></i>Filter Masalah
                            <span class="desktop-hidden float-end">
                                <i class="fas fa-bars"></i>
                            </span>
                        </h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <form method="GET" class="mb-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" 
                                               name="search" 
                                               placeholder="Cari masalah..."
                                               value="<?php echo htmlspecialchars($search_query); ?>"
                                               aria-label="Cari masalah">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                            <span class="mobile-hidden ms-1">Cari</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <form method="GET" class="mb-0">
                                    <label class="form-label visually-hidden">Filter Pertemuan</label>
                                    <select class="form-select" name="pertemuan" onchange="this.form.submit()"
                                            aria-label="Filter berdasarkan pertemuan">
                                        <option value="">Semua Pertemuan</option>
                                        <option value="1" <?php echo $pertemuan_filter == '1' ? 'selected' : ''; ?>>Pertemuan 1</option>
                                        <option value="2" <?php echo $pertemuan_filter == '2' ? 'selected' : ''; ?>>Pertemuan 2</option>
                                        <option value="3" <?php echo $pertemuan_filter == '3' ? 'selected' : ''; ?>>Pertemuan 3</option>
                                    </select>
                                </form>
                            </div>
                            
                            <div class="col-12 col-md-4">
                                <form method="GET" class="mb-0">
                                    <label class="form-label visually-hidden">Filter Fase</label>
                                    <select class="form-select" name="phase" onchange="this.form.submit()"
                                            aria-label="Filter berdasarkan fase">
                                        <option value="">Semua Fase</option>
                                        <option value="1" <?php echo $phase_filter == '1' ? 'selected' : ''; ?>>Fase 1</option>
                                        <option value="2" <?php echo $phase_filter == '2' ? 'selected' : ''; ?>>Fase 2</option>
                                        <option value="3" <?php echo $phase_filter == '3' ? 'selected' : ''; ?>>Fase 3</option>
                                        <option value="4" <?php echo $phase_filter == '4' ? 'selected' : ''; ?>>Fase 4</option>
                                        <option value="5" <?php echo $phase_filter == '5' ? 'selected' : ''; ?>>Fase 5</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php 
        // Counter untuk pertemuan
        $pertemuan_counter = 0;
        foreach ($pertemuan_structure as $pertemuan_title => $pertemuan_data): 
            $pertemuan_counter++;
            
            // Filter berdasarkan pertemuan jika ada
            if (!empty($pertemuan_filter) && $pertemuan_counter != $pertemuan_filter) {
                continue;
            }
        ?>
        <!-- Pertemuan Section -->
        <div class="pertemuan-section fade-in">
            <div class="pertemuan-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                    <div class="mb-2 mb-md-0">
                        <h3 class="fw-bold mb-2 text-primary">
                            <i class="fas fa-book-open me-2"></i>
                            <?php echo $pertemuan_title; ?>
                        </h3>
                        <p class="text-muted mb-0">
                            <?php echo $pertemuan_data['deskripsi']; ?>
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="badge bg-primary">
                            <i class="fas fa-layer-group me-1"></i>
                            <span class="mobile-hidden">Pertemuan</span> <?php echo $pertemuan_counter; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php 
            $fase_colors = ['fase-1', 'fase-2', 'fase-3', 'fase-4', 'fase-5'];
            $fase_index = 0;
            ?>
            
            <?php foreach ($pertemuan_data['fases'] as $fase_key => $fase_data): 
                $fase_index++;
                
                // Filter berdasarkan fase jika ada
                if (!empty($phase_filter) && $fase_index != $phase_filter) {
                    continue;
                }
            ?>
            <!-- Fase dalam Pertemuan -->
            <div class="fase-section <?php echo $fase_colors[$fase_index - 1]; ?> fade-in" style="animation-delay: <?php echo $fase_index * 0.1; ?>s;">
                <div class="fase-header">
                    <div class="w-100">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-2">
                            <span class="badge-fase <?php echo 'badge-fase-' . $fase_index; ?> me-0 me-md-3 mb-2 mb-md-0">
                                <?php echo $fase_key; ?>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0"><?php echo $fase_data['title']; ?></h5>
                                <p class="fase-description mb-0 mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <?php echo $fase_data['deskripsi']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (count($fase_data['problems']) > 0): ?>
                    <?php foreach ($fase_data['problems'] as $problem_index => $problem): 
                        // Cek apakah ada submission sebelumnya
                        $submission_status = 'pending';
                        $submitted_score = 0;
                        
                        $submission_stmt = $conn->prepare("SELECT status, score FROM submissions 
                                                          WHERE student_id = ? AND problem_id = ?");
                        $submission_stmt->bind_param("is", $student_id, $problem['id']);
                        $submission_stmt->execute();
                        $submission_result = $submission_stmt->get_result();
                        
                        if ($submission_result->num_rows > 0) {
                            $submission_data = $submission_result->fetch_assoc();
                            $submission_status = $submission_data['status'];
                            $submitted_score = $submission_data['score'] ?? 0;
                        }
                    ?>
                    <div class="problem-card fade-in" style="animation-delay: <?php echo ($fase_index + $problem_index) * 0.05; ?>s;">
                        <div class="mb-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-3">
                                <div class="mb-2 mb-md-0 w-100">
                                    <h6 class="fw-bold mb-1" style="color: #1a237e;">
                                        <i class="fas fa-puzzle-piece me-2"></i>
                                        <?php echo $problem['title']; ?>
                                    </h6>
                                    
                                    <!-- Sub Bab -->
                                    <div class="mb-2">
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-book me-1"></i>
                                            <?php echo $problem['sub_bab']; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Status Submission -->
                                    <?php if ($submission_status != 'pending'): ?>
                                    <div class="mt-2">
                                        <span class="progress-badge <?php echo 'progress-' . $submission_status; ?> me-2">
                                            <?php 
                                            $status_text = [
                                                'submitted' => 'Sudah Dikirim',
                                                'graded' => 'Sudah Dinilai',
                                                'returned' => 'Dikembalikan'
                                            ];
                                            echo $status_text[$submission_status] ?? $submission_status;
                                            ?>
                                        </span>
                                        <?php if ($submitted_score > 0): ?>
                                        <span class="poin-display mt-1 mt-md-0">
                                            <i class="fas fa-check-circle me-1"></i>
                                            <?php echo $submitted_score; ?>/<?php echo $problem['max_points']; ?> poin
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-start text-md-end w-100 w-md-auto">
                                    <div class="mb-2">
                                        <span class="poin-display">
                                            <i class="fas fa-star me-1"></i>
                                            <?php echo $problem['points']; ?> poin
                                        </span>
                                    </div>
                                    <?php if ($submitted_score > 0): ?>
                                    <div class="text-success fw-bold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Nilai: <?php echo $submitted_score; ?>/<?php echo $problem['max_points']; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="problem-description">
                                <i class="fas fa-question-circle me-2 text-primary"></i>
                                <?php echo $problem['description']; ?>
                            </div>
                            
                            <div class="problem-meta">
                                <div class="deadline-badge">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Deadline: <?php echo date('d/m/Y', strtotime($problem['due_date'])); ?>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Maks: <?php echo $problem['max_points']; ?> poin
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-start text-md-end">
                            <!-- TOMBOL PERBAIKAN: Kirim problem_id -->
                            <a href="submit_work.php?problem_id=<?php echo urlencode($problem['id']); ?>" 
                               class="btn btn-success w-100 w-md-auto">
                                <?php if ($submission_status == 'pending'): ?>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <span>Kerjakan Sekarang</span>
                                <?php elseif ($submission_status == 'returned'): ?>
                                    <i class="fas fa-redo me-2"></i>
                                    <span>Perbaiki Submission</span>
                                <?php else: ?>
                                    <i class="fas fa-edit me-2"></i>
                                    <span>Lihat/Edit Submission</span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-problems">
                        <i class="fas fa-info-circle me-2"></i>
                        Belum ada masalah untuk fase ini
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($pertemuan_structure)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada masalah</h4>
            <p class="text-muted">Silakan hubungi guru untuk informasi lebih lanjut</p>
        </div>
        <?php endif; ?>
        
        <!-- Back to Top Button -->
        <button id="backToTop" class="btn btn-primary rounded-circle shadow-lg position-fixed d-none" 
                style="bottom: 20px; right: 20px; width: 50px; height: 50px; z-index: 1000;">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
    
    <!-- Footer -->
    <footer class="mt-5 py-4" style="background: linear-gradient(135deg, var(--primary-light), var(--accent-blue));">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-dark mb-0">
                        &copy; 2026 MATHLine | Pendidikan Matematika | Universitas Negeri Medan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to Top Button
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('d-none');
            } else {
                backToTopButton.classList.add('d-none');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Toggle sidebar on mobile (jika ada)
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
        
        // Animasi hover untuk problem card (hanya desktop)
        if (window.innerWidth > 768) {
            document.querySelectorAll('.problem-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                    this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        }
        
        // Highlight fase berdasarkan filter
        const urlParams = new URLSearchParams(window.location.search);
        const faseFilter = urlParams.get('phase');
        
        if (faseFilter) {
            const faseElements = document.querySelectorAll('.fase-section');
            faseElements.forEach((element, index) => {
                if (index + 1 == faseFilter) {
                    element.style.animation = 'pulse 2s infinite';
                }
            });
        }
        
        // Search highlight
        const searchQuery = "<?php echo addslashes($search_query); ?>";
        if (searchQuery) {
            // Highlight teks yang sesuai dengan pencarian
            document.querySelectorAll('.problem-card h6, .problem-description').forEach(element => {
                const html = element.innerHTML;
                const regex = new RegExp(`(${searchQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                element.innerHTML = html.replace(regex, '<span class="bg-warning px-1 rounded">$1</span>');
            });
        }
        
        // Handle orientation change
        let previousOrientation = window.orientation;
        window.addEventListener('orientationchange', function() {
            if (window.orientation !== previousOrientation) {
                // Force reflow to fix layout issues
                document.body.style.display = 'none';
                document.body.offsetHeight; // Trigger reflow
                document.body.style.display = '';
                previousOrientation = window.orientation;
            }
        });
        
        // Touch device detection
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (isTouchDevice) {
            document.body.classList.add('touch-device');
            // Add touch-specific optimizations
            document.querySelectorAll('a, button').forEach(el => {
                el.style.touchAction = 'manipulation';
            });
        }
        
        // Lazy load images if any
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img[data-src]');
            
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
        });
        
        // Mobile menu toggle for filter section
        document.querySelector('.desktop-hidden.float-end')?.addEventListener('click', function() {
            const filterSection = document.querySelector('.filter-section .card-body .row.g-3');
            if (filterSection) {
                filterSection.classList.toggle('d-none');
                this.classList.toggle('fa-bars');
                this.classList.toggle('fa-times');
            }
        });
    </script>
</body>
</html>