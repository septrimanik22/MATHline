<?php
// admin/student_detail.php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireRole(['admin', 'guru']);

$db = Database::getInstance();
$conn = $db->getConnection();

// Get student ID from URL
$student_id = $_GET['id'] ?? 0;

// Get student basic info
$stmt = $conn->prepare("
    SELECT u.id, u.full_name, s.class, s.status
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    WHERE u.id = ? AND u.role = 'siswa'
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    // Jika tidak ditemukan, gunakan data contoh
    $student = [
        'id' => $student_id,
        'full_name' => 'Ahmad Budi Santoso',
        'class' => 'VIII',
        'status' => 'active'
    ];
}

// Data struktur PBL (Pertemuan -> Fase -> Masalah)
$pertemuan_structure = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'masalah' => 'Jalan Desa Petani',
                'keterangan' => 'Seorang petani ingin membuat pagar lurus dari titik A(2,3) ke titik B(6,7). Bagaimana cara menyatakan persamaan garis lurus dari kedua titik tersebut? Tentukan gradien dan persamaan garisnya!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'masalah' => 'Analisis Peta Desa',
                'keterangan' => 'Buat rencana pembelajaran untuk memahami konsep persamaan garis lurus berdasarkan masalah petani. Identifikasi materi yang perlu dipelajari!',
                'poin' => 6,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'masalah' => 'Investigasi Rumus Gradien',
                'keterangan' => 'Lakukan investigasi tentang rumus gradien dan persamaan garis lurus. Kumpulkan data dan buat analisis!',
                'poin' => 6,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'masalah' => 'Presentasi Solusi Petani',
                'keterangan' => 'Kembangkan solusi lengkap untuk masalah petani dan buat presentasi hasil investigasi!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'masalah' => 'Refleksi Pembelajaran',
                'keterangan' => 'Lakukan evaluasi terhadap proses pembelajaran Fase 1-4. Berikan analisis dan saran perbaikan!',
                'poin' => 4,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ]
        ]
    ],
    
    'Pertemuan 2: Kemiringan (Gradien) Garis Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'masalah' => 'Tangga Darurat',
                'keterangan' => 'Sebuah tangga darurat dipasang dengan ujung bawah di (1,1) dan ujung atas di (5,9). Apakah tangga ini memenuhi standar keamanan? Analisis gradiennya!',
                'poin' => 8,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'masalah' => 'Perencanaan Pembelajaran Gradien',
                'keterangan' => 'Buat rencana pembelajaran untuk memahami konsep gradien berdasarkan masalah tangga darurat!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'masalah' => 'Investigasi Jenis Gradien',
                'keterangan' => 'Lakukan investigasi tentang gradien positif, negatif, nol, dan tak terdefinisi. Berikan contoh nyata!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'masalah' => 'Presentasi Aplikasi Gradien',
                'keterangan' => 'Kembangkan presentasi tentang aplikasi gradien dalam kehidupan sehari-hari!',
                'poin' => 8,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'masalah' => 'Evaluasi Pemahaman Gradien',
                'keterangan' => 'Evaluasi pemahaman konsep gradien dan berikan analisis perkembangan belajar!',
                'poin' => 5,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ]
        ]
    ],
    
    'Pertemuan 3: Garis Sejajar dan Tegak Lurus' => [
        'fases' => [
            'Fase 1' => [
                'title' => 'Orientasi pada Masalah',
                'masalah' => 'Desain Taman Sekolah',
                'keterangan' => 'Desain jalur paving di taman dengan syarat: jalur utama sejajar dengan jalan setapak (y = 2x + 1), jalur tegak lurus memotong di titik (2,5).',
                'poin' => 8,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 2' => [
                'title' => 'Mengorganisasikan Pembelajaran',
                'masalah' => 'Perencanaan Pembelajaran Desain',
                'keterangan' => 'Buat rencana pembelajaran untuk memahami konsep garis sejajar dan tegak lurus dalam desain!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 3' => [
                'title' => 'Membimbing Investigasi',
                'masalah' => 'Investigasi Hubungan Gradien',
                'keterangan' => 'Lakukan investigasi tentang hubungan m₁ = m₂ untuk garis sejajar dan m₁ × m₂ = -1 untuk garis tegak lurus!',
                'poin' => 7,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 4' => [
                'title' => 'Mengembangkan & Menyajikan Hasil',
                'masalah' => 'Presentasi Desain Final',
                'keterangan' => 'Kembangkan desain taman sekolah lengkap dengan perhitungan matematis dan buat presentasi!',
                'poin' => 8,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ],
            'Fase 5' => [
                'title' => 'Analisis & Evaluasi',
                'masalah' => 'Evaluasi Komprehensif',
                'keterangan' => 'Lakukan evaluasi menyeluruh terhadap proses pembelajaran dari Pertemuan 1-3!',
                'poin' => 5,
                'deadline' => '01 Maret 2026',
                'status' => 'Belum dikerjakan',
                'nilai' => '-',
                'submission_date' => '',
                'submission_text' => '',
                'submission_attachments' => []
            ]
        ]
    ]
];

