<?php
// student/simulasi_sejajar.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Garis Sejajar - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --biru-utama: #1976d2;
            --hijau-utama: #4caf50;
            --oranye: #ff9800;
            --mobile-breakpoint: 768px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }
        
        /* Header Responsive */
        .simulasi-header {
            background: linear-gradient(135deg, var(--biru-utama), #0d47a1);
            color: white;
            padding: 1rem 0;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(25, 118, 210, 0.2);
            position: relative;
        }
        
        @media (min-width: 768px) {
            .simulasi-header {
                padding: 1.5rem 0;
                border-radius: 0 0 20px 20px;
            }
        }
        
        .simulasi-header h1 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        @media (min-width: 576px) {
            .simulasi-header h1 {
                font-size: 1.3rem;
            }
        }
        
        @media (min-width: 768px) {
            .simulasi-header h1 {
                font-size: 1.5rem;
            }
        }
        
        .simulasi-header p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        @media (min-width: 768px) {
            .simulasi-header p {
                font-size: 0.9rem;
            }
        }
        
        /* Tombol Kembali Responsive */
        .back-btn {
            background: white !important;
            color: var(--biru-utama) !important;
            border: none !important;
            min-height: 40px;
            min-width: 40px;
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none !important;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }
        
        @media (max-width: 768px) {
            .back-btn {
                font-size: 0.8rem;
                padding: 0.5rem;
            }
            
            .back-btn span {
                display: inline;
            }
        }
        
        @media (max-width: 576px) {
            .back-btn span {
                display: none;
            }
        }
        
        /* Main Container Responsive */
        .simulasi-container {
            max-width: 1400px;
            margin: 1rem auto;
            padding: 0 15px;
            width: 100%;
        }
        
        @media (min-width: 768px) {
            .simulasi-container {
                margin: 1.5rem auto;
                padding: 0 20px;
            }
        }
        
        /* Cards Responsive */
        .simulasi-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: none;
            border-left: 4px solid var(--biru-utama);
        }
        
        .simulasi-body {
            padding: 1.25rem;
        }
        
        @media (min-width: 768px) {
            .simulasi-body {
                padding: 1.5rem;
            }
        }
        
        @media (min-width: 992px) {
            .simulasi-body {
                padding: 2rem;
            }
        }
        
        /* Grafik Container Responsive */
        #sejajar-container {
            width: 100%;
            height: 300px;
            background: white;
            border-radius: 10px;
            border: 2px solid #bbdefb;
            margin-bottom: 1rem;
        }
        
        @media (min-width: 576px) {
            #sejajar-container {
                height: 350px;
            }
        }
        
        @media (min-width: 768px) {
            #sejajar-container {
                height: 400px;
            }
        }
        
        @media (min-width: 992px) {
            #sejajar-container {
                height: 450px;
            }
        }
        
        @media (min-width: 1200px) {
            #sejajar-container {
                height: 500px;
            }
        }
        
        /* Control Panel Responsive */
        .control-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .control-panel {
                padding: 1.5rem;
            }
        }
        
        /* Prinsip Box Responsive */
        .prinsip-box {
            background: #e8f5e9;
            border: 2px solid var(--hijau-utama);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .prinsip-box h6 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .prinsip-box p {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        @media (min-width: 768px) {
            .prinsip-box {
                padding: 1.25rem;
            }
            
            .prinsip-box h6 {
                font-size: 1rem;
            }
            
            .prinsip-box p {
                font-size: 0.85rem;
            }
        }
        
        /* Buttons Responsive */
        .btn-biru {
            background: linear-gradient(135deg, var(--biru-utama), #1565c0);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            min-height: 44px;
            width: 100%;
            margin-bottom: 0.75rem;
        }
        
        .btn-biru:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
        .btn-outline-secondary {
            min-height: 44px;
            font-size: 0.9rem;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .btn-biru, .btn-outline-secondary {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
        }
        
        /* Gradien Display Responsive */
        .gradien-display {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--biru-utama);
            text-align: center;
            margin: 1rem 0;
        }
        
        @media (min-width: 576px) {
            .gradien-display {
                font-size: 2rem;
            }
        }
        
        @media (min-width: 768px) {
            .gradien-display {
                font-size: 2.5rem;
            }
        }
        
        /* Persamaan Box Responsive */
        .persamaan-box {
            background: #fff3e0;
            border-radius: 10px;
            padding: 0.75rem;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            text-align: center;
            font-size: 0.9rem;
            word-break: break-word;
        }
        
        @media (min-width: 768px) {
            .persamaan-box {
                padding: 1rem;
                font-size: 1rem;
            }
        }
        
        /* Form Controls Responsive */
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .form-range {
            padding: 0.5rem 0;
        }
        
        /* Slider Labels Responsive */
        .slider-labels {
            font-size: 0.75rem;
            color: #666;
        }
        
        /* Jarak Info Responsive */
        .jarak-info {
            background: #f3e5f5;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .jarak-info h6 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        @media (min-width: 768px) {
            .jarak-info {
                padding: 1.25rem;
            }
            
            .jarak-info h6 {
                font-size: 1rem;
            }
        }
        
        /* Latihan Card Responsive */
        .latihan-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            padding: 1rem;
            height: 100%;
        }
        
        .latihan-card h6 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .latihan-card p {
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        @media (min-width: 768px) {
            .latihan-card {
                padding: 1.25rem;
            }
            
            .latihan-card h6 {
                font-size: 1rem;
            }
            
            .latihan-card p {
                font-size: 0.85rem;
            }
        }
        
        /* Status Icon Responsive */
        #status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            #status-icon {
                font-size: 2.5rem;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Touch Optimizations */
        button, .btn, input[type="range"] {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        input[type="range"] {
            width: 100%;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--biru-utama);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #1565c0;
        }
        
        /* Responsive Grid Layout */
        @media (max-width: 767px) {
            .row-cols-md-2 > * {
                margin-bottom: 1rem;
            }
        }
        
        /* Safe Area untuk Mobile */
        @media (max-width: 768px) {
            body {
                padding-bottom: 20px;
            }
            
            .simulasi-header {
                margin-top: env(safe-area-inset-top, 0);
                padding-top: calc(1rem + env(safe-area-inset-top, 0));
            }
        }
        
        /* HP Sangat Kecil */
        @media (max-width: 375px) {
            .simulasi-body {
                padding: 1rem;
            }
            
            #sejajar-container {
                height: 280px;
            }
            
            .gradien-display {
                font-size: 1.6rem;
            }
            
            .control-panel {
                padding: 1rem;
            }
            
            .persamaan-box {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            #sejajar-container {
                height: 250px;
            }
            
            .simulasi-header {
                padding: 0.5rem 0;
            }
            
            .simulasi-header h1 {
                font-size: 1rem;
                margin-bottom: 0;
            }
            
            .simulasi-header p {
                display: none;
            }
            
            .gradien-display {
                font-size: 1.5rem;
                margin: 0.5rem 0;
            }
        }
        
        /* Tablet Landscape */
        @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
            #sejajar-container {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="simulasi-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-8 col-md-6">
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-grip-lines me-2"></i>Simulasi Garis Sejajar
                    </h1>
                    <p class="mb-0 opacity-90">
                        Pelajari sifat garis sejajar: m₁ = m₂
                    </p>
                </div>
                <div class="col-4 col-md-6 text-end">
                    <a href="materials.php" class="btn back-btn">
                        <i class="fas fa-arrow-left me-1"></i><span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mb-0 small">Menyiapkan simulasi...</p>
        </div>
    </div>
    
    <!-- Simulasi Container -->
    <div class="container simulasi-container">
        <div class="row">
            <!-- Grafik Section -->
            <div class="col-lg-8">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-project-diagram me-2"></i>Visualisasi Garis Sejajar
                        </h4>
                        
                        <!-- Sejajar Container -->
                        <div id="sejajar-container"></div>
                        
                        <!-- Prinsip Garis Sejajar -->
                        <div class="prinsip-box mt-3">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h6 class="fw-bold">
                                        <i class="fas fa-ruler-combined me-2"></i>Sifat Garis Sejajar
                                    </h6>
                                    <p class="mb-0 small">
                                        Dua garis dikatakan sejajar jika memiliki gradien yang sama.
                                        <br>Rumus: <strong>m₁ = m₂</strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">
                                        <i class="fas fa-info-circle me-2"></i>Karakteristik
                                    </h6>
                                    <p class="mb-0 small">
                                        • Tidak pernah berpotongan<br>
                                        • Jarak antar garis selalu sama<br>
                                        • Memiliki kemiringan identik
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Section -->
            <div class="col-lg-4">
                <div class="control-panel fade-in">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-sliders-h me-2"></i>Kontrol Garis
                    </h5>
                    
                    <!-- Garis Utama -->
                    <div class="mb-3">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-line me-2"></i>Garis Utama
                        </h6>
                        <div class="mb-3">
                            <label class="form-label small">Gradien (m₁)</label>
                            <input type="range" class="form-range" id="m1-slider" 
                                   min="-3" max="3" step="0.5" value="1">
                            <div class="d-flex justify-content-between slider-labels">
                                <small>-3</small>
                                <small>Kemiringan</small>
                                <small>3</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Intercept (c₁)</label>
                            <input type="range" class="form-range" id="c1-slider" 
                                   min="-5" max="5" step="1" value="0">
                            <div class="d-flex justify-content-between slider-labels">
                                <small>-5</small>
                                <small>Titik Potong</small>
                                <small>5</small>
                            </div>
                        </div>
                        <div class="persamaan-box">
                            <span id="persamaan-utama">y = 1.0x + 0</span>
                        </div>
                    </div>
                    
                    <!-- Garis Sejajar -->
                    <div class="mb-3">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-grip-lines me-2"></i>Garis Sejajar
                        </h6>
                        <div class="mb-3">
                            <label class="form-label small">Intercept (c₂)</label>
                            <input type="range" class="form-range" id="c2-slider" 
                                   min="-5" max="5" step="1" value="2">
                            <div class="d-flex justify-content-between slider-labels">
                                <small>-5</small>
                                <small>Jarak dari garis utama</small>
                                <small>5</small>
                            </div>
                        </div>
                        <div class="persamaan-box">
                            <span id="persamaan-sejajar">y = 1.0x + 2</span>
                        </div>
                    </div>
                    
                    <!-- Nilai Gradien -->
                    <div class="text-center mb-3">
                        <div class="gradien-display">
                            m₁ = m₂ = <span id="gradien-value">1.0</span>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="mt-3">
                        <button class="btn btn-biru mb-2" id="update-btn">
                            <i class="fas fa-sync me-2"></i>Update Garis
                        </button>
                        <button class="btn btn-outline-secondary" id="reset-btn">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Jarak -->
        <div class="row">
            <div class="col-12">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-ruler me-2"></i>Jarak Antar Garis Sejajar
                        </h5>
                        
                        <div class="jarak-info">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-calculator me-2"></i>Perhitungan Jarak
                                    </h6>
                                    <p class="mb-2 small">
                                        Jarak vertikal antar garis sejajar:
                                        <br><strong>Δc = |c₂ - c₁|</strong>
                                    </p>
                                    <div class="persamaan-box">
                                        Δc = |<span id="c2-value">2</span> - <span id="c1-value">0</span>| = 
                                        <span id="delta-c">2</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-chart-line me-2"></i>Persamaan Garis
                                    </h6>
                                    <div class="text-center">
                                        <p class="mb-1 small">Garis 1: y = <span id="eq1">1.0x + 0</span></p>
                                        <p class="mb-1 small">Garis 2: y = <span id="eq2">1.0x + 2</span></p>
                                        <p class="mb-0 small">Jarak: <span id="jarak-text" class="fw-bold" style="color: var(--oranye);">2 unit</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- Footer -->
        <div class="text-center mt-4 mb-4">
            <p class="text-muted small">
                <i class="fas fa-graduation-cap me-1"></i>
                MATHLine - Pendidikan Matematika | Universitas Negeri Medan
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Data garis
        let m1 = 1.0;
        let c1 = 0;
        let m2 = 1.0;
        let c2 = 2;
        
        // Show loading spinner
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }
        
        // Hide loading spinner
        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }
        
        // Fungsi untuk update ukuran grafik berdasarkan layar
        function getGrafikHeight() {
            const width = window.innerWidth;
            if (width < 576) return 280;  // HP kecil
            if (width < 768) return 320;  // HP besar/tablet kecil
            if (width < 992) return 380;  // Tablet
            if (width < 1200) return 420; // Laptop kecil
            return 500; // Desktop besar
        }
        
        // Update grafik dengan visualisasi yang lebih rapi
        function updateGrafik() {
            // Tampilkan loading
            showLoading();
            
            // Generate data untuk garis utama
            const x1 = [];
            const y1 = [];
            for (let i = -10; i <= 10; i += 0.2) {
                x1.push(i);
                y1.push(m1 * i + c1);
            }
            
            // Generate data untuk garis sejajar
            const x2 = [];
            const y2 = [];
            for (let i = -10; i <= 10; i += 0.2) {
                x2.push(i);
                y2.push(m2 * i + c2);
            }
            
            // Data untuk garis utama (lebih tebal)
            const garis1Trace = {
                x: x1,
                y: y1,
                mode: 'lines',
                type: 'scatter',
                name: `Garis Utama: y = ${m1.toFixed(1)}x + ${c1}`,
                line: {
                    color: '#1976d2',
                    width: 4
                },
                hoverinfo: 'name'
            };
            
            // Data untuk garis sejajar
            const garis2Trace = {
                x: x2,
                y: y2,
                mode: 'lines',
                type: 'scatter',
                name: `Garis Sejajar: y = ${m2.toFixed(1)}x + ${c2}`,
                line: {
                    color: '#4caf50',
                    width: 3
                },
                hoverinfo: 'name'
            };
            
            // Gabungkan semua traces
            const data = [garis1Trace, garis2Trace];
            
            // Layout responsif
            const layout = {
                title: {
                    text: `Garis Sejajar: m₁ = m₂ = ${m1.toFixed(1)}`,
                    font: { 
                        size: 14,
                        family: 'Segoe UI, sans-serif'
                    }
                },
                xaxis: {
                    title: {
                        text: 'Sumbu X',
                        font: { size: 12 }
                    },
                    range: [-10, 10],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    zerolinewidth: 2,
                    showgrid: true
                },
                yaxis: {
                    title: {
                        text: 'Sumbu Y',
                        font: { size: 12 }
                    },
                    range: [-10, 10],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    zerolinewidth: 2,
                    showgrid: true
                },
                plot_bgcolor: '#f8f9fa',
                paper_bgcolor: '#fff',
                showlegend: true,
                legend: {
                    x: 0.02,
                    y: 1.1,
                    font: { size: 11 },
                    bgcolor: 'rgba(255,255,255,0.8)'
                },
                shapes: [
                    // Garis bantu untuk menunjukkan jarak di x = 0
                    {
                        type: 'line',
                        x0: 0,
                        y0: c1,
                        x1: 0,
                        y1: c2,
                        line: {
                            color: '#ff9800',
                            width: 2,
                            dash: 'dash'
                        }
                    },
                    // Garis bantu untuk menunjukkan jarak di x = 7
                    {
                        type: 'line',
                        x0: 7,
                        y0: m1 * 7 + c1,
                        x1: 7,
                        y1: m2 * 7 + c2,
                        line: {
                            color: '#ff9800',
                            width: 2,
                            dash: 'dash'
                        }
                    }
                ],
                annotations: [
                    // Label Δc di x = 0
                    {
                        x: 0,
                        y: (c1 + c2) / 2,
                        text: `Δc = ${Math.abs(c2 - c1)}`,
                        showarrow: true,
                        arrowhead: 2,
                        arrowsize: 1,
                        arrowwidth: 2,
                        arrowcolor: '#ff9800',
                        ax: 20,
                        ay: 0,
                        font: {
                            size: 12,
                            color: '#ff9800',
                            family: 'Arial, sans-serif'
                        },
                        bgcolor: 'rgba(255,255,255,0.9)',
                        bordercolor: '#ff9800',
                        borderwidth: 1,
                        borderpad: 4
                    },
                    // Persamaan garis utama
                    {
                        x: -8,
                        y: m1 * (-8) + c1,
                        text: `y = ${m1.toFixed(1)}x + ${c1}`,
                        showarrow: false,
                        font: {
                            size: 11,
                            color: '#1976d2',
                            family: 'Courier New, monospace'
                        },
                        bgcolor: 'rgba(255,255,255,0.8)',
                        borderpad: 4
                    },
                    // Persamaan garis sejajar
                    {
                        x: -8,
                        y: m2 * (-8) + c2,
                        text: `y = ${m2.toFixed(1)}x + ${c2}`,
                        showarrow: false,
                        font: {
                            size: 11,
                            color: '#4caf50',
                            family: 'Courier New, monospace'
                        },
                        bgcolor: 'rgba(255,255,255,0.8)',
                        borderpad: 4
                    }
                ]
            };
            
            // Config untuk responsif
            const config = {
                responsive: true,
                displayModeBar: true,
                displaylogo: false,
                modeBarButtonsToRemove: ['select2d', 'lasso2d', 'toggleSpikelines'],
                scrollZoom: true,
                doubleClick: 'reset'
            };
            
            // Render grafik
            Plotly.react('sejajar-container', data, layout, config).then(() => {
                hideLoading();
            });
            
            // Update nilai-nilai
            updateNilai();
        }
        
        // Update nilai-nilai
        function updateNilai() {
            // Update persamaan
            document.getElementById('persamaan-utama').textContent = `y = ${m1.toFixed(1)}x + ${c1}`;
            document.getElementById('persamaan-sejajar').textContent = `y = ${m2.toFixed(1)}x + ${c2}`;
            
            // Update gradien value
            document.getElementById('gradien-value').textContent = m1.toFixed(1);
            
            // Update jarak info
            document.getElementById('c1-value').textContent = c1;
            document.getElementById('c2-value').textContent = c2;
            document.getElementById('delta-c').textContent = Math.abs(c2 - c1);
            document.getElementById('eq1').textContent = `${m1.toFixed(1)}x + ${c1}`;
            document.getElementById('eq2').textContent = `${m2.toFixed(1)}x + ${c2}`;
            document.getElementById('jarak-text').textContent = `${Math.abs(c2 - c1)} unit`;
        }
        
        // Event listeners untuk slider
        document.getElementById('m1-slider').addEventListener('input', function(e) {
            m1 = parseFloat(e.target.value);
            m2 = m1; // Garis sejajar memiliki gradien sama
            updateGrafik();
        });
        
        document.getElementById('c1-slider').addEventListener('input', function(e) {
            c1 = parseInt(e.target.value);
            updateGrafik();
        });
        
        document.getElementById('c2-slider').addEventListener('input', function(e) {
            c2 = parseInt(e.target.value);
            updateGrafik();
        });
        
        // Update button
        document.getElementById('update-btn').addEventListener('click', function() {
            updateGrafik();
            showToast('Garis diperbarui');
        });
        
        // Reset button
        document.getElementById('reset-btn').addEventListener('click', function() {
            m1 = 1.0;
            c1 = 0;
            m2 = 1.0;
            c2 = 2;
            updateGrafik();
            showToast('Reset ke nilai default');
        });
        
        // Cek apakah sejajar
        function checkSejajar() {
            const userM = parseFloat(document.getElementById('challenge-m').value);
            const userC = parseFloat(document.getElementById('challenge-c').value);
            
            const statusIcon = document.getElementById('status-icon');
            const statusText = document.getElementById('status-text');
            
            if (Math.abs(userM - m1) < 0.01) { // Toleransi kecil
                statusIcon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                statusText.innerHTML = `
                    <span class="text-success fw-bold">Benar! Garis sejajar</span><br>
                    <small>m₁ = ${m1.toFixed(1)}, m₂ = ${userM.toFixed(1)}</small>
                `;
                showToast('Benar! Garis sejajar', 'success');
            } else {
                statusIcon.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                statusText.innerHTML = `
                    <span class="text-danger fw-bold">Salah! Garis tidak sejajar</span><br>
                    <small>m₁ = ${m1.toFixed(1)}, m₂ = ${userM.toFixed(1)}</small>
                `;
                showToast('Salah! Gradien tidak sama', 'danger');
            }
        }
        
        // Toast notification
        function showToast(message, type = 'info') {
            // Hapus toast sebelumnya jika ada
            const oldToast = document.querySelector('.custom-toast');
            if (oldToast) oldToast.remove();
            
            // Tentukan warna berdasarkan type
            let bgColor = '#1976d2'; // default primary
            if (type === 'success') bgColor = '#4CAF50';
            if (type === 'danger') bgColor = '#f44336';
            
            // Buat element toast
            const toast = document.createElement('div');
            toast.className = 'custom-toast';
            toast.innerHTML = `
                <div style="
                    position: fixed;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, ${bgColor}, ${bgColor}99);
                    color: white;
                    padding: 12px 20px;
                    border-radius: 25px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    z-index: 10000;
                    animation: fadeInUp 0.3s ease-out;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                    display: flex;
                    align-items: center;
                    min-width: 200px;
                    max-width: 90%;
                    justify-content: center;
                    text-align: center;
                ">
                    <i class="fas fa-info-circle me-2"></i>${message}
                </div>
            `;
            document.body.appendChild(toast);
            
            // Hapus setelah 3 detik
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(-50%) translateY(20px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const height = getGrafikHeight();
                document.getElementById('sejajar-container').style.height = height + 'px';
                Plotly.relayout('sejajar-container', {
                    height: height
                });
            }, 250);
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial height
            const initialHeight = getGrafikHeight();
            document.getElementById('sejajar-container').style.height = initialHeight + 'px';
            
            // Inisialisasi grafik
            updateGrafik();
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Perbesar input untuk touch
                document.querySelectorAll('input').forEach(input => {
                    input.style.minHeight = '44px';
                    if (input.type === 'number') {
                        input.style.fontSize = '16px'; // Mencegah zoom di iOS
                    }
                });
            }
        });
        
        // CSS untuk animasi toast
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }
            
            .touch-device .btn {
                padding: 12px;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>