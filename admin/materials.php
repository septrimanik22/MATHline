<?php
// admin/materials.php
session_start();
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireRole(['admin', 'guru']);

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle delete dengan POST untuk keamanan lebih baik
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("UPDATE materials SET is_published = FALSE WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Materi berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus materi!";
    }
    
    header("Location: materials.php");
    exit();
}

// 3 PERTEMUAN UTAMA SESUAI MODUL AJAR (DATA DEFAULT)
$pertemuan_data = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'video' => 'https://youtu.be/IoQ-xDOYRuM?si=1llFALfGiiAJfoez',
        'warna' => '#e3f2fd',
        'materi' => [
            [
                'title' => 'Pengertian Persamaan Garis Lurus',
                'content' => '<h4>Apa itu Persamaan Garis Lurus?</h4>
                             <p>Persamaan garis lurus adalah persamaan matematika yang menggambarkan hubungan linear antara variabel x dan y pada bidang koordinat kartesius.</p>
                             <h5>Karakteristik Utama:</h5>
                             <ul>
                                 <li>Memiliki bentuk umum: ax + by + c = 0</li>
                                 <li>Dapat ditulis dalam bentuk eksplisit: y = mx + c</li>
                                 <li>Membentuk grafik berupa garis lurus</li>
                                 <li>Memiliki kemiringan (gradien) yang konstan</li>
                             </ul>',
                'interaktif' => 'simulasi_grafik.php',
                'icon' => 'fas fa-chart-line'
            ],
            [
                'title' => 'Bentuk Umum dan Bentuk Eksplisit',
                'content' => '<h4>Bentuk Umum Persamaan Garis Lurus</h4>
                             <p>Bentuk umum: ax + by + c = 0</p>
                             <p>Dimana a, b, dan c adalah konstanta dengan a dan b tidak keduanya nol.</p>
                             
                             <h4>Bentuk Eksplisit (Bentuk Gradien-Titik Potong)</h4>
                             <p>y = mx + c</p>
                             <p>Dimana:<br>
                             m = gradien (kemiringan garis)<br>
                             c = titik potong sumbu y</p>
                             
                             <h5>Contoh Konversi:</h5>
                             <p>2x + 3y - 6 = 0 → y = -2/3 x + 2</p>',
                'interaktif' => 'simulasi_grafik.php',
                'icon' => 'fas fa-superscript'
            ],
            [
                'title' => 'Menyatakan Pola Garis Lurus',
                'content' => '<h4>Mengenal Pola pada Koordinat</h4>
                             <p>Pola garis lurus dapat dikenali dari hubungan yang konsisten antara nilai x dan y pada beberapa titik koordinat.</p>
                             
                             <h5>Contoh Pola:</h5>
                             <p>Jika diketahui titik-titik: (1,3), (2,5), (3,7)<br>
                             Maka dapat dilihat pola: y = 2x + 1</p>
                             
                             <h4>Langkah Menentukan Persamaan dari Pola:</h4>
                             <ol>
                                 <li>Identifikasi hubungan antara x dan y</li>
                                 <li>Tentukan gradien dari dua titik</li>
                                 <li>Cari persamaan garis</li>
                                 <li>Verifikasi dengan titik lain</li>
                             </ol>',
                'interaktif' => 'simulasi_koordinat.php',
                'icon' => 'fas fa-drafting-compass'
            ]
        ]
    ],
    
    'Pertemuan 2: Kemiringan (Gradien) Garis Lurus' => [
        'video' => 'https://youtu.be/P_cmHCpEhDY?si=G9Bk_xIPAezxC2O4',
        'warna' => '#bbdefb',
        'materi' => [
            [
                'title' => 'Konsep Dasar Gradien',
                'content' => '<h4>Apa itu Gradien?</h4>
                             <p>Gradien atau kemiringan adalah ukuran kecuraman suatu garis lurus. Dilambangkan dengan huruf m.</p>
                             
                             <h5>Rumus Gradien:</h5>
                             <p>m = Δy/Δx = (y₂ - y₁)/(x₂ - x₁)</p>
                             
                             <h4>Interpretasi Gradien:</h4>
                             <ul>
                                 <li>Gradien positif → garis naik ke kanan</li>
                                 <li>Gradien negatif → garis turun ke kanan</li>
                                 <li>Gradien nol → garis horizontal</li>
                                 <li>Gradien tak terdefinisi → garis vertikal</li>
                             </ul>',
                'interaktif' => 'simulasi_gradien.php',
                'icon' => 'fas fa-mountain'
            ],
            [
                'title' => 'Menghitung Gradien dari Dua Titik',
                'content' => '<h4>Cara Menghitung Gradien dari Dua Titik</h4>
                             <p>Diberikan dua titik: A(x₁, y₁) dan B(x₂, y₂)</p>
                             
                             <h5>Langkah-langkah:</h5>
                             <ol>
                                 <li>Identifikasi koordinat kedua titik</li>
                                 <li>Hitung selisih ordinat: Δy = y₂ - y₁</li>
                                 <li>Hitung selisih absis: Δx = x₂ - x₁</li>
                                 <li>Bagi Δy dengan Δx: m = Δy/Δx</li>
                             </ol>
                             
                             <h4>Contoh:</h4>
                             <p>Titik A(2, 3) dan B(5, 11)<br>
                             Δy = 11 - 3 = 8<br>
                             Δx = 5 - 2 = 3<br>
                             m = 8/3 ≈ 2.67</p>',
                'interaktif' => 'simulasi_dua_titik.php',
                'icon' => 'fas fa-calculator'
            ],
            [
                'title' => 'Jenis-jenis Gradien',
                'content' => '<h4>Macam-macam Gradien</h4>
                             
                             <h5>1. Gradien Positif</h5>
                             <p>• Nilai m > 0<br>
                             • Garis naik dari kiri ke kanan<br>
                             • Contoh: m = 2, m = 0.5</p>
                             
                             <h5>2. Gradien Negatif</h5>
                             <p>• Nilai m < 0<br>
                             • Garis turun dari kiri ke kanan<br>
                             • Contoh: m = -1, m = -0.3</p>
                             
                             <h5>3. Gradien Nol</h5>
                             <p>• Nilai m = 0<br>
                             • Garis horizontal sejajar sumbu x<br>
                             • Contoh: y = 5</p>
                             
                             <h5>4. Gradien Tak Terdefinisi</h5>
                             <p>• Garis vertikal sejajar sumbu y<br>
                             • Tidak memiliki nilai m<br>
                             • Contoh: x = 3</p>',
                'interaktif' => 'simulasi_jenis_gradien.php',
                'icon' => 'fas fa-chart-bar'
            ]
        ]
    ],
    
    'Pertemuan 3: Garis Sejajar dan Tegak Lurus' => [
        'video' => 'https://youtu.be/nAfbJ_WrxgE?si=MKvHWUeKqkq8BHKD',
        'warna' => '#90caf9',
        'materi' => [
            [
                'title' => 'Garis Sejajar: m₁ = m₂',
                'content' => '<h4>Syarat Garis Sejajar</h4>
                             <p>Dua garis dikatakan sejajar jika memiliki gradien yang sama.</p>
                             <p>Rumus: m₁ = m₂</p>
                             
                             <h5>Contoh:</h5>
                             <p>Garis 1: y = 2x + 3 → m₁ = 2<br>
                             Garis 2: y = 2x - 5 → m₂ = 2<br>
                             Karena m₁ = m₂, maka kedua garis sejajar.</p>
                             
                             <h4>Ciri-ciri Garis Sejajar:</h4>
                             <ul>
                                 <li>Memiliki gradien sama</li>
                                 <li>Tidak pernah berpotongan</li>
                                 <li>Jarak antar garis selalu sama</li>
                                 <li>Contoh nyata: rel kereta api</li>
                             </ul>',
                'interaktif' => 'simulasi_sejajar.php',
                'icon' => 'fas fa-grip-lines'
            ],
            [
                'title' => 'Garis Tegak Lurus: m₁ × m₂ = -1',
                'content' => '<h4>Syarat Garis Tegak Lurus</h4>
                             <p>Dua garis dikatakan tegak lurus jika hasil kali gradiennya sama dengan -1.</p>
                             <p>Rumus: m₁ × m₂ = -1</p>
                             
                             <h5>Contoh:</h5>
                             <p>Garis 1: y = 2x + 1 → m₁ = 2<br>
                             Garis 2: y = -½x + 3 → m₂ = -½<br>
                             m₁ × m₂ = 2 × (-½) = -1 ✓<br>
                             Jadi kedua garis tegak lurus.</p>
                             
                             <h4>Ciri-ciri Garis Tegak Lurus:</h4>
                             <ul>
                                 <li>Hasil kali gradien = -1</li>
                                 <li>Membentuk sudut 90°</li>
                                 <li>Saling berpotongan tegak lurus</li>
                                 <li>Contoh nyata: sudut bangunan</li>
                             </ul>',
                'interaktif' => 'simulasi_tegak_lurus.php',
                'icon' => 'fas fa-times-circle'
            ],
            [
                'title' => 'Penerapan dalam Desain Nyata',
                'content' => '<h4>Aplikasi Garis Sejajar dan Tegak Lurus</h4>
                             
                             <h5>1. Dalam Arsitektur:</h5>
                             <p>• Garis sejajar: kolom bangunan, jendela bertingkat<br>
                             • Garis tegak lurus: pertemuan dinding, tiang dengan lantai</p>
                             
                             <h5>2. Dalam Desain Grafis:</h5>
                             <p>• Alignment teks dan gambar<br>
                             • Grid system untuk layout</p>
                             
                             <h5>3. Dalam Teknik Sipil:</h5>
                             <p>• Jalan raya yang sejajar<br>
                             • Jembatan dengan penyangga tegak lurus</p>
                             
                             <h4>Langkah Desain:</h4>
                             <ol>
                                 <li>Tentukan kebutuhan fungsional</li>
                                 <li>Identifikasi garis utama</li>
                                 <li>Tentukan hubungan garis (sejajar/tegak lurus)</li>
                                 <li>Buat persamaan matematisnya</li>
                                 <li>Implementasikan dalam desain</li>
                             </ol>',
                'interaktif' => 'simulasi_desain.php',
                'icon' => 'fas fa-lightbulb'
            ]
        ]
    ]
];