// Calculate overall statistics
$total_fases = 0;
$completed_fases = 0;
$total_poin = 0;
$nilai_didapat = 0;

foreach ($pertemuan_structure as $pertemuan_data) {
    $total_fases += count($pertemuan_data['fases']);
    foreach ($pertemuan_data['fases'] as $fase_data) {
        $total_poin += $fase_data['poin'];
        if ($fase_data['status'] != 'Belum dikerjakan') {
            $completed_fases++;
            if ($fase_data['nilai'] != '-') {
                $nilai_didapat += $fase_data['nilai'];
            }
        }
    }
}

$progress_rate = $total_fases > 0 ? round(($completed_fases / $total_fases) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Detail Progress - MATHLine</title>
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
            --warning-orange: #f59e0b;
            --light-orange: #fef3c7;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --border-color: #e5e7eb;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--background-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: clamp(0.5rem, 1.5vw, 1rem);
            overflow-x: hidden;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        /* Back Button */
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
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }
        
        .btn-back:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--light-green), white);
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1.25rem, 2.5vw, 1.75rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            border-left: clamp(3px, 0.8vw, 5px) solid var(--dark-green);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
            width: 100%;
        }
        
        .page-header h1 {
            font-size: clamp(1.4rem, 3vw, 1.8rem);
            font-weight: 700;
            margin: 0;
            color: var(--dark-green);
            line-height: 1.3;
        }
        
        /* Student Information Card */
        .student-info-card {
            background: white;
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1.25rem, 2.5vw, 1.75rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            border: 1px solid var(--medium-green);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
            width: 100%;
        }
        
        .student-avatar {
            display: flex;
            align-items: center;
            gap: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }
        
        .user-avatar {
            width: clamp(55px, 10vw, 70px);
            height: clamp(55px, 10vw, 70px);
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border-radius: clamp(10px, 1.8vw, 14px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: clamp(1.4rem, 3vw, 1.8rem);
            flex-shrink: 0;
        }
        
        .student-details h4 {
            font-size: clamp(1.2rem, 2.5vw, 1.5rem);
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }
        
        .info-label {
            color: var(--text-gray);
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-weight: 600;
            font-size: clamp(0.95rem, 1.8vw, 1.1rem);
            color: var(--text-dark);
        }
        
        /* Progress Summary */
        .progress-summary {
            background: linear-gradient(135deg, var(--light-green), var(--card-bg));
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1.25rem, 2.5vw, 1.75rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            border: 2px solid var(--accent-green);
            text-align: center;
            width: 100%;
        }
        
        .progress-ring {
            width: clamp(80px, 15vw, 120px);
            height: clamp(80px, 15vw, 120px);
            border-radius: 50%;
            background: conic-gradient(var(--primary-green) <?php echo $progress_rate * 3.6; ?>deg, var(--border-color) 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto clamp(1rem, 2vw, 1.5rem);
            transition: background 1.5s ease;
        }
        
        .progress-ring-inner {
            width: 85%;
            height: 85%;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: clamp(1.2rem, 2.5vw, 1.6rem);
            color: var(--dark-green);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(150px, 100%), 1fr));
            gap: clamp(1rem, 2vw, 1.5rem);
            margin-top: clamp(1rem, 2vw, 1.5rem);
        }
        
        .stat-item {
            text-align: center;
            padding: clamp(0.75rem, 1.5vw, 1rem);
            background: white;
            border-radius: clamp(8px, 1.5vw, 10px);
            border: 1px solid var(--light-green);
        }
        
        .stat-number {
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--text-gray);
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin: 0;
        }
        
        /* Pertemuan Cards */
        .pertemuan-card {
            background: white;
            border-radius: clamp(12px, 2vw, 15px);
            padding: clamp(1.25rem, 2.5vw, 1.75rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            border: 1px solid var(--medium-green);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .pertemuan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        }
        
        .pertemuan-header {
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            padding-bottom: clamp(0.75rem, 1.5vw, 1rem);
            border-bottom: 2px solid var(--light-green);
        }
        
        .pertemuan-header h4 {
            font-size: clamp(1.1rem, 2.2vw, 1.3rem);
            font-weight: 700;
            color: var(--dark-green);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            line-height: 1.4;
        }
        
        /* Fase Items */
        .fase-item {
            background: var(--card-bg);
            border-radius: clamp(10px, 1.8vw, 12px);
            padding: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            border-left: 4px solid var(--dark-green);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .timeline-marker {
            width: clamp(10px, 2vw, 14px);
            height: clamp(10px, 2vw, 14px);
            border-radius: 50%;
            background: var(--primary-green);
            border: 2px solid white;
            box-shadow: 0 0 0 3px var(--light-green);
            position: absolute;
            left: -7px;
            top: 20px;
        }
        
        .fase-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            padding-bottom: clamp(0.5rem, 1vw, 0.75rem);
            border-bottom: 1px solid var(--medium-green);
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .fase-info {
            flex: 1;
            min-width: 250px;
        }
        
        .fase-title-row {
            display: flex;
            align-items: center;
            gap: clamp(0.5rem, 1vw, 0.75rem);
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
        }
        
        .badge-fase {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            padding: clamp(4px, 0.8vw, 5px) clamp(12px, 2vw, 15px);
            border-radius: 20px;
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            font-weight: 600;
            white-space: nowrap;
        }
        
        .fase-title {
            font-size: clamp(1rem, 1.8vw, 1.1rem);
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.3;
        }
        
        .deadline-text {
            color: var(--text-gray);
            font-size: clamp(0.75rem, 1.4vw, 0.85rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Badge Status */
        .badge-status {
            font-size: clamp(0.75rem, 1.4vw, 0.85rem);
            padding: clamp(3px, 0.8vw, 4px) clamp(10px, 2vw, 12px);
            border-radius: 15px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .status-belum {
            background: var(--light-orange);
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .status-menunggu {
            background: linear-gradient(135deg, var(--light-orange), #fde68a);
            color: #92400e;
            border: 1px solid var(--warning-orange);
        }
        
        .status-selesai {
            background: linear-gradient(135deg, var(--light-green), var(--medium-green));
            color: #065f46;
            border: 1px solid var(--accent-green);
        }
        
        /* Masalah Detail */
        .masalah-detail {
            background: white;
            border-radius: clamp(8px, 1.5vw, 10px);
            padding: clamp(1rem, 1.8vw, 1.25rem);
            margin-top: clamp(0.75rem, 1.5vw, 1rem);
            border: 1px solid var(--medium-green);
        }
        
        .masalah-content h6 {
            font-size: clamp(0.95rem, 1.8vw, 1.05rem);
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .masalah-content p {
            color: var(--text-gray);
            font-size: clamp(0.85rem, 1.5vw, 0.9rem);
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .fase-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .poin-badge {
            background: linear-gradient(135deg, var(--warning-orange), #f59e0b);
            color: #92400e;
            padding: clamp(3px, 0.8vw, 4px) clamp(8px, 1.5vw, 10px);
            border-radius: 12px;
            font-size: clamp(0.75rem, 1.4vw, 0.85rem);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .nilai-display {
            font-size: clamp(1rem, 2vw, 1.2rem);
            font-weight: bold;
            color: var(--dark-green);
            text-align: right;
        }
        
        /* Buttons */
        .btn-group-fase {
            display: flex;
            gap: clamp(0.5rem, 1vw, 0.75rem);
            flex-wrap: wrap;
            justify-content: flex-end;
            width: 100%;
        }
        
        .btn-detail {
            background: white;
            border: 2px solid var(--primary-green);
            color: var(--dark-green);
            padding: clamp(6px, 1.2vw, 8px) clamp(12px, 2vw, 16px);
            border-radius: clamp(6px, 1.2vw, 8px);
            font-weight: 500;
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 38px;
            white-space: nowrap;
        }
        
        .btn-detail:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(16, 185, 129, 0.2);
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: clamp(12px, 2vw, 15px);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: clamp(1rem, 2vw, 1.25rem);
        }
        
        .modal-body {
            padding: clamp(1rem, 2vw, 1.25rem);
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: clamp(0.75rem, 1.5vw, 1rem);
        }
        
        .submission-content {
            max-height: 300px;
            overflow-y: auto;
            padding: clamp(0.75rem, 1.5vw, 1rem);
            background-color: #f9f9f9;
            border-radius: clamp(6px, 1.2vw, 8px);
            margin: 10px 0;
            white-space: pre-wrap;
            font-family: inherit;
            font-size: clamp(0.85rem, 1.5vw, 0.95rem);
            border: 1px solid var(--border-color);
            line-height: 1.5;
        }
        
        /* No Data State */
        .no-data {
            text-align: center;
            padding: clamp(2rem, 4vw, 3rem);
            width: 100%;
        }
        
        .no-data i {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            color: var(--text-gray);
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .main-container {
                padding: 0 clamp(0.5rem, 1.5vw, 1rem);
            }
        }
        
        @media (max-width: 992px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .student-info-card {
                padding: 1.5rem;
            }
            
            .progress-summary {
                padding: 1.5rem;
            }
            
            .pertemuan-card {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 0.75rem;
            }
            
            .page-header {
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .student-info-card {
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }
            
            .student-avatar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .user-avatar {
                width: 65px;
                height: 65px;
                font-size: 1.6rem;
            }
            
            .progress-summary {
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }
            
            .progress-ring {
                width: 100px;
                height: 100px;
                margin-bottom: 1.25rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .pertemuan-card {
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }
            
            .pertemuan-header h4 {
                font-size: 1.2rem;
            }
            
            .fase-item {
                padding: 1.125rem;
                border-radius: 10px;
            }
            
            .fase-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .fase-info {
                min-width: 100%;
            }
            
            .fase-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }
            
            .btn-group-fase {
                justify-content: stretch;
            }
            
            .btn-detail {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .page-header {
                padding: 1rem;
                border-radius: 10px;
                margin-bottom: 1.25rem;
            }
            
            .page-header h1 {
                font-size: 1.3rem;
            }
            
            .student-info-card {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .student-details h4 {
                font-size: 1.3rem;
            }
            
            .progress-summary {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .progress-ring {
                width: 90px;
                height: 90px;
                margin-bottom: 1rem;
            }
            
            .progress-ring-inner {
                font-size: 1.3rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .stat-item {
                padding: 0.875rem;
            }
            
            .stat-number {
                font-size: 1.4rem;
            }
            
            .pertemuan-card {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .pertemuan-header h4 {
                font-size: 1.1rem;
            }
            
            .fase-item {
                padding: 1rem;
                border-radius: 8px;
            }
            
            .fase-title {
                font-size: 1rem;
            }
            
            .masalah-detail {
                padding: 0.875rem;
            }
            
            .btn-detail {
                min-height: 36px;
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 375px) {
            body {
                padding: 0.5rem;
            }
            
            .page-header {
                padding: 0.875rem;
                border-radius: 8px;
            }
            
            .page-header h1 {
                font-size: 1.2rem;
            }
            
            .student-info-card {
                padding: 0.875rem;
                border-radius: 8px;
            }
            
            .user-avatar {
                width: 55px;
                height: 55px;
                font-size: 1.4rem;
            }
            
            .student-details h4 {
                font-size: 1.2rem;
            }
            
            .info-label, .info-value {
                font-size: 0.9rem;
            }
            
            .fase-title-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .pertemuan-card:hover {
                transform: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }
            
            .btn-back:hover,
            .btn-detail:hover {
                transform: none;
                box-shadow: none;
            }
            
            .btn-back:active,
            .btn-detail:active {
                opacity: 0.8;
                transform: scale(0.98);
            }
            
            /* Larger touch targets */
            .btn-detail {
                min-height: 44px;
            }
            
            .badge-status {
                min-height: 30px;
                display: inline-flex;
                align-items: center;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .page-header {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .student-info-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .progress-summary {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .pertemuan-card {
                margin-bottom: 1rem;
            }
            
            .fase-item {
                margin-bottom: 0.75rem;
                padding: 0.875rem;
            }
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding: 0.5rem;
            }
            
            .page-header {
                background: white !important;
                color: black !important;
                border: 1px solid #000;
                box-shadow: none;
            }
            
            .student-info-card,
            .progress-summary,
            .pertemuan-card {
                box-shadow: none;
                border: 1px solid #000;
                break-inside: avoid;
            }
            
            .btn-back,
            .btn-detail {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Back Button -->
        <div class="d-flex justify-content-center">
            <a href="progress_tracking.php" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>
                <span>Kembali ke Progress Siswa</span>
            </a>
        </div>
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-12">
                    <h1>
                        <i class="fas fa-chart-bar me-2"></i>Detail Progress Siswa
                    </h1>
                </div>
            </div>
        </div>
        
        <!-- Student Information -->
        <div class="student-info-card">
            <div class="student-avatar">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                </div>
                <div class="student-details">
                    <h4><?php echo htmlspecialchars($student['full_name']); ?></h4>
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <div class="info-label">Kelas</div>
                            <div class="info-value"><?php echo htmlspecialchars($student['class']); ?></div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="info-label">Status</div>
                            <div class="info-value"><?php echo htmlspecialchars($student['status']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Progress Summary -->
        <div class="progress-summary">
            <div class="progress-ring">
                <div class="progress-ring-inner">
                    <?php echo $progress_rate; ?>%
                </div>
            </div>
            <div class="info-label mb-3">Progress Keseluruhan</div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $completed_fases; ?>/<?php echo $total_fases; ?></div>
                    <div class="stat-label">Fase Diselesaikan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $nilai_didapat; ?>/<?php echo $total_poin; ?></div>
                    <div class="stat-label">Poin Didapat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $total_fases; ?></div>
                    <div class="stat-label">Total Fase</div>
                </div>
            </div>
        </div>
        
        <!-- Fase Progress -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($pertemuan_structure)): ?>
                <div class="pertemuan-card">
                    <div class="no-data">
                        <i class="fas fa-calendar-times"></i>
                        <h5 class="text-muted mb-2">Belum ada jadwal pertemuan</h5>
                        <p class="text-muted">Silakan hubungi administrator untuk informasi lebih lanjut</p>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($pertemuan_structure as $pertemuan_title => $pertemuan_data): ?>
                    <div class="pertemuan-card">
                        <div class="pertemuan-header">
                            <h4>
                                <i class="fas fa-book-open me-2"></i>
                                <?php echo $pertemuan_title; ?>
                            </h4>
                        </div>
                        
                        <!-- Fase-fase dalam Pertemuan -->
                        <?php foreach ($pertemuan_data['fases'] as $fase_key => $fase_data): ?>
                        <div class="fase-item">
                            <div class="timeline-marker"></div>
                            
                            <div class="fase-header">
                                <div class="fase-info">
                                    <div class="fase-title-row">
                                        <span class="badge-fase">
                                            <?php echo $fase_key; ?>
                                        </span>
                                        <h5 class="fase-title"><?php echo $fase_data['title']; ?></h5>
                                    </div>
                                    <div class="deadline-text">
                                        <i class="fas fa-clock"></i>
                                        <span>Deadline: <?php echo $fase_data['deadline']; ?></span>
                                    </div>
                                </div>
                                <span class="badge-status status-<?php echo strtolower(explode(' ', $fase_data['status'])[0]); ?>">
                                    <?php echo $fase_data['status']; ?>
                                </span>
                            </div>
                            
                            <!-- Detail Masalah -->
                            <div class="masalah-detail">
                                <div class="masalah-content">
                                    <h6><?php echo $fase_data['masalah']; ?></h6>
                                    <p><?php echo $fase_data['keterangan']; ?></p>
                                </div>
                                
                                <div class="fase-footer">
                                    <div class="poin-badge">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo $fase_data['poin']; ?> Poin</span>
                                    </div>
                                    
                                    <?php if ($fase_data['nilai'] != '-'): ?>
                                    <div class="nilai-display">
                                        <?php echo $fase_data['nilai']; ?>/<?php echo $fase_data['poin']; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="btn-group-fase">
                                        <button class="btn-detail" onclick="lihatDetailSubmission(
                                            '<?php echo $student_id; ?>',
                                            '<?php echo $fase_key; ?>',
                                            `<?php echo addslashes($fase_data['submission_text']); ?>`,
                                            <?php echo $fase_data['poin']; ?>,
                                            '<?php echo $fase_data['nilai']; ?>'
                                        )">
                                            <i class="fas fa-eye me-1"></i>
                                            <span>Lihat Detail</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk Lihat Detail Submission -->
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submissionModalLabel">Jawaban Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Jawaban Siswa -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">Jawaban Siswa:</h6>
                        <div class="submission-content" id="modalSubmissionContent">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Form untuk memberikan skor -->
                    <div class="border-top pt-4">
                        <h6 class="fw-bold mb-3">Beri Nilai:</h6>
                        <form id="scoreForm">
                            <input type="hidden" id="modalStudentId">
                            <input type="hidden" id="modalFaseKey">
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="scoreValue" class="form-label">Nilai (0-<span id="maxScoreDisplay">0</span> poin)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="scoreValue" name="score" min="0" required>
                                        <span class="input-group-text">/ <span id="maxScoreText">0</span></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="scoreFeedback" class="form-label">Feedback</label>
                                <textarea class="form-control" id="scoreFeedback" name="feedback" rows="3" placeholder="Berikan masukan dan saran perbaikan..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="simpanNilai()">
                        <i class="fas fa-save me-1"></i>Simpan Nilai
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function untuk melihat detail submission
        function lihatDetailSubmission(studentId, faseKey, submissionText, maxScore, currentScore) {
            // Set modal content
            document.getElementById('modalStudentId').value = studentId;
            document.getElementById('modalFaseKey').value = faseKey;
            document.getElementById('maxScoreDisplay').textContent = maxScore;
            document.getElementById('maxScoreText').textContent = maxScore;
            
            // Set submission content
            const submissionContent = document.getElementById('modalSubmissionContent');
            if (submissionText && submissionText.trim() !== '') {
                submissionContent.textContent = submissionText;
            } else {
                submissionContent.textContent = 'Siswa belum mengirimkan jawaban untuk tugas ini.';
                submissionContent.style.fontStyle = 'italic';
                submissionContent.style.color = '#666';
            }
            
            // Set current score if exists
            if (currentScore && currentScore !== '-') {
                document.getElementById('scoreValue').value = currentScore;
            } else {
                document.getElementById('scoreValue').value = '';
            }
            
            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('submissionModal'));
            modal.show();
        }
        
        // Function untuk menyimpan nilai
        function simpanNilai() {
            const studentId = document.getElementById('modalStudentId').value;
            const faseKey = document.getElementById('modalFaseKey').value;
            const score = document.getElementById('scoreValue').value;
            const maxScore = parseInt(document.getElementById('maxScoreText').textContent);
            const feedback = document.getElementById('scoreFeedback').value;
            
            // Validasi
            if (score === '' || score < 0 || score > maxScore) {
                alert(`Masukkan nilai yang valid! (0-${maxScore})`);
                return;
            }
            
            // Tampilkan loading
            const saveBtn = document.querySelector('#submissionModal .btn-primary');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            saveBtn.disabled = true;
            
            // Simulasi penyimpanan ke server
            setTimeout(() => {
                console.log('Menyimpan nilai:', {
                    studentId,
                    faseKey,
                    score,
                    maxScore,
                    feedback
                });
                
                // Tampilkan pesan sukses
                alert(`Nilai ${score}/${maxScore} berhasil disimpan!`);
                
                // Restore button
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('submissionModal'));
                modal.hide();
                
                // Reload halaman untuk memperbarui data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }, 1500);
        }
        
        // Animate progress ring on page load
        document.addEventListener('DOMContentLoaded', function() {
            const progressRing = document.querySelector('.progress-ring');
            const computedStyle = getComputedStyle(progressRing);
            const conicGradient = computedStyle.backgroundImage;
            const percentage = conicGradient.match(/(\d+(\.\d+)?)deg/)[1];
            
            // Animate from 0 to the actual percentage
            progressRing.style.background = 'conic-gradient(#e5e7eb 0deg, #e5e7eb 0deg)';
            
            setTimeout(() => {
                progressRing.style.background = `conic-gradient(var(--primary-green) ${percentage}deg, #e5e7eb 0deg)`;
            }, 300);
        });
        
        // Handle responsive adjustments
        function adjustLayout() {
            const isMobile = window.innerWidth < 768;
            const isLandscape = window.innerWidth > window.innerHeight;
            
            if (isMobile) {
                // Optimize for mobile
                document.querySelectorAll('.pertemuan-card').forEach(card => {
                    card.style.transition = 'transform 0.2s ease';
                });
                
                if (isLandscape) {
                    // Landscape mode optimizations
                    document.querySelectorAll('.fase-item').forEach(item => {
                        item.style.padding = '0.875rem';
                    });
                }
            }
        }
        
        // Initial adjustment
        adjustLayout();
        
        // Adjust on resize
        window.addEventListener('resize', function() {
            adjustLayout();
        });
        
        // Handle modal positioning for mobile
        const submissionModal = document.getElementById('submissionModal');
        if (submissionModal) {
            submissionModal.addEventListener('show.bs.modal', function () {
                if (window.innerWidth < 768) {
                    this.style.paddingLeft = '0';
                    this.style.paddingRight = '0';
                }
            });
        }
    </script>
</body>
</html>