<?php
// student/view_materials.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');

require_once '../includes/db_connection.php';
$database = Database::getInstance();
$conn = $database->getConnection();

// 3 PERTEMUAN UTAMA SESUAI MODUL AJAR
$pertemuan_data = [
    'Pertemuan 1: Konsep Persamaan Garis Lurus' => [
        'video' => 'https://youtu.be/IoQ-xDOYRuM?si=1llFALfGiiAJfoez',
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
                'interaktif' => '#',
                'icon' => 'fas fa-chart-line',
                'langkah' => [
                    'Memahami hubungan linear antara x dan y',
                    'Mengenal bentuk umum ax + by + c = 0',
                    'Mengubah ke bentuk eksplisit y = mx + c',
                    'Menggambar grafik dari persamaan'
                ]
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
                             
                             <h5>Contoh:</h5>
                             <p>2x + 3y - 6 = 0 → y = -2/3 x + 2</p>',
                'interaktif' => 'simulasi_grafik.php',
                'icon' => 'fas fa-superscript',
                'langkah' => [
                    'Mengenal bentuk umum ax + by + c = 0',
                    'Mengubah ke bentuk y = mx + c',
                    'Menentukan nilai m dan c',
                    'Contoh aplikasi dalam kehidupan'
                ]
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
                'icon' => 'fas fa-drafting-compass',
                'langkah' => [
                    'Mengamati pola pada koordinat',
                    'Menentukan hubungan x dan y',
                    'Membuat persamaan dari pola',
                    'Verifikasi dengan titik lain'
                ]
            ]
        ],
        'warna' => '#e3f2fd'
    ],
    
    'Pertemuan 2: Kemiringan (Gradien) Garis Lurus' => [
        'video' => 'https://youtu.be/P_cmHCpEhDY?si=G9Bk_xIPAezxC2O4',
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
                'icon' => 'fas fa-mountain',
                'langkah' => [
                    'Memahami arti kemiringan',
                    'Mengenal rasio Δy/Δx',
                    'Membaca gradien dari grafik',
                    'Menerjemahkan ke situasi nyata'
                ]
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
                'icon' => 'fas fa-calculator',
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
                'icon' => 'fas fa-chart-bar',
                'langkah' => [
                    'Gradien positif: garis naik',
                    'Gradien negatif: garis turun',
                    'Gradien nol: garis horizontal',
                    'Gradien tak terdefinisi: garis vertikal'
                ]
            ]
        ],
        'warna' => '#bbdefb'
    ],
    
    'Pertemuan 3: Garis Sejajar dan Tegak Lurus' => [
        'video' => 'https://youtu.be/nAfbJ_WrxgE?si=MKvHWUeKqkq8BHKD',
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
                'icon' => 'fas fa-grip-lines',
                'langkah' => [
                    'Memahami konsep sejajar',
                    'Mengidentifikasi gradien sama',
                    'Membuat garis sejajar',
                    'Contoh dalam arsitektur'
                ]
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
                'icon' => 'fas fa-times-circle',
                'langkah' => [
                    'Memahami konsep tegak lurus',
                    'Rumus m₁ × m₂ = -1',
                    'Mencari gradien tegak lurus',
                    'Aplikasi dalam desain'
                ]
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
                'icon' => 'fas fa-lightbulb',
                'langkah' => [
                    'Analisis kebutuhan desain',
                    'Tentukan garis sejajar',
                    'Tentukan garis tegak lurus',
                    'Buat persamaan masing-masing'
                ]
            ]
        ],
        'warna' => '#90caf9'
    ]
];

// Count total materials
$total_materials = 0;
foreach ($pertemuan_data as $pertemuan) {
    $total_materials += count($pertemuan['materi']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Materi Pembelajaran - MATHLine</title>
    
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
        }
        
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #2d3748;
            overflow-x: hidden;
            width: 100%;
        }
        
        /* Header Section Responsive */
        .header-section {
            background: linear-gradient(135deg, var(--biru-muda-1) 0%, var(--biru-muda-3) 100%);
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(25, 118, 210, 0.08);
        }
        
        .header-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        
        .header-subtitle {
            font-size: clamp(0.9rem, 2.5vw, 1.2rem);
            color: #374151;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        /* Card Responsive */
        .card-materi {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            overflow: hidden;
            border-left: 4px solid var(--biru-utama);
            margin-bottom: 1rem;
        }
        
        .card-materi:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(25, 118, 210, 0.15);
            border-left: 4px solid #ff9800;
        }
        
        .materi-card-content {
            padding: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .materi-icon-wrapper {
            width: clamp(45px, 8vw, 60px);
            height: clamp(45px, 8vw, 60px);
            background: linear-gradient(135deg, var(--biru-muda-2), var(--biru-utama));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            margin-bottom: 0.75rem;
            flex-shrink: 0;
        }
        
        .materi-card-title {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            min-height: auto;
        }
        
        /* Buttons Responsive */
        .simulasikan-btn {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        .btn-biru-muda {
            background: linear-gradient(135deg, var(--biru-muda-2), var(--biru-utama));
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            font-size: 0.9rem;
        }
        
        .pelajari-materi-btn {
            background: linear-gradient(135deg, var(--biru-muda-2), var(--biru-utama));
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            font-size: 0.9rem;
        }
        
        .pertemuan-video-btn {
            background: linear-gradient(135deg, var(--hijau-muda), var(--hijau-tua));
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.9rem;
            width: 100%;
        }
        
        /* Pertemuan Card Responsive */
        .pertemuan-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--biru-utama);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .pertemuan-header {
            background: linear-gradient(135deg, var(--biru-muda-1), var(--biru-muda-2));
            padding: 1rem;
            border-bottom: 2px solid var(--biru-utama);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .pertemuan-title {
            font-size: clamp(1.1rem, 3vw, 1.3rem);
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        /* Video Container Responsive */
        .video-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            background: #000;
        }
        
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Modal Responsive */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            margin: 1rem;
        }
        
        .modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #dee2e6;
            position: relative;
        }
        
        .modal-header .btn-close-custom {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s;
        }
        
        .modal-header .btn-close-custom:hover {
            background: #c82333;
            transform: translateY(-50%) scale(1.1);
        }
        
        .modal-body {
            padding: 1rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        /* Grid System Responsive */
        .row.g-4 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }
        
        /* Simulasi Grid Responsive */
        .simulasi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 250px), 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .simulasi-card {
            background: white;
            border: 1px solid #e3f2fd;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s;
        }
        
        /* Typography Responsive */
        h1, h2, h3, h4, h5, h6 {
            line-height: 1.2;
        }
        
        .materi-content h4 {
            font-size: 1.1rem;
            color: #1976d2;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
        }
        
        .materi-content h5 {
            font-size: 1rem;
            color: #2e7d32;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .materi-content p, .materi-content li {
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .materi-content ul, .materi-content ol {
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }
        
        /* Tombol X Simulasi */
        .btn-close-simulasi {
            background: #dc3545 !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1rem !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            margin-left: auto !important;
        }
        
        .btn-close-simulasi:hover {
            background: #c82333 !important;
            transform: scale(1.1) !important;
        }
        
        /* Utilities */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Touch Optimizations */
        button, .btn, a.btn {
            min-height: 44px;
            min-width: 44px;
            touch-action: manipulation;
        }
        
        /* Mode View Styles */
        .mode-pelajari {
            display: block;
        }
        
        .mode-simulasi {
            display: none;
        }
        
        /* Simulasi Container */
        .simulasi-container {
            text-align: center;
            padding: 1rem;
            position: relative;
        }
        
        /* Mobile Specific Styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .header-section {
                padding: 1.25rem 0;
                border-radius: 0 0 10px 10px;
            }
            
            .pertemuan-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .pertemuan-video-btn {
                width: 100%;
                margin-top: 0.5rem;
            }
            
            .materi-card-content {
                padding: 0.875rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            
            .modal-xl {
                --bs-modal-width: 95%;
            }
            
            .d-flex.align-items-start.mb-3 {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .materi-icon-wrapper {
                margin-right: 0;
                margin-bottom: 0.75rem;
            }
            
            .btn-close-simulasi {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.9rem !important;
            }
        }
        
        @media (max-width: 576px) {
            body {
                font-size: 14px;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
            
            .header-subtitle {
                font-size: 0.9rem;
            }
            
            .pertemuan-title {
                font-size: 1rem;
            }
            
            .materi-card-title {
                font-size: 0.9rem;
                min-height: auto;
            }
            
            .pelajari-materi-btn, .simulasikan-btn {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
            }
            
            .pertemuan-video-btn {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
            
            .row.g-4 {
                --bs-gutter-x: 0.75rem;
                --bs-gutter-y: 0.75rem;
            }
            
            .col-lg-4, .col-md-6 {
                padding: 0.375rem;
            }
            
            .materi-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 375px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .header-section {
                padding: 1rem 0;
            }
            
            .header-title {
                font-size: 1.3rem;
            }
            
            .pelajari-materi-btn, .simulasikan-btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }
            
            .materi-icon-wrapper {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }
        
        /* Landscape Orientation */
        @media (max-height: 600px) and (orientation: landscape) {
            .header-section {
                padding: 1rem 0;
            }
            
            .header-title {
                margin-bottom: 0.25rem;
            }
            
            .pertemuan-card {
                margin-bottom: 1rem;
            }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a2e;
                color: #e0e0e0;
            }
            
            .card-materi, .pertemuan-card, .simulasi-card {
                background: #2d3748;
                color: #e0e0e0;
            }
            
            .materi-card-title {
                color: #e2e8f0;
            }
            
            .materi-content h4 {
                color: #64b5f6;
            }
            
            .materi-content h5 {
                color: #81c784;
            }
        }
        
        /* Print Styles */
        @media print {
            .header-section {
                background: white !important;
                color: black !important;
                box-shadow: none !important;
            }
            
            .card-materi, .pertemuan-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            button, .btn, .pertemuan-video-btn {
                display: none !important;
            }
            
            .header-title {
                -webkit-text-fill-color: black !important;
                color: black !important;
                background: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="header-content text-center">
                        <h1 class="header-title">
                            <i class="fas fa-book-open me-2"></i>Materi Pembelajaran
                        </h1>
                        <p class="header-subtitle">
                            Ayok belajar Persamaan Garis Lurus
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container py-3">
        <!-- Tombol Kembali ke Dashboard -->
        <div class="mb-3">
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
        
        <!-- Materials Grid -->
        <?php foreach ($pertemuan_data as $judul_pertemuan => $pertemuan): ?>
            <!-- Pertemuan Card -->
            <div class="pertemuan-card">
                <!-- Pertemuan Header -->
                <div class="pertemuan-header">
                    <div>
                        <h3 class="pertemuan-title">
                            <?php echo $judul_pertemuan; ?>
                        </h3>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-secondary">
                                <i class="fas fa-book me-1"></i>
                                <?php echo count($pertemuan['materi']); ?> Sub Materi
                            </span>
                            <span class="badge bg-danger">
                                <i class="fas fa-video me-1"></i> 1 Video
                            </span>
                        </div>
                    </div>
                    <div class="w-100">
                        <button class="pertemuan-video-btn" 
                                onclick="tontonVideoPertemuan('<?php echo $pertemuan['video']; ?>', '<?php echo htmlspecialchars($judul_pertemuan, ENT_QUOTES, 'UTF-8'); ?>')">
                            <i class="fas fa-play-circle"></i>
                            Tonton Video Pembelajaran
                        </button>
                    </div>
                </div>
                
                <!-- Materi Cards -->
                <div class="row g-3 p-3">
                    <?php foreach ($pertemuan['materi'] as $index => $materi): ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card-materi h-100">
                                <div class="materi-card-content">
                                    <div class="d-flex align-items-start">
                                        <div class="materi-icon-wrapper me-3">
                                            <i class="<?php echo $materi['icon']; ?>"></i>
                                        </div>
                                        <div>
                                            <h5 class="materi-card-title text-truncate-2">
                                                <?php echo $materi['title']; ?>
                                            </h5>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-2">
                                        <!-- Button Pelajari Materi -->
                                        <button class="btn pelajari-materi-btn mb-2" 
                                                onclick="bukaMateri(
                                                    <?php echo htmlspecialchars(json_encode($materi), ENT_QUOTES, 'UTF-8'); ?>,
                                                    '<?php echo htmlspecialchars($judul_pertemuan, ENT_QUOTES, 'UTF-8'); ?>'
                                                )">
                                            <i class="fas fa-eye me-1"></i>Pelajari Materi
                                        </button>
                                        
                                        <!-- Button Coba Simulasi (Hanya tampil jika ada simulasi) -->
                                        <?php if (!empty($materi['interaktif']) && $materi['interaktif'] != '#'): ?>
                                            <button class="btn simulasikan-btn" 
                                                    onclick="bukaSimulasi('<?php echo $materi['interaktif']; ?>', '<?php echo htmlspecialchars($materi['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                                <i class="fas fa-play-circle me-1"></i>Coba Simulasi
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Video Pertemuan Modal -->
    <div class="modal fade" id="videoPertemuanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-video me-2 text-danger"></i>
                        <span id="videoPertemuanTitle" class="text-truncate">Video Pembelajaran</span>
                    </h5>
                    <button type="button" class="btn-close-custom" onclick="tutupVideoModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-container">
                        <div class="video-wrapper">
                            <iframe id="videoPertemuanFrame" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    loading="lazy">
                            </iframe>
                        </div>
                    </div>
                    <div class="p-3">
                        <div id="videoPertemuanDescription" class="video-description">
                            <!-- Deskripsi akan diisi oleh JavaScript -->
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-puzzle-piece me-2 text-success"></i>Simulasi Interaktif
                            </h6>
                            <div id="simulasiList" class="simulasi-grid">
                                <!-- Daftar simulasi akan diisi oleh JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Materi Modal -->
    <div class="modal fade" id="materiModal" tabindex="-1" aria-labelledby="materiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        <i class="fas fa-book me-2"></i>
                        <span id="modalJudul">Materi</span>
                    </h5>
                    <button type="button" class="btn-close-custom" onclick="tutupMateriModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Mode Pelajari Materi -->
                    <div id="modePelajari" class="mode-pelajari">
                        <!-- Content -->
                        <div id="modalIsi" class="mb-3 materi-content">
                            <!-- Content will be loaded here -->
                        </div>
                        
                        <!-- Simulasi Section -->
                        <div id="modalSimulasiSection" style="margin-top: 2rem;">
                            <div class="text-center">
                                <button id="btnBukaSimulasi" class="btn simulasikan-btn">
                                    <i class="fas fa-play-circle me-2"></i>Coba Simulasi
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mode Simulasi -->
                    <div id="modeSimulasi" class="mode-simulasi">
                        <!-- Simulasi Container dengan tombol X -->
                        <div id="simulasiContainer" class="simulasi-container">
                            <!-- Tombol X untuk kembali ke materi -->
                            <div class="text-end mb-3">
                                <button type="button" class="btn-close-simulasi" onclick="kembaliKeMateri()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mb-3">Menyiapkan simulasi...</p>
                            <button class="btn btn-primary mt-2" onclick="bukaSimulasiTabBaru()">
                                <i class="fas fa-external-link-alt me-2"></i>Buka di Tab Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-4 py-3" style="background: linear-gradient(135deg, var(--biru-muda-1), var(--biru-muda-3));">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-dark mb-0 small">
                        &copy; 2026 MATHLine | Pendidikan Matematika | Universitas Negeri Medan
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
        // Variable untuk menyimpan data
        let currentMateriData = null;
        let currentSimulasiUrl = '';
        let currentSimulasiTitle = '';
        
        // Function to watch pertemuan video
        function tontonVideoPertemuan(videoUrl, pertemuanTitle) {
            try {
                document.getElementById('videoPertemuanTitle').textContent = pertemuanTitle;
                
                // Extract video ID from URL
                let videoId = '';
                if (videoUrl.includes('youtube.com/embed/')) {
                    videoId = videoUrl.split('embed/')[1];
                } else if (videoUrl.includes('youtube.com/watch?v=')) {
                    videoId = videoUrl.split('v=')[1].split('&')[0];
                } else if (videoUrl.includes('youtu.be/')) {
                    videoId = videoUrl.split('youtu.be/')[1].split('?')[0];
                } else {
                    videoId = videoUrl.split('/').pop();
                }
                
                // Set video source
                const videoFrame = document.getElementById('videoPertemuanFrame');
                videoFrame.src = `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&autoplay=0`;
                
                // Set description
                const description = document.getElementById('videoPertemuanDescription');
                let descText = '';
                
                if (pertemuanTitle.includes('Pertemuan 1')) {
                    descText = `
                        <h6 class="fw-bold mb-2">Persamaan Garis Lurus - Konsep Dasar</h6>
                        <p class="mb-2 small">Video ini membahas:</p>
                        <ul class="small">
                            <li>Pengertian persamaan garis lurus</li>
                            <li>Bentuk umum dan bentuk eksplisit</li>
                            <li>Cara menggambar grafik garis lurus</li>
                            <li>Contoh soal dan penerapan</li>
                        </ul>
                        <p class="mb-0 small"><strong>Durasi:</strong> ~15 menit</p>
                    `;
                } else if (pertemuanTitle.includes('Pertemuan 2')) {
                    descText = `
                        <h6 class="fw-bold mb-2">Gradien (Kemiringan) Garis Lurus</h6>
                        <p class="mb-2 small">Video ini membahas:</p>
                        <ul class="small">
                            <li>Konsep dasar gradien</li>
                            <li>Cara menghitung gradien dari dua titik</li>
                            <li>Jenis-jenis gradien (positif, negatif, nol)</li>
                            <li>Interpretasi gradien dalam kehidupan nyata</li>
                        </ul>
                        <p class="mb-0 small"><strong>Durasi:</strong> ~18 menit</p>
                    `;
                } else if (pertemuanTitle.includes('Pertemuan 3')) {
                    descText = `
                        <h6 class="fw-bold mb-2">Garis Sejajar dan Tegak Lurus</h6>
                        <p class="mb-2 small">Video ini membahas:</p>
                        <ul class="small">
                            <li>Syarat garis sejajar (m₁ = m₂)</li>
                            <li>Syarat garis tegak lurus (m₁ × m₂ = -1)</li>
                            <li>Cara menentukan persamaan garis sejajar/tegak lurus</li>
                            <li>Penerapan dalam desain dan arsitektur</li>
                        </ul>
                        <p class="mb-0 small"><strong>Durasi:</strong> ~20 menit</p>
                    `;
                }
                
                description.innerHTML = descText;
                
                // Set simulasi list
                const simulasiList = document.getElementById('simulasiList');
                simulasiList.innerHTML = '';
                
                // Cari data pertemuan untuk simulasi
                <?php foreach ($pertemuan_data as $judul_pertemuan => $pertemuan): ?>
                    if ('<?php echo $judul_pertemuan; ?>' === pertemuanTitle) {
                        const materiData = <?php echo json_encode($pertemuan['materi']); ?>;
                        
                        // Tampilkan simulasi yang tersedia
                        let simulasiCount = 0;
                        materiData.forEach((materi, index) => {
                            if (materi.interaktif && materi.interaktif !== '#') {
                                simulasiCount++;
                                const simulasiCard = document.createElement('div');
                                simulasiCard.className = 'simulasi-card';
                                simulasiCard.innerHTML = `
                                    <div class="d-flex align-items-start">
                                        <div class="me-2 flex-shrink-0">
                                            <i class="${materi.icon} text-primary fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1 small">${materi.title}</h6>
                                            <button class="btn btn-sm btn-outline-primary w-100" 
                                                    onclick="bukaSimulasi('${materi.interaktif}', '${materi.title.replace(/'/g, "\\'")}')">
                                                <i class="fas fa-play me-1"></i>Coba
                                            </button>
                                        </div>
                                    </div>
                                `;
                                simulasiList.appendChild(simulasiCard);
                            }
                        });
                        
                        if (simulasiCount === 0) {
                            simulasiList.innerHTML = '<p class="text-muted small mb-0">Tidak ada simulasi untuk pertemuan ini.</p>';
                        }
                    }
                <?php endforeach; ?>
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('videoPertemuanModal'));
                modal.show();
                
            } catch (error) {
                console.error('Error opening video:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memuat video. Silakan coba lagi.',
                    confirmButtonColor: '#1976d2'
                });
            }
        }
        
        // Function to open materi modal
        function bukaMateri(materi, judulPertemuan) {
            try {
                // Simpan data materi
                currentMateriData = materi;
                
                // Set modal title
                document.getElementById('modalJudul').textContent = materi.title;
                
                // Set content
                document.getElementById('modalIsi').innerHTML = `
                    <div class="materi-content">
                        ${materi.content}
                    </div>
                `;
                
                // Handle simulasi
                const simulasiSection = document.getElementById('modalSimulasiSection');
                const btnBukaSimulasi = document.getElementById('btnBukaSimulasi');
                if (materi.interaktif && materi.interaktif !== '#') {
                    simulasiSection.style.display = 'block';
                    currentSimulasiUrl = materi.interaktif;
                    currentSimulasiTitle = materi.title;
                    btnBukaSimulasi.onclick = function() {
                        bukaSimulasiDariModal();
                    };
                } else {
                    simulasiSection.style.display = 'none';
                    currentSimulasiUrl = '';
                }
                
                // Set ke mode pelajari
                setMode('pelajari');
                
                // Show modal menggunakan Bootstrap modal API
                const modalElement = document.getElementById('materiModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                
            } catch (error) {
                console.error('Error opening materi:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memuat materi. Silakan coba lagi.',
                    confirmButtonColor: '#1976d2'
                });
            }
        }
        
        // Function untuk kembali ke mode pelajari materi dari simulasi
        function kembaliKeMateri() {
            // Ganti ke mode pelajari
            setMode('pelajari');
            
            // Update title kembali ke judul materi
            if (currentMateriData) {
                document.getElementById('modalJudul').textContent = currentMateriData.title;
                document.getElementById('modalTitle').innerHTML = `
                    <i class="fas fa-book me-2"></i>
                    <span id="modalJudul">${currentMateriData.title}</span>
                `;
            }
            
            // Tampilkan notifikasi
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Kembali ke materi'
            });
        }
        
        // Function to open simulasi dari luar
        function bukaSimulasi(simulasiUrl, judulMateri) {
            if (simulasiUrl === '#') {
                Swal.fire({
                    icon: 'info',
                    title: 'Simulasi Tidak Tersedia',
                    text: 'Simulasi untuk materi ini sedang dalam pengembangan.',
                    confirmButtonColor: '#1976d2'
                });
                return;
            }
            
            // Buka di tab baru
            window.open(simulasiUrl, '_blank');
            
            // Tampilkan notifikasi
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Membuka simulasi di tab baru'
            });
        }
        
        // Function to open simulasi dari dalam modal
        function bukaSimulasiDariModal() {
            if (!currentSimulasiUrl || currentSimulasiUrl === '#') {
                Swal.fire({
                    icon: 'info',
                    title: 'Simulasi Tidak Tersedia',
                    text: 'Simulasi untuk materi ini sedang dalam pengembangan.',
                    confirmButtonColor: '#1976d2'
                });
                return;
            }
            
            // Ganti ke mode simulasi
            setMode('simulasi');
            
            // Update title untuk mode simulasi
            document.getElementById('modalJudul').textContent = `Simulasi: ${currentSimulasiTitle}`;
            document.getElementById('modalTitle').innerHTML = `
                <i class="fas fa-play-circle me-2 text-success"></i>
                <span id="modalJudul">Simulasi: ${currentSimulasiTitle}</span>
            `;
            
            // Tampilkan notifikasi bahwa simulasi dibuka di tab baru
            setTimeout(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                
                Toast.fire({
                    icon: 'info',
                    title: 'Tombol X untuk kembali ke materi'
                });
            }, 500);
        }
        
        // Function untuk membuka simulasi di tab baru dari modal
        function bukaSimulasiTabBaru() {
            if (!currentSimulasiUrl) return;
            
            window.open(currentSimulasiUrl, '_blank');
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Simulasi dibuka di tab baru'
            });
        }
        
        // Function untuk set mode (pelajari/simulasi)
        function setMode(mode) {
            const modePelajari = document.getElementById('modePelajari');
            const modeSimulasi = document.getElementById('modeSimulasi');
            
            if (mode === 'pelajari') {
                modePelajari.style.display = 'block';
                modeSimulasi.style.display = 'none';
            } else if (mode === 'simulasi') {
                modePelajari.style.display = 'none';
                modeSimulasi.style.display = 'block';
            }
        }
        
        // Function untuk tutup modal video dengan tombol X
        function tutupVideoModal() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('videoPertemuanModal'));
            if (modal) {
                const videoFrame = document.getElementById('videoPertemuanFrame');
                videoFrame.src = '';
                modal.hide();
            }
        }
        
        // Function untuk tutup modal materi dengan tombol X
        function tutupMateriModal() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('materiModal'));
            if (modal) {
                // Reset modal ke mode pelajari
                setMode('pelajari');
                document.getElementById('modalJudul').textContent = 'Materi';
                document.getElementById('modalTitle').innerHTML = `
                    <i class="fas fa-book me-2"></i>
                    <span id="modalJudul">Materi</span>
                `;
                modal.hide();
            }
        }
        
        // Cleanup modals when closed
        document.getElementById('videoPertemuanModal').addEventListener('hidden.bs.modal', function () {
            const videoFrame = document.getElementById('videoPertemuanFrame');
            videoFrame.src = '';
        });
        
        document.getElementById('materiModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalIsi').innerHTML = '';
            currentMateriData = null;
            currentSimulasiUrl = '';
            setMode('pelajari');
            document.getElementById('modalJudul').textContent = 'Materi';
            document.getElementById('modalTitle').innerHTML = `
                <i class="fas fa-book me-2"></i>
                <span id="modalJudul">Materi</span>
            `;
        });
        
        // Handle mobile orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    bootstrap.Modal.getInstance(modal).handleUpdate();
                });
            }, 300);
        });
        
        // Prevent zoom on double-tap on mobile
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>