// Count total materials
$total_materials = 0;
foreach ($pertemuan_data as $pertemuan) {
    $total_materials += count($pertemuan['materi']);
}
$total_pertemuan = count($pertemuan_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Kelola Materi - MATHLine</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --biru-muda-1: #e3f2fd;
            --biru-muda-2: #bbdefb;
            --biru-muda-3: #90caf9;
            --biru-utama: #1976d2;
            --hijau-muda: #4caf50;
            --hijau-tua: #2e7d32;
            --orange: #ff9800;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #2d3748;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Container Responsive */
        .container-fluid {
            padding: clamp(0.5rem, 2vw, 1rem);
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header-section {
            background: linear-gradient(135deg, var(--biru-muda-1) 0%, var(--biru-muda-3) 100%);
            padding: clamp(1.5rem, 4vw, 2.5rem) clamp(0.5rem, 2vw, 1rem);
            margin-bottom: clamp(1rem, 2.5vw, 2rem);
            border-radius: clamp(12px, 2.5vw, 20px);
            box-shadow: 0 4px 20px rgba(25, 118, 210, 0.1);
            margin-top: 0.5rem;
            width: 100%;
        }
        
        .header-title {
            font-size: clamp(1.5rem, 4vw, 2.2rem);
            font-weight: 800;
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
            text-align: center;
        }
        
        .header-subtitle {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            color: #374151;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            line-height: 1.6;
            text-align: center;
        }
        
        /* Pertemuan Card */
        .pertemuan-card {
            background: white;
            border-radius: clamp(12px, 2vw, 15px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: clamp(1.5rem, 3vw, 2.5rem);
            overflow: hidden;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .pertemuan-card:hover {
            transform: translateY(-5px);
        }
        
        .pertemuan-header {
            padding: clamp(1rem, 2vw, 1.5rem);
            border-bottom: 2px solid rgba(25, 118, 210, 0.1);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: clamp(0.5rem, 1.5vw, 1rem);
        }
        
        .pertemuan-title {
            font-weight: 700;
            color: #1a237e;
            margin-bottom: clamp(0.25rem, 0.5vw, 0.5rem);
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            line-height: 1.4;
            flex: 1;
            min-width: 250px;
        }
        
        .pertemuan-meta {
            display: flex;
            gap: clamp(0.5rem, 1.5vw, 1rem);
            align-items: center;
            color: #666;
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            flex-wrap: wrap;
        }
        
        /* Button Styles */
        .btn-tonton-video {
            background: linear-gradient(135deg, var(--hijau-muda), var(--hijau-tua));
            color: white;
            border: none;
            padding: clamp(8px, 1.5vw, 10px) clamp(12px, 2vw, 20px);
            border-radius: clamp(8px, 1vw, 10px);
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 8px);
            text-decoration: none;
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            white-space: nowrap;
        }
        
        .btn-tambah-video {
            background: linear-gradient(135deg, var(--orange), #e65100);
            color: white;
            border: none;
            padding: clamp(8px, 1.5vw, 10px) clamp(12px, 2vw, 20px);
            border-radius: clamp(8px, 1vw, 10px);
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 8px);
            text-decoration: none;
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            white-space: nowrap;
        }
        
        /* Video Buttons Container */
        .video-buttons {
            display: flex;
            gap: clamp(8px, 1vw, 10px);
            flex-wrap: wrap;
            justify-content: flex-end;
            width: 100%;
        }
        
        /* Materi Card Grid */
        .materi-card {
            border: none;
            border-radius: clamp(10px, 1.5vw, 12px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            overflow: hidden;
            border-left: 4px solid var(--biru-utama);
            position: relative;
            min-height: clamp(280px, 40vh, 320px);
        }
        
        .materi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(25, 118, 210, 0.15);
            border-left: 4px solid var(--orange);
        }
        
        .materi-card-content {
            padding: clamp(1rem, 1.8vw, 1.5rem);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .materi-icon-wrapper {
            width: clamp(50px, 8vw, 60px);
            height: clamp(50px, 8vw, 60px);
            background: linear-gradient(135deg, var(--biru-muda-2), var(--biru-utama));
            border-radius: clamp(10px, 1.5vw, 15px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(1.4rem, 2.5vw, 1.8rem);
            margin-bottom: clamp(0.75rem, 1.2vw, 1rem);
        }
        
        .materi-card-title {
            font-size: clamp(1rem, 1.8vw, 1.1rem);
            font-weight: 700;
            color: #1e293b;
            margin-bottom: clamp(0.75rem, 1.2vw, 1rem);
            line-height: 1.4;
            min-height: 2.8rem;
        }
        
        .materi-excerpt {
            color: #666;
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin-bottom: clamp(0.75rem, 1.2vw, 1rem);
            flex-grow: 1;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Action Buttons */
        .action-buttons {
            position: absolute;
            top: clamp(10px, 1.5vw, 15px);
            right: clamp(10px, 1.5vw, 15px);
            z-index: 10;
        }
        
        .action-btn {
            width: clamp(32px, 4vw, 36px);
            height: clamp(32px, 4vw, 36px);
            border-radius: clamp(6px, 1vw, 8px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s;
            font-size: clamp(0.9rem, 1.5vw, 1rem);
        }
        
        /* Back Button */
        .back-to-dashboard {
            position: relative;
            top: 0;
            left: 0;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            z-index: 100;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        
        .btn-back {
            background: white;
            border: 2px solid var(--biru-utama);
            color: var(--biru-utama);
            padding: clamp(6px, 1vw, 8px) clamp(12px, 2vw, 16px);
            border-radius: clamp(8px, 1.5vw, 10px);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: clamp(4px, 0.8vw, 8px);
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            max-width: 300px;
        }
        
        /* Button in Cards */
        .btn-lihat-detail {
            background: linear-gradient(135deg, var(--biru-muda-2), var(--biru-utama));
            color: white;
            border: none;
            padding: clamp(8px, 1.2vw, 10px);
            border-radius: clamp(6px, 1vw, 8px);
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-simulasi {
            background: linear-gradient(135deg, var(--hijau-muda), var(--hijau-tua));
            color: white;
            border: none;
            padding: clamp(8px, 1.2vw, 10px);
            border-radius: clamp(6px, 1vw, 8px);
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            min-height: 44px;
            gap: 8px;
        }
        
        .materi-card-buttons {
            display: flex;
            gap: clamp(8px, 1vw, 12px);
            margin-top: auto;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--biru-muda-1), var(--biru-muda-3));
            margin-top: auto;
            border-top: 1px solid #e5e7eb;
            padding: clamp(1rem, 2vw, 1.5rem) 0 !important;
            width: 100%;
        }
        
        footer p {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            margin: 0;
            text-align: center;
        }
        
        /* Modal Responsive */
        .modal-dialog {
            margin: clamp(1rem, 2vw, 2rem);
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 1400px) {
            .container-fluid {
                padding: clamp(0.5rem, 1.5vw, 1rem);
            }
        }
        
        @media (max-width: 1200px) {
            .pertemuan-title {
                min-width: 200px;
            }
            
            .video-buttons {
                justify-content: flex-start;
            }
        }
        
        @media (max-width: 992px) {
            .header-section {
                padding: clamp(1.25rem, 3vw, 1.5rem) clamp(0.75rem, 1.5vw, 1rem);
                border-radius: 16px;
            }
            
            .pertemuan-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .pertemuan-title {
                min-width: 100%;
            }
            
            .video-buttons {
                justify-content: flex-start;
                width: 100%;
            }
            
            .btn-tonton-video, .btn-tambah-video {
                width: 100%;
                justify-content: center;
            }
            
            .materi-card {
                min-height: 280px;
            }
        }
        
        @media (max-width: 768px) {
            .header-title {
                font-size: 1.4rem;
            }
            
            .pertemuan-card {
                border-radius: 12px;
            }
            
            .pertemuan-header {
                padding: 1.25rem;
            }
            
            .pertemuan-title {
                font-size: 1.2rem;
                min-width: 100%;
            }
            
            .video-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-tonton-video, .btn-tambah-video {
                width: 100%;
            }
            
            .materi-card {
                min-height: 260px;
            }
            
            .materi-card-buttons {
                flex-direction: column;
            }
            
            .back-to-dashboard {
                justify-content: flex-start;
            }
            
            .btn-back {
                max-width: 250px;
            }
        }
        
        @media (max-width: 576px) {
            body {
                font-size: 14px;
            }
            
            .container-fluid {
                padding: 0.5rem;
            }
            
            .header-section {
                padding: 1.25rem 0.75rem;
                border-radius: 12px;
                margin-top: 0.25rem;
            }
            
            .header-title {
                font-size: 1.3rem;
            }
            
            .pertemuan-card {
                border-radius: 10px;
                margin-bottom: 1.5rem;
            }
            
            .pertemuan-header {
                padding: 1rem;
            }
            
            .pertemuan-title {
                font-size: 1.1rem;
            }
            
            .pertemuan-meta {
                font-size: 0.85rem;
            }
            
            .materi-card {
                min-height: 240px;
                border-radius: 10px;
            }
            
            .materi-card-content {
                padding: 1rem;
            }
            
            .materi-icon-wrapper {
                width: 45px;
                height: 45px;
                font-size: 1.4rem;
                border-radius: 10px;
            }
            
            .materi-card-title {
                font-size: 1rem;
                min-height: 2.5rem;
            }
            
            .materi-excerpt {
                font-size: 0.85rem;
            }
            
            .btn-back {
                padding: 6px 12px;
                font-size: 0.9rem;
                max-width: 220px;
            }
            
            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 375px) {
            .header-title {
                font-size: 1.2rem;
            }
            
            .pertemuan-title {
                font-size: 1rem;
            }
            
            .btn-tonton-video, .btn-tambah-video {
                font-size: 0.85rem;
                padding: 8px 12px;
            }
            
            .materi-card {
                min-height: 220px;
            }
            
            .materi-card-title {
                font-size: 0.95rem;
            }
            
            .materi-card-buttons {
                gap: 6px;
            }
            
            .btn-lihat-detail, .btn-simulasi {
                font-size: 0.85rem;
                min-height: 40px;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn-tonton-video, .btn-tambah-video, .btn-lihat-detail, .btn-simulasi, .btn-back {
                min-height: 48px;
            }
            
            .action-btn {
                min-width: 40px;
                min-height: 40px;
            }
            
            .materi-card:hover {
                transform: none;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .header-section {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .materi-card {
                min-height: 200px;
            }
            
            .pertemuan-card {
                margin-bottom: 1.5rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .header-section, .back-to-dashboard, .video-buttons, .action-buttons, footer {
                display: none !important;
            }
            
            .pertemuan-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .materi-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #000;
            }
        }
        
        /* Grid Columns */
        .materi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr));
            gap: clamp(1rem, 2vw, 1.5rem);
            padding: clamp(1rem, 2vw, 1.5rem);
        }
    </style>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <div class="container-fluid">
        <div class="back-to-dashboard">
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>
    
    <!-- Header Section -->
    <div class="header-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="header-content">
                        <h1 class="header-title">
                            <i class="fas fa-book-open me-2"></i>Daftar Materi
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-fluid py-2 py-lg-3">
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
        
        <!-- Materials Section -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($pertemuan_data)): ?>
                    <div class="alert alert-info text-center">
                        <h4><i class="fas fa-info-circle me-2"></i>Tidak Ada Materi</h4>
                        <p>Belum ada materi yang tersedia</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pertemuan_data as $judul_pertemuan => $pertemuan): ?>
                        <?php if (!empty($pertemuan['materi'])): ?>
                        <div class="pertemuan-card" style="border-left: 5px solid <?php echo $pertemuan['warna']; ?>;">
                            <!-- Pertemuan Header -->
                            <div class="pertemuan-header" style="background: <?php echo $pertemuan['warna']; ?>20;">
                                <div class="w-100">
                                    <h3 class="pertemuan-title">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>
                                        <?php echo $judul_pertemuan; ?>
                                    </h3>
                                    <div class="pertemuan-meta mb-3">
                                        <span>
                                            <i class="fas fa-book me-1"></i>
                                            <?php echo count($pertemuan['materi']); ?> Sub Materi
                                        </span>
                                        <?php if (!empty($pertemuan['video'])): ?>
                                        <span>
                                            <i class="fas fa-video me-1"></i>
                                            Video Tersedia
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="video-buttons">
                                    <?php if (!empty($pertemuan['video'])): ?>
                                    <button class="btn-tonton-video" onclick="tontonVideoPertemuan('<?php echo $pertemuan['video']; ?>', '<?php echo htmlspecialchars($judul_pertemuan, ENT_QUOTES); ?>')">
                                        <i class="fas fa-play-circle me-2"></i>
                                        Tonton Video
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn-tambah-video" onclick="tambahVideo('<?php echo htmlspecialchars($judul_pertemuan, ENT_QUOTES); ?>')">
                                        <i class="fas fa-video-plus me-2"></i>
                                        Tambah Video
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Materi Cards Grid -->
                            <div class="materi-grid">
                                <?php foreach ($pertemuan['materi'] as $index => $material): ?>
                                    <?php 
                                    $icon = $material['icon'] ?? 'fas fa-book';
                                    ?>
                                    <div class="materi-card-wrapper">
                                        <div class="materi-card">
                                            <div class="action-buttons">
                                                <div class="dropdown">
                                                    <button class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="editMateri(<?php echo htmlspecialchars(json_encode([
                                                                'title' => $material['title'],
                                                                'content' => $material['content'],
                                                                'sub_bab' => $judul_pertemuan
                                                            ]), ENT_QUOTES, 'UTF-8'); ?>)">
                                                                <i class="fas fa-edit me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item text-danger" 
                                                                    onclick="hapusMateriKonfirmasi('<?php echo htmlspecialchars($material['title']); ?>')">
                                                                <i class="fas fa-trash me-2"></i>Hapus
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <div class="materi-card-content">
                                                <div class="materi-icon-wrapper">
                                                    <i class="<?php echo $icon; ?>"></i>
                                                </div>
                                                
                                                <h5 class="materi-card-title">
                                                    <?php echo htmlspecialchars($material['title']); ?>
                                                </h5>
                                                
                                                <p class="materi-excerpt">
                                                    <?php 
                                                    $content = strip_tags($material['content']);
                                                    echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                                    ?>
                                                </p>
                                                
                                                <div class="materi-card-buttons">
                                                    <button type="button" 
                                                            class="btn btn-lihat-detail detail-btn"
                                                            data-title="<?php echo htmlspecialchars($material['title']); ?>"
                                                            data-content="<?php echo htmlspecialchars($material['content']); ?>">
                                                        <i class="fas fa-eye me-2"></i>
                                                        <span>Detail</span>
                                                    </button>
                                                    
                                                    <?php if (isset($material['interaktif']) && $material['interaktif'] != '#'): ?>
                                                    <a href="<?php echo htmlspecialchars($material['interaktif']); ?>" 
                                                       class="btn btn-simulasi" 
                                                       target="_blank"
                                                       title="Buka Simulasi: <?php echo htmlspecialchars($material['title']); ?>">
                                                        <i class="fas fa-play me-2"></i>
                                                        <span>Simulasi</span>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Video -->
    <div class="modal fade" id="tambahVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-video-plus me-2 text-success"></i>
                        <span id="modalPertemuanTitle">Tambah Video untuk Pertemuan</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambahVideo">
                    <div class="modal-body">
                        <input type="hidden" id="inputPertemuan" name="pertemuan">
                        
                        <div class="mb-3">
                            <label for="inputVideoUrl" class="form-label">URL Video YouTube</label>
                            <input type="url" class="form-control" id="inputVideoUrl" 
                                   name="video_url" placeholder="https://youtu.be/..." required>
                            <div class="form-text">Masukkan link YouTube video pembelajaran</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Simpan Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Video Preview Modal -->
    <div class="modal fade" id="videoPertemuanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-video me-2 text-danger"></i>
                        <span id="videoPertemuanTitle">Video Pembelajaran</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-container">
                        <div class="video-wrapper">
                            <iframe id="videoPertemuanFrame" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Materi Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-book me-2"></i>
                        <span id="detailTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent" class="materi-modal-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-4 mt-lg-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-dark mb-0">
                        &copy; <?php echo date('Y'); ?> MATHLine | Pendidikan Matematika | Universitas Negeri Medan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Function untuk menambah video
    function tambahVideo(pertemuan) {
        const modal = new bootstrap.Modal(document.getElementById('tambahVideoModal'));
        document.getElementById('modalPertemuanTitle').textContent = 'Tambah Video untuk ' + pertemuan;
        document.getElementById('inputPertemuan').value = pertemuan;
        document.getElementById('inputVideoUrl').value = '';
        modal.show();
    }
    
    // Function untuk menonton video pertemuan
    function tontonVideoPertemuan(videoUrl, pertemuanTitle) {
        document.getElementById('videoPertemuanTitle').textContent = pertemuanTitle;
        
        // Set video
        const videoFrame = document.getElementById('videoPertemuanFrame');
        let videoId = '';
        
        if (videoUrl.includes('youtube.com/embed/')) {
            videoId = videoUrl.split('embed/')[1];
        } else if (videoUrl.includes('youtube.com/watch?v=')) {
            videoId = videoUrl.split('v=')[1].split('&')[0];
        } else if (videoUrl.includes('youtu.be/')) {
            videoId = videoUrl.split('youtu.be/')[1].split('?')[0];
        }
        
        if (videoId) {
            // Menambahkan responsive class untuk iframe
            videoFrame.className = 'w-100';
            videoFrame.style.height = '60vh';
            videoFrame.style.minHeight = '400px';
            
            videoFrame.src = `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&autoplay=1`;
            const modal = new bootstrap.Modal(document.getElementById('videoPertemuanModal'));
            modal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'URL Video Tidak Valid',
                text: 'Format URL video tidak dikenali',
                confirmButtonColor: '#3085d6'
            });
        }
    }
    
    // Function untuk submit form tambah video
    document.getElementById('formTambahVideo').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const pertemuan = document.getElementById('inputPertemuan').value;
        const videoUrl = document.getElementById('inputVideoUrl').value;
        
        // Validasi URL YouTube
        if (!isValidYouTubeUrl(videoUrl)) {
            Swal.fire({
                icon: 'error',
                title: 'URL Tidak Valid',
                text: 'Masukkan URL YouTube yang valid (contoh: https://youtu.be/... atau https://www.youtube.com/watch?v=...)',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Tampilkan loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        submitBtn.disabled = true;
        
        // Simulasi pengiriman data (dalam implementasi nyata, ini akan AJAX ke server)
        setTimeout(() => {
            // Reset form dan close modal
            document.getElementById('formTambahVideo').reset();
            bootstrap.Modal.getInstance(document.getElementById('tambahVideoModal')).hide();
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Tampilkan sukses
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: `Video berhasil ditambahkan ke:<br><strong>${pertemuan}</strong>`,
                confirmButtonColor: '#1976d6',
                timer: 2000
            }).then(() => {
                // Refresh halaman untuk melihat perubahan
                location.reload();
            });
        }, 1500);
    });
    
    // Validasi URL YouTube
    function isValidYouTubeUrl(url) {
        const patterns = [
            /^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
            /^(https?:\/\/)?(www\.)?youtu\.be\/([a-zA-Z0-9_-]+)/,
            /^(https?:\/\/)?(www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]+)/
        ];
        
        return patterns.some(pattern => pattern.test(url));
    }
    
    // Lihat detail materi
    function lihatDetail(materi) {
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        const detailTitle = document.getElementById('detailTitle');
        const detailContent = document.getElementById('detailContent');
        
        detailTitle.textContent = materi.title;
        detailContent.innerHTML = materi.content;
        
        // Responsive modal for mobile
        if (window.innerWidth < 768) {
            detailModal._dialog.style.margin = '1rem';
        }
        
        detailModal.show();
    }
    
    // Edit materi
    function editMateri(materi) {
        Swal.fire({
            title: 'Edit Materi',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Judul Materi</label>
                        <input type="text" id="editJudul" class="form-control" value="${materi.title}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pertemuan</label>
                        <input type="text" id="editPertemuan" class="form-control" value="${materi.sub_bab}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konten Materi</label>
                        <textarea id="editKonten" class="form-control" rows="8" style="font-size: 14px;">${materi.content}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1976d2',
            width: window.innerWidth < 768 ? '90%' : '600px',
            preConfirm: () => {
                const judul = document.getElementById('editJudul').value;
                const konten = document.getElementById('editKonten').value;
                
                if (!judul.trim()) {
                    Swal.showValidationMessage('Judul materi harus diisi');
                    return false;
                }
                
                if (!konten.trim()) {
                    Swal.showValidationMessage('Konten materi harus diisi');
                    return false;
                }
                
                return { judul, konten };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Materi berhasil diperbarui',
                    confirmButtonColor: '#1976d2',
                    timer: 1500
                }).then(() => {
                    // Refresh halaman untuk melihat perubahan
                    location.reload();
                });
            }
        });
    }
    
    // Hapus materi konfirmasi
    function hapusMateriKonfirmasi(title) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `<p>Yakin ingin menghapus materi:</p><p class="fw-bold">"${title}"</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            width: window.innerWidth < 768 ? '90%' : '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Materi berhasil dihapus',
                    confirmButtonColor: '#1976d2',
                    timer: 1500
                }).then(() => {
                    // Refresh halaman untuk melihat perubahan
                    location.reload();
                });
            }
        });
    }
    
    // Setup event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Setup tombol detail
        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const title = this.getAttribute('data-title');
                const content = this.getAttribute('data-content');
                
                lihatDetail({ title: title, content: content });
            });
        });
        
        // Handle responsive video modal
        const videoModal = document.getElementById('videoPertemuanModal');
        if (videoModal) {
            videoModal.addEventListener('show.bs.modal', function () {
                const iframe = document.getElementById('videoPertemuanFrame');
                if (window.innerWidth < 768) {
                    iframe.style.height = '40vh';
                    iframe.style.minHeight = '300px';
                } else {
                    iframe.style.height = '60vh';
                    iframe.style.minHeight = '400px';
                }
            });
        }
        
        // Handle window resize for responsive behavior
        window.addEventListener('resize', function() {
            // Adjust modal sizes on resize
            const videoModalElement = document.getElementById('videoPertemuanModal');
            if (videoModalElement && bootstrap.Modal.getInstance(videoModalElement)) {
                const iframe = document.getElementById('videoPertemuanFrame');
                if (window.innerWidth < 768) {
                    iframe.style.height = '40vh';
                    iframe.style.minHeight = '300px';
                } else {
                    iframe.style.height = '60vh';
                    iframe.style.minHeight = '400px';
                }
            }
        });
        
        // Touch device optimizations
        if ('ontouchstart' in window || navigator.maxTouchPoints) {
            // Increase touch targets
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.style.minWidth = '44px';
                btn.style.minHeight = '44px';
            });
            
            // Prevent hover effects on touch devices
            document.querySelectorAll('.materi-card').forEach(card => {
                card.classList.remove('hover-effect');
            });
        }
    });
    
    // Reset video when modal is closed
    document.getElementById('videoPertemuanModal').addEventListener('hidden.bs.modal', function () {
        const videoFrame = document.getElementById('videoPertemuanFrame');
        videoFrame.src = '';
    });
    
    // Error handling
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
    });
</script>
</body>
</html>