<?php
// student/submit_work.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$database = Database::getInstance();
$conn = $database->getConnection();

$student_id = $_SESSION['user_id'];
$problem_id = isset($_GET['problem_id']) ? trim($_GET['problem_id']) : '';
$success = '';
$error = '';

// Data struktur PBL (sesuai dengan view_problems.php yang sudah diperbarui)
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

// Fungsi untuk mencari problem berdasarkan ID
function findProblemById($id, $structure) {
    foreach ($structure as $pertemuan_title => $pertemuan_data) {
        foreach ($pertemuan_data['fases'] as $fase_key => $fase_data) {
            foreach ($fase_data['problems'] as $problem) {
                if (isset($problem['id']) && $problem['id'] == $id) {
                    // Tambahkan informasi fase dan pertemuan
                    $problem['phase'] = $fase_key;
                    $problem['phase_title'] = $fase_data['title'];
                    $problem['pertemuan'] = $pertemuan_title;
                    return $problem;
                }
            }
        }
    }
    return null;
}

// Get problem details
$problem = null;
$previous_submission = null;
$previous_files = [];

if (!empty($problem_id)) {
    // Cari problem dari array struktur
    $problem = findProblemById($problem_id, $pertemuan_structure);
    
    if (!$problem) {
        header('Location: view_problems.php?error=' . urlencode('Masalah tidak ditemukan!'));
        exit();
    }
    
    // Check for previous submission
    $sub_stmt = $conn->prepare("SELECT * FROM submissions 
                               WHERE student_id = ? AND problem_id = ? 
                               ORDER BY submitted_at DESC LIMIT 1");
    $sub_stmt->bind_param("is", $student_id, $problem_id);
    $sub_stmt->execute();
    $sub_result = $sub_stmt->get_result();
    
    if ($sub_result->num_rows > 0) {
        $previous_submission = $sub_result->fetch_assoc();
        
        // Get previous uploaded files
        if (!empty($previous_submission['file_path'])) {
            $previous_files = json_decode($previous_submission['file_path'], true) ?? [$previous_submission['file_path']];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_work'])) {
    $answer = trim($_POST['answer']);
    $problem_id = $_POST['problem_id'];
    
    if (empty($answer)) {
        $error = "Jawaban tidak boleh kosong!";
    } else {
        // File upload handling - MULTIPLE FILES
        $uploaded_files = [];
        
        if (isset($_FILES['submission_files']) && !empty($_FILES['submission_files']['name'][0])) {
            $upload_dir = '../assets/uploads/submissions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'txt'];
            $max_size = 10 * 1024 * 1024; // 10MB
            $max_files = 5;
            
            $file_count = count($_FILES['submission_files']['name']);
            if ($file_count > $max_files) {
                $error = "Maksimal $max_files file yang dapat diupload.";
            } else {
                for ($i = 0; $i < $file_count; $i++) {
                    $file_name = $_FILES['submission_files']['name'][$i];
                    $file_tmp = $_FILES['submission_files']['tmp_name'][$i];
                    $file_size = $_FILES['submission_files']['size'][$i];
                    $file_error = $_FILES['submission_files']['error'][$i];
                    
                    if ($file_error === UPLOAD_ERR_OK) {
                        if ($file_size > $max_size) {
                            $error = "File $file_name terlalu besar! Maksimal 10MB per file.";
                            break;
                        }
                        
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        if (!in_array($file_ext, $allowed_types)) {
                            $error = "Format file $file_name tidak didukung! Gunakan: PDF, DOC, PPT, JPG, PNG.";
                            break;
                        }
                        
                        // Generate unique filename
                        $new_filename = time() . '_' . $student_id . '_' . uniqid() . '.' . $file_ext;
                        $target_file = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $uploaded_files[] = 'assets/uploads/submissions/' . $new_filename;
                        } else {
                            $error = "Gagal mengupload file $file_name.";
                            break;
                        }
                    }
                }
            }
        }
        
        // Keep old files if no new files uploaded
        if (empty($uploaded_files) && isset($_POST['keep_existing_files']) && $_POST['keep_existing_files'] == '1') {
            $uploaded_files = $previous_files;
        }
        
        if (empty($error)) {
            // Prepare file paths for database
            $file_path_json = !empty($uploaded_files) ? json_encode($uploaded_files) : null;
            
            // Check if this is a resubmission
            if ($previous_submission) {
                // Update existing submission
                $stmt = $conn->prepare("UPDATE submissions SET answer = ?, file_path = ?, 
                                       status = 'submitted', submitted_at = NOW() 
                                       WHERE id = ?");
                $stmt->bind_param("ssi", $answer, $file_path_json, $previous_submission['id']);
                $action = 'update';
            } else {
                // Create new submission
                $stmt = $conn->prepare("INSERT INTO submissions (student_id, problem_id, answer, file_path, status) 
                                       VALUES (?, ?, ?, ?, 'submitted')");
                $stmt->bind_param("isss", $student_id, $problem_id, $answer, $file_path_json);
                $action = 'insert';
            }
            
            if ($stmt->execute()) {
                // Log activity
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, description) VALUES (?, ?, ?)");
                
                $log_action = $action . '_submission';
                $log_description = $action == 'insert' ? 
                    "Mengumpulkan tugas: " . $problem['title'] : 
                    "Mengirim ulang tugas: " . $problem['title'];
                
                $log_stmt->bind_param("iss", $student_id, $log_action, $log_description);
                $log_stmt->execute();
                
                $success = "Tugas berhasil dikumpulkan!" . 
                          (!empty($uploaded_files) ? " " . count($uploaded_files) . " file berhasil diupload." : "");
                
                // Clear form
                $_POST['answer'] = '';
                
                // Update previous submission data
                if ($action == 'insert') {
                    // Get the new submission
                    $new_sub_stmt = $conn->prepare("SELECT * FROM submissions 
                                                   WHERE student_id = ? AND problem_id = ? 
                                                   ORDER BY submitted_at DESC LIMIT 1");
                    $new_sub_stmt->bind_param("is", $student_id, $problem_id);
                    $new_sub_stmt->execute();
                    $new_sub_result = $new_sub_stmt->get_result();
                    
                    if ($new_sub_result->num_rows > 0) {
                        $previous_submission = $new_sub_result->fetch_assoc();
                        $previous_files = $uploaded_files;
                    }
                } else {
                    // Update existing submission data
                    $previous_submission['answer'] = $answer;
                    $previous_submission['file_path'] = $file_path_json;
                    $previous_submission['submitted_at'] = date('Y-m-d H:i:s');
                    $previous_files = $uploaded_files;
                }
            } else {
                $error = "Gagal mengumpulkan tugas: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Kumpulkan Tugas - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-light: #e3f2fd;
            --primary-blue: #1976d2;
            --light-blue: #f0f8ff;
            --accent-blue: #64b5f6;
            --mobile-breakpoint: 768px;
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
            -webkit-text-size-adjust: 100%;
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
            margin-bottom: 1.25rem;
        }
        
        .page-header h4 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #1e293b;
            line-height: 1.3;
        }
        
        .page-header p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0;
        }
        
        @media (max-width: 576px) {
            .page-header h4 {
                font-size: 1.2rem;
            }
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
            overflow: hidden;
            background: white;
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }
        
        .card-header h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        @media (max-width: 576px) {
            .card-header {
                padding: 0.875rem 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        /* Problem Description */
        .problem-description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 1.5rem;
            white-space: normal;
            word-wrap: break-word;
        }
        
        /* Badges */
        .badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        /* Form Elements */
        .form-label {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 0.875rem;
            font-size: 0.95rem;
            line-height: 1.5;
            min-height: 150px;
            resize: vertical;
            transition: all 0.2s;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
            outline: none;
        }
        
        /* File Upload Area */
        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
            margin-bottom: 1rem;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-blue);
            background: #e3f2fd;
        }
        
        .file-upload-area.dragover {
            border-color: var(--primary-blue);
            background: #bbdefb;
            transform: scale(1.02);
        }
        
        .file-upload-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }
        
        /* File Preview */
        .file-preview-container {
            margin-top: 1rem;
        }
        
        .file-preview-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .file-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .file-pdf { background: #ffebee; color: #d32f2f; }
        .file-image { background: #f3e5f5; color: #7b1fa2; }
        .file-presentation { background: #e8f5e8; color: #388e3c; }
        .file-document { background: #e3f2fd; color: #1976d2; }
        .file-other { background: #f5f5f5; color: #616161; }
        
        .file-info {
            flex: 1;
            min-width: 0;
        }
        
        .file-name {
            font-weight: 500;
            font-size: 0.9rem;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.25rem;
        }
        
        .file-size {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        .file-remove {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }
        
        /* Uploaded Files */
        .uploaded-files {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .uploaded-file-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            border: 1px solid #e2e8f0;
        }
        
        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s;
            min-height: 50px;
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        /* Back Button */
        .btn-back {
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            background: white;
            transition: all 0.2s;
        }
        
        /* Word Count */
        .word-count {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.5rem;
        }
        
        /* File Type Hints */
        .file-type-hints {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            justify-content: center;
        }
        
        .file-type-badge {
            padding: 0.25rem 0.5rem;
            background: #e3f2fd;
            border-radius: 6px;
            font-size: 0.75rem;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        /* Responsive Grid */
        @media (min-width: 768px) {
            .col-lg-4 {
                width: 33.333%;
            }
            
            .col-lg-8 {
                width: 66.667%;
            }
        }
        
        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
                color: #e2e8f0;
            }
            
            .card {
                background: #1e293b;
            }
            
            .card-header {
                background: linear-gradient(135deg, #334155, #475569);
            }
            
            .form-control {
                background: #334155;
                border-color: #475569;
                color: #e2e8f0;
            }
            
            .file-upload-area {
                background: #334155;
                border-color: #475569;
            }
            
            .file-preview-item {
                background: #334155;
                border-color: #475569;
            }
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
                    <h4>
                        <?php echo $problem ? 'Kumpulkan Tugas' : 'Pilih Tugas'; ?>
                    </h4>
                    <p>
                        <?php echo $problem ? 'Kerjakan dan kumpulkan tugas Anda' : 'Silakan pilih tugas dari halaman masalah PBL'; ?>
                    </p>
                </div>
                <div>
                    <?php if ($problem): ?>
                        <a href="view_problems.php" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-check-circle me-2 mt-1"></i>
                    <div>
                        <strong>Berhasil!</strong> <?php echo $success; ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Error!</strong> <?php echo $error; ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!$problem): ?>
            <!-- No Problem Selected -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-3">Belum ada tugas yang dipilih</h5>
                            <p class="text-muted mb-4">Silakan pilih tugas dari halaman masalah PBL terlebih dahulu</p>
                            <a href="view_problems.php" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>Pilih Tugas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Problem and Submission Form -->
            <div class="row">
                <!-- Problem Details -->
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-info-circle text-primary"></i>
                                Detail Masalah
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Badges -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-info">
                                    <i class="fas fa-layer-group"></i>
                                    <?php echo htmlspecialchars($problem['phase'] ?? 'Fase'); ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-bookmark"></i>
                                    <?php echo htmlspecialchars($problem['sub_bab']); ?>
                                </span>
                            </div>
                            
                            <!-- Problem Title -->
                            <h5 class="mb-3"><?php echo htmlspecialchars($problem['title']); ?></h5>
                            
                            <!-- Problem Description -->
                            <div class="problem-description">
                                <?php echo nl2br(htmlspecialchars($problem['description'])); ?>
                            </div>
                            
                            <!-- Points and Due Date -->
                            <div class="row">
                                <?php if ($problem['points']): ?>
                                <div class="col-6">
                                    <div class="text-muted small">Nilai Maksimal</div>
                                    <div class="fw-bold text-success"><?php echo $problem['points']; ?> poin</div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($problem['due_date']): ?>
                                <div class="col-6">
                                    <div class="text-muted small">Batas Waktu</div>
                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($problem['due_date'])); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Previous Submission -->
                    <?php if ($previous_submission): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-history text-secondary"></i>
                                Pengumpulan Sebelumnya
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">Dikirim pada</div>
                                <div class="fw-bold">
                                    <?php echo date('d/m/Y H:i', strtotime($previous_submission['submitted_at'])); ?>
                                </div>
                            </div>
                            
                            <?php if ($previous_submission['status'] == 'graded'): ?>
                                <div class="alert alert-success mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Sudah Dinilai</strong>
                                        </div>
                                        <div class="fw-bold">
                                            <?php echo $previous_submission['score']; ?>/<?php echo $problem['max_points']; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($previous_submission['status'] == 'returned'): ?>
                                <div class="alert alert-warning mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-redo me-2"></i>
                                        <div>
                                            <strong>Dikembalikan</strong>
                                            <div class="small mt-1">Silakan perbaiki dan kirim ulang</div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Menunggu Penilaian</strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($previous_submission['feedback'])): ?>
                                <div class="mt-3">
                                    <div class="text-muted small mb-2">Feedback dari Guru:</div>
                                    <div class="border rounded p-3 bg-light small">
                                        <?php echo nl2br(htmlspecialchars($previous_submission['feedback'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Previous Files -->
                            <?php if (!empty($previous_files)): ?>
                                <div class="mt-3">
                                    <div class="text-muted small mb-2">File Sebelumnya:</div>
                                    <div class="uploaded-files">
                                        <?php foreach ($previous_files as $file): 
                                            $filename = basename($file);
                                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                            $icon_class = '';
                                            
                                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                $icon_class = 'file-image';
                                                $icon = 'fa-image';
                                            } elseif ($extension == 'pdf') {
                                                $icon_class = 'file-pdf';
                                                $icon = 'fa-file-pdf';
                                            } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                $icon_class = 'file-presentation';
                                                $icon = 'fa-file-powerpoint';
                                            } elseif (in_array($extension, ['doc', 'docx', 'txt'])) {
                                                $icon_class = 'file-document';
                                                $icon = 'fa-file-word';
                                            } else {
                                                $icon_class = 'file-other';
                                                $icon = 'fa-file';
                                            }
                                        ?>
                                        <a href="../<?php echo htmlspecialchars($file); ?>" 
                                           target="_blank" 
                                           class="uploaded-file-item text-decoration-none">
                                            <div class="file-icon <?php echo $icon_class; ?>">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </div>
                                            <div class="file-info">
                                                <div class="file-name"><?php echo htmlspecialchars($filename); ?></div>
                                                <div class="file-size">
                                                    <i class="fas fa-file me-1"></i>
                                                    <?php echo strtoupper($extension); ?>
                                                </div>
                                            </div>
                                            <i class="fas fa-external-link-alt text-primary"></i>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="keep_existing_files" value="1">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Submission Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-file-upload text-success"></i>
                                Form Pengumpulan
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" id="submissionForm">
                                <input type="hidden" name="problem_id" value="<?php echo htmlspecialchars($problem_id); ?>">
                                <?php if (!empty($previous_files)): ?>
                                <input type="hidden" name="keep_existing_files" value="1" id="keepExistingFiles">
                                <?php endif; ?>
                                
                                <!-- Answer Textarea -->
                                <div class="mb-4">
                                    <label for="answer" class="form-label">
                                        <i class="fas fa-edit text-primary"></i>
                                        Jawaban Anda <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="answer" name="answer" rows="8" 
                                              placeholder="Tulis jawaban Anda di sini. Minimal 50 karakter."
                                              required><?php echo isset($_POST['answer']) ? htmlspecialchars($_POST['answer']) : ($previous_submission['answer'] ?? ''); ?></textarea>
                                    <div class="word-count">
                                        <span id="charCount">0 karakter</span>
                                        <span id="wordCountText">0 kata</span>
                                    </div>
                                </div>
                                
                                <!-- File Upload -->
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-paperclip"></i>
                                        Lampiran File
                                    </label>
                                    
                                    <div class="file-upload-area" id="fileUploadArea">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h6 class="mb-2">Upload File Tugas</h6>
                                        <p class="text-muted mb-3">Seret file ke sini atau klik untuk memilih</p>
                                        
                                        <div class="file-type-hints">
                                            <span class="file-type-badge">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </span>
                                            <span class="file-type-badge">
                                                <i class="fas fa-file-word"></i> DOC/DOCX
                                            </span>
                                            <span class="file-type-badge">
                                                <i class="fas fa-file-powerpoint"></i> PPT/PPTX
                                            </span>
                                            <span class="file-type-badge">
                                                <i class="fas fa-image"></i> JPG/PNG
                                            </span>
                                            <span class="file-type-badge">
                                                <i class="fas fa-file-alt"></i> TXT
                                            </span>
                                        </div>
                                        
                                        <small class="text-muted d-block mt-2">
                                            Maksimal 5 file, 10MB per file
                                        </small>
                                    </div>
                                    
                                    <input type="file" class="d-none" id="submission_files" name="submission_files[]" 
                                           multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.txt">
                                    
                                    <!-- File Preview -->
                                    <div class="file-preview-container" id="filePreviewContainer"></div>
                                    
                                    <!-- Upload Progress -->
                                    <div class="progress d-none" id="uploadProgress" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-4">
                                    <button type="submit" name="submit_work" class="btn-submit" id="submitBtn">
                                        <i class="fas fa-paper-plane"></i>
                                        <?php echo $previous_submission ? 'Kirim Ulang Tugas' : 'Kumpulkan Tugas'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-lightbulb text-warning"></i>
                                Petunjuk Pengumpulan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-keyboard text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong>Jawaban Teks</strong>
                                            <p class="text-muted small mb-0">Tulis jawaban minimal 50 karakter. Gunakan bahasa yang jelas dan sistematis.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-file-upload text-success me-2 mt-1"></i>
                                        <div>
                                            <strong>Upload File</strong>
                                            <p class="text-muted small mb-0">Dukung jawaban dengan file PDF, DOC, PPT, atau gambar. Maksimal 5 file.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                        <div>
                                            <strong>Pemeriksaan</strong>
                                            <p class="text-muted small mb-0">Pastikan semua data sudah benar sebelum mengumpulkan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <footer class="page-footer mt-4">
        <div class="container-fluid">
            <p class="text-center mb-0">&copy; 2026 MATHLine | Pendidikan Matematika | Universitas Negeri Medan</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // DOM Elements
        const answerTextarea = document.getElementById('answer');
        const charCount = document.getElementById('charCount');
        const wordCountText = document.getElementById('wordCountText');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('submission_files');
        const filePreviewContainer = document.getElementById('filePreviewContainer');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        const submitBtn = document.getElementById('submitBtn');
        const keepExistingFiles = document.getElementById('keepExistingFiles');
        
        // File handling variables
        let selectedFiles = [];
        const maxFiles = 5;
        const maxFileSize = 10 * 1024 * 1024; // 10MB
        
        // Update character and word count
        function updateCount() {
            if (!answerTextarea) return;
            
            const text = answerTextarea.value.trim();
            const charLength = text.length;
            const wordArray = text.split(/\s+/).filter(word => word.length > 0);
            const wordLength = wordArray.length;
            
            // Update counters
            charCount.textContent = `${charLength} karakter`;
            wordCountText.textContent = `${wordLength} kata`;
            
            // Validate minimum length
            if (charLength < 50) {
                answerTextarea.classList.add('border-danger');
                answerTextarea.classList.remove('border-success');
            } else {
                answerTextarea.classList.remove('border-danger');
                answerTextarea.classList.add('border-success');
            }
        }
        
        // Initialize count on page load
        if (answerTextarea) {
            answerTextarea.addEventListener('input', updateCount);
            updateCount(); // Initial update
        }
        
        // File upload handling
        if (fileUploadArea && fileInput) {
            // Click to open file dialog
            fileUploadArea.addEventListener('click', () => fileInput.click());
            
            // File input change event
            fileInput.addEventListener('change', handleFileSelect);
            
            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, () => {
                    fileUploadArea.classList.add('dragover');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, () => {
                    fileUploadArea.classList.remove('dragover');
                }, false);
            });
            
            fileUploadArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }
            
            function handleFileSelect(e) {
                const files = e.target.files;
                handleFiles(files);
            }
            
            function handleFiles(files) {
                // Check total file count
                if (selectedFiles.length + files.length > maxFiles) {
                    alert(`Maksimal ${maxFiles} file yang dapat diupload.`);
                    return;
                }
                
                // Process each file
                Array.from(files).forEach(file => {
                    // Check file size
                    if (file.size > maxFileSize) {
                        alert(`File ${file.name} terlalu besar! Maksimal 10MB.`);
                        return;
                    }
                    
                    // Check file type
                    const allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'txt'];
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedExtensions.includes(fileExt)) {
                        alert(`Format file ${file.name} tidak didukung!`);
                        return;
                    }
                    
                    // Add to selected files
                    selectedFiles.push(file);
                    
                    // Update preview
                    updateFilePreview();
                });
                
                // Update file input
                updateFileInput();
            }
            
            function updateFilePreview() {
                filePreviewContainer.innerHTML = '';
                
                selectedFiles.forEach((file, index) => {
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    let iconClass = 'file-other';
                    let icon = 'fa-file';
                    
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        iconClass = 'file-image';
                        icon = 'fa-image';
                    } else if (fileExt === 'pdf') {
                        iconClass = 'file-pdf';
                        icon = 'fa-file-pdf';
                    } else if (['ppt', 'pptx'].includes(fileExt)) {
                        iconClass = 'file-presentation';
                        icon = 'fa-file-powerpoint';
                    } else if (['doc', 'docx', 'txt'].includes(fileExt)) {
                        iconClass = 'file-document';
                        icon = 'fa-file-word';
                    }
                    
                    const fileSize = formatFileSize(file.size);
                    
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-preview-item';
                    fileItem.innerHTML = `
                        <div class="file-icon ${iconClass}">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize} • ${fileExt.toUpperCase()}</div>
                        </div>
                        <button type="button" class="file-remove" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    filePreviewContainer.appendChild(fileItem);
                });
                
                // Show/hide upload area based on file count
                if (selectedFiles.length >= maxFiles) {
                    fileUploadArea.style.opacity = '0.5';
                    fileUploadArea.style.pointerEvents = 'none';
                } else {
                    fileUploadArea.style.opacity = '1';
                    fileUploadArea.style.pointerEvents = 'auto';
                }
            }
            
            function updateFileInput() {
                // Create a new DataTransfer object
                const dataTransfer = new DataTransfer();
                
                // Add each file to the DataTransfer object
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                
                // Update the file input
                fileInput.files = dataTransfer.files;
                
                // If files are selected, uncheck keep existing files
                if (selectedFiles.length > 0 && keepExistingFiles) {
                    keepExistingFiles.value = '0';
                }
            }
            
            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }
        
        // Remove file from selection
        window.removeFile = function(index) {
            selectedFiles.splice(index, 1);
            updateFilePreview();
            updateFileInput();
            
            // If no files selected, re-enable keep existing files
            if (selectedFiles.length === 0 && keepExistingFiles) {
                keepExistingFiles.value = '1';
            }
        };
        
        // Form validation
        document.getElementById('submissionForm')?.addEventListener('submit', function(e) {
            const answer = document.getElementById('answer')?.value.trim() || '';
            
            // Validate answer length
            if (answer.length < 50) {
                e.preventDefault();
                alert('Jawaban terlalu pendek! Minimal 50 karakter.');
                document.getElementById('answer').focus();
                return false;
            }
            
            // Show loading state
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                submitBtn.disabled = true;
            }
            
            // Show upload progress
            if (uploadProgress && selectedFiles.length > 0) {
                uploadProgress.classList.remove('d-none');
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 10;
                    progressBar.style.width = `${progress}%`;
                    if (progress >= 90) clearInterval(interval);
                }, 100);
            }
            
            // Confirmation dialog
            const previousSubmission = <?php echo $previous_submission ? 'true' : 'false'; ?>;
            const confirmMessage = previousSubmission 
                ? 'Tugas sebelumnya akan diganti dengan yang baru. Lanjutkan?'
                : 'Apakah Anda yakin ingin mengumpulkan tugas ini?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                if (submitBtn) {
                    submitBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i>${previousSubmission ? 'Kirim Ulang Tugas' : 'Kumpulkan Tugas'}`;
                    submitBtn.disabled = false;
                }
                return false;
            }
            
            return true;
        });
        
        // Auto-save draft
        let saveTimeout;
        if (answerTextarea) {
            answerTextarea.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    localStorage.setItem('draft_<?php echo $problem_id; ?>', this.value);
                }, 2000);
            });
            
            // Load draft on page load
            const savedDraft = localStorage.getItem('draft_<?php echo $problem_id; ?>');
            if (savedDraft && !answerTextarea.value) {
                answerTextarea.value = savedDraft;
                updateCount();
            }
        }
        
        // Clear draft on successful submission
        window.addEventListener('beforeunload', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn && submitBtn.disabled) {
                localStorage.removeItem('draft_<?php echo $problem_id; ?>');
            }
        });
        
        // Handle mobile orientation change
        let orientationTimeout;
        window.addEventListener('orientationchange', function() {
            clearTimeout(orientationTimeout);
            orientationTimeout = setTimeout(function() {
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    document.body.style.overflow = '';
                }, 100);
            }, 300);
        });
    </script>
</body>
</html>