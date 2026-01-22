<?php
// student/simulasi_dua_titik.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole('siswa');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Gradien dari Dua Titik - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --biru-utama: #1976d2;
            --biru-muda: #bbdefb;
            --hijau-utama: #4caf50;
            --hijau-muda: #c8e6c9;
            --oranye-utama: #ff9800;
            --oranye-muda: #ffe0b2;
            --mobile-breakpoint: 768px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e3f2fd 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            padding-bottom: 20px;
        }
        
        /* Header Responsive */
        .simulasi-header {
            background: linear-gradient(135deg, var(--biru-utama), #0d47a1);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
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
            .simulasi-header {
                padding: 1.2rem 0;
            }
            
            .simulasi-header h1 {
                font-size: 1.4rem;
            }
        }
        
        .simulasi-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Tombol Kembali */
        .back-btn {
            background: linear-gradient(135deg, var(--hijau-utama), #2e7d32) !important;
            color: white !important;
            border: none !important;
            min-height: 40px;
            min-width: 40px;
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none !important;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }
        
        @media (max-width: 768px) {
            .back-btn {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .back-btn span {
                display: none;
            }
        }
        
        /* Main Container */
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
        
        /* Cards */
        .simulasi-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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
        
        /* Grafik Container Responsive */
        #grafik-container {
            width: 100%;
            height: 300px;
            background: white;
            border-radius: 8px;
            border: 2px solid var(--biru-muda);
            margin-bottom: 1rem;
        }
        
        @media (min-width: 576px) {
            #grafik-container {
                height: 350px;
            }
        }
        
        @media (min-width: 768px) {
            #grafik-container {
                height: 400px;
            }
        }
        
        @media (min-width: 992px) {
            #grafik-container {
                height: 450px;
            }
        }
        
        /* Input Controls */
        .titik-control {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.25rem;
            border: 2px solid #e9ecef;
            margin-bottom: 1rem;
        }
        
        .titik-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--biru-utama);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .titik-label i {
            margin-right: 8px;
        }
        
        .input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        @media (max-width: 576px) {
            .input-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .input-group-custom {
            margin-bottom: 0.5rem;
        }
        
        .input-group-custom label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            display: block;
            color: #495057;
        }
        
        .input-group-custom input {
            width: 100%;
            padding: 0.375rem 0.75rem;
            border: 2px solid #ced4da;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s;
            min-height: 40px;
        }
        
        .input-group-custom input:focus {
            border-color: var(--biru-utama);
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
            outline: none;
        }
        
        /* Gradien Result */
        .gradien-result {
            background: linear-gradient(135deg, var(--hijau-muda), #a5d6a7);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin: 1rem 0;
            border: 2px solid var(--hijau-utama);
        }
        
        .gradien-formula {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #1b5e20;
            background: white;
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px dashed var(--hijau-utama);
        }
        
        @media (min-width: 768px) {
            .gradien-formula {
                font-size: 1.2rem;
            }
        }
        
        .gradien-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1b5e20;
            margin: 0.5rem 0;
        }
        
        @media (min-width: 768px) {
            .gradien-value {
                font-size: 3.5rem;
            }
        }
        
        .gradien-info {
            font-size: 0.9rem;
            color: #2e7d32;
            margin-top: 0.5rem;
        }
        
        /* Step by Step */
        .step-container {
            background: #f3e5f5;
            border-radius: 10px;
            padding: 1.25rem;
            border: 2px solid #7b1fa2;
            margin: 1rem 0;
        }
        
        .step-title {
            font-size: 1rem;
            font-weight: 600;
            color: #7b1fa2;
            margin-bottom: 1rem;
        }
        
        .step-list {
            padding-left: 1.5rem;
        }
        
        .step-item {
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            color: #6a1b9a;
            line-height: 1.4;
        }
        
        .step-item strong {
            color: #4a148c;
        }
        
        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--biru-utama), #1565c0);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            min-height: 44px;
            width: 100%;
            margin-bottom: 0.75rem;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
        .btn-outline-custom {
            min-height: 44px;
            font-size: 0.9rem;
            border: 2px solid var(--oranye-utama);
            color: var(--oranye-utama);
            width: 100%;
        }
        
        .btn-outline-custom:hover {
            background-color: var(--oranye-utama);
            color: white;
        }
        
        @media (max-width: 768px) {
            .btn-primary-custom, .btn-outline-custom {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
        }
        
        /* Contoh Titik */
        .contoh-container {
            margin: 1.5rem 0;
        }
        
        .contoh-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--oranye-utama);
            margin-bottom: 1rem;
        }
        
        .contoh-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        @media (max-width: 768px) {
            .contoh-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .contoh-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .contoh-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--biru-utama);
        }
        
        .contoh-item h6 {
            font-size: 0.9rem;
            color: var(--biru-utama);
            margin-bottom: 0.5rem;
        }
        
        .contoh-item p {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .contoh-item .gradien-kecil {
            font-size: 0.75rem;
            color: #4caf50;
            font-weight: 600;
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
        button, .btn, .contoh-item {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Footer */
        .simulasi-footer {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
            color: #2d3748;
            font-size: 0.9rem;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="simulasi-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-9 col-md-10">
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-calculator me-2"></i>Simulasi Gradien dari Dua Titik
                    </h1>
                    <p class="mb-0 opacity-90">
                        Hitung gradien menggunakan rumus m = (y₂ - y₁) / (x₂ - x₁)
                    </p>
                </div>
                <div class="col-3 col-md-2 text-end">
                    <a href="view_materials.php" class="btn back-btn">
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
    
    <!-- Main Content -->
    <div class="container simulasi-container">
        <div class="row">
            <!-- Grafik Section -->
            <div class="col-lg-8">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Visualisasi Dua Titik dan Garis
                        </h5>
                        
                        <!-- Grafik Container -->
                        <div id="grafik-container"></div>
                        
                        <!-- Step by Step Calculation -->
                        <div class="step-container mt-3">
                            <h6 class="step-title">
                                <i class="fas fa-list-ol me-2"></i>Langkah Perhitungan
                            </h6>
                            <div id="step-calculation">
                                <ol class="step-list">
                                    <li class="step-item">Tentukan titik A(x₁, y₁) dan titik B(x₂, y₂)</li>
                                    <li class="step-item">Hitung Δy = y₂ - y₁</li>
                                    <li class="step-item">Hitung Δx = x₂ - x₁</li>
                                    <li class="step-item">Bagi Δy dengan Δx: m = Δy / Δx</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Section -->
            <div class="col-lg-4">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-sliders-h me-2"></i>Input Titik Koordinat
                        </h5>
                        
                        <!-- Titik A -->
                        <div class="titik-control">
                            <div class="titik-label text-primary">
                                <i class="fas fa-circle"></i>Titik A (x₁, y₁)
                            </div>
                            <div class="input-grid">
                                <div class="input-group-custom">
                                    <label for="x1-input">Nilai x₁</label>
                                    <input type="number" id="x1-input" class="form-control" 
                                           min="-10" max="10" step="1" value="2">
                                </div>
                                <div class="input-group-custom">
                                    <label for="y1-input">Nilai y₁</label>
                                    <input type="number" id="y1-input" class="form-control" 
                                           min="-10" max="10" step="1" value="3">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Titik B -->
                        <div class="titik-control">
                            <div class="titik-label text-success">
                                <i class="fas fa-circle"></i>Titik B (x₂, y₂)
                            </div>
                            <div class="input-grid">
                                <div class="input-group-custom">
                                    <label for="x2-input">Nilai x₂</label>
                                    <input type="number" id="x2-input" class="form-control" 
                                           min="-10" max="10" step="1" value="6">
                                </div>
                                <div class="input-group-custom">
                                    <label for="y2-input">Nilai y₂</label>
                                    <input type="number" id="y2-input" class="form-control" 
                                           min="-10" max="10" step="1" value="7">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hasil Gradien -->
                        <div class="gradien-result mt-3">
                            <div class="gradien-formula">
                                m = <span id="formula-text">(7 - 3) / (6 - 2)</span>
                            </div>
                            <div class="gradien-value">
                                m = <span id="gradien-value">1.00</span>
                            </div>
                            <div id="gradien-kategori" class="badge bg-success fs-6 px-3 py-2">
                                Gradien Positif
                            </div>
                            <p id="gradien-interpretasi" class="gradien-info mt-2">
                                Setiap kenaikan 1 satuan horizontal, naik 1 satuan vertikal
                            </p>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="mt-3">
                            <button class="btn btn-primary-custom mb-2" id="hitung-btn">
                                <i class="fas fa-calculator me-2"></i>Hitung Gradien
                            </button>
                            <button class="btn btn-outline-custom" id="reset-btn">
                                <i class="fas fa-redo me-2"></i>Reset ke Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Tambahan -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i>Konsep Penting Gradien
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-ruler-combined me-2"></i>Rumus Gradien
                                    </h6>
                                    <p class="mb-2 small">Gradien (m) dihitung dengan:</p>
                                    <p class="mb-0 text-center font-monospace">
                                        m = Δy / Δx = (y₂ - y₁) / (x₂ - x₁)
                                    </p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                                    </h6>
                                    <p class="mb-0 small">
                                        • Jika Δx = 0, garis vertikal (gradien tak terdefinisi)<br>
                                        • Jika Δy = 0, garis horizontal (gradien = 0)<br>
                                        • Titik tidak boleh sama (x₁≠x₂ atau y₁≠y₂)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="simulasi-footer fade-in">
            <p class="mb-0">
                <i class="fas fa-graduation-cap me-1"></i>
                MATHLine - Pendidikan Matematika | Universitas Negeri Medan
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Data titik
        let titikA = { x: 2, y: 3 };
        let titikB = { x: 6, y: 7 };
        let gradien = 1.0;
        
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
            return 420; // Desktop
        }
        
        // Update grafik
        function updateGrafik() {
            // Tampilkan loading
            showLoading();
            
            // Hitung gradien
            const deltaY = titikB.y - titikA.y;
            const deltaX = titikB.x - titikA.x;
            gradien = deltaX !== 0 ? deltaY / deltaX : Infinity;
            
            // Data untuk titik
            const titikTrace = {
                x: [titikA.x, titikB.x],
                y: [titikA.y, titikB.y],
                mode: 'markers+text',
                type: 'scatter',
                name: 'Titik',
                marker: {
                    size: 14,
                    color: ['#1976d2', '#4caf50']
                },
                text: ['A', 'B'],
                textposition: 'top center',
                textfont: {
                    size: 16,
                    family: 'Arial, sans-serif',
                    weight: 'bold'
                },
                hoverinfo: 'x+y+text'
            };
            
            // Data untuk garis
            const garisTrace = {
                x: [titikA.x, titikB.x],
                y: [titikA.y, titikB.y],
                mode: 'lines',
                type: 'scatter',
                name: `Garis AB`,
                line: {
                    color: '#ff9800',
                    width: 3,
                    dash: 'solid'
                },
                hoverinfo: 'none'
            };
            
            // Data untuk segitiga gradien
            const segitigaTrace = {
                x: [titikA.x, titikB.x, titikB.x, titikA.x],
                y: [titikA.y, titikA.y, titikB.y, titikA.y],
                mode: 'lines',
                type: 'scatter',
                fill: 'toself',
                fillcolor: 'rgba(25, 118, 210, 0.1)',
                line: {
                    color: '#1976d2',
                    width: 1,
                    dash: 'dash'
                },
                name: 'Δx dan Δy',
                hoverinfo: 'none'
            };
            
            // Data untuk label delta
            const deltaAnnotations = [];
            
            if (deltaX !== 0) {
                deltaAnnotations.push({
                    x: (titikA.x + titikB.x) / 2,
                    y: titikA.y - 0.5,
                    text: `Δx = ${deltaX}`,
                    showarrow: false,
                    font: {
                        size: 12,
                        color: '#1976d2',
                        family: 'Arial, sans-serif'
                    },
                    bgcolor: 'rgba(255,255,255,0.8)',
                    bordercolor: '#1976d2',
                    borderwidth: 1,
                    borderpad: 4
                });
            }
            
            if (deltaY !== 0) {
                deltaAnnotations.push({
                    x: titikB.x + 0.5,
                    y: (titikA.y + titikB.y) / 2,
                    text: `Δy = ${deltaY}`,
                    showarrow: false,
                    font: {
                        size: 12,
                        color: '#4caf50',
                        family: 'Arial, sans-serif'
                    },
                    bgcolor: 'rgba(255,255,255,0.8)',
                    bordercolor: '#4caf50',
                    borderwidth: 1,
                    borderpad: 4
                });
            }
            
            // Gabungkan semua traces
            const data = [titikTrace, garisTrace, segitigaTrace];
            
            // Layout responsif
            const layout = {
                title: {
                    text: `Gradien dari A(${titikA.x},${titikA.y}) ke B(${titikB.x},${titikB.y})`,
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
                    x: 0,
                    y: 1.1,
                    font: { size: 11 },
                    bgcolor: 'rgba(255,255,255,0.8)'
                },
                margin: {
                    l: 60,
                    r: 30,
                    b: 60,
                    t: 60,
                    pad: 5
                },
                autosize: true,
                hovermode: 'closest',
                annotations: deltaAnnotations
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
            Plotly.react('grafik-container', data, layout, config).then(() => {
                hideLoading();
            });
            
            // Update nilai
            updateNilai();
        }
        
        // Update nilai-nilai
        function updateNilai() {
            const deltaY = titikB.y - titikA.y;
            const deltaX = titikB.x - titikA.x;
            
            // Update formula
            document.getElementById('formula-text').textContent = 
                `(${titikB.y} - ${titikA.y}) / (${titikB.x} - ${titikA.x})`;
            
            // Update gradien value
            document.getElementById('gradien-value').textContent = 
                deltaX !== 0 ? gradien.toFixed(2) : '∞';
            
            // Update step calculation
            const stepHTML = `
                <ol class="step-list">
                    <li class="step-item">Tentukan titik <strong>A(${titikA.x}, ${titikA.y})</strong> dan titik <strong>B(${titikB.x}, ${titikB.y})</strong></li>
                    <li class="step-item">Hitung Δy = y₂ - y₁ = <strong>${titikB.y} - ${titikA.y} = ${deltaY}</strong></li>
                    <li class="step-item">Hitung Δx = x₂ - x₁ = <strong>${titikB.x} - ${titikA.x} = ${deltaX}</strong></li>
                    <li class="step-item">Bagi Δy dengan Δx: m = Δy / Δx = <strong>${deltaY} / ${deltaX} = ${deltaX !== 0 ? gradien.toFixed(2) : '∞'}</strong></li>
                </ol>
            `;
            document.getElementById('step-calculation').innerHTML = stepHTML;
            
            // Update kategori gradien
            let interpretasi = '';
            if (deltaX === 0) {
                interpretasi = 'Garis vertikal - gradien tidak terdefinisi';
                document.getElementById('gradien-kategori').className = 'badge bg-info fs-6 px-3 py-2';
                document.getElementById('gradien-kategori').textContent = 'Tidak Terdefinisi';
            } else if (gradien > 0) {
                interpretasi = `Setiap kenaikan 1 satuan horizontal, naik ${gradien.toFixed(2)} satuan vertikal`;
                document.getElementById('gradien-kategori').className = 'badge bg-success fs-6 px-3 py-2';
                document.getElementById('gradien-kategori').textContent = 'Gradien Positif';
            } else if (gradien < 0) {
                interpretasi = `Setiap kenaikan 1 satuan horizontal, turun ${Math.abs(gradien).toFixed(2)} satuan vertikal`;
                document.getElementById('gradien-kategori').className = 'badge bg-danger fs-6 px-3 py-2';
                document.getElementById('gradien-kategori').textContent = 'Gradien Negatif';
            } else {
                interpretasi = 'Tidak ada perubahan vertikal - garis horizontal';
                document.getElementById('gradien-kategori').className = 'badge bg-warning fs-6 px-3 py-2';
                document.getElementById('gradien-kategori').textContent = 'Gradien Nol';
            }
            document.getElementById('gradien-interpretasi').textContent = interpretasi;
            
            // Tampilkan toast notifikasi
            showToast(`Gradien dihitung: ${deltaX !== 0 ? gradien.toFixed(2) : '∞'}`);
        }
        
        // Toast notification
        function showToast(message) {
            // Hapus toast sebelumnya jika ada
            const oldToast = document.querySelector('.custom-toast');
            if (oldToast) oldToast.remove();
            
            // Buat element toast
            const toast = document.createElement('div');
            toast.className = 'custom-toast';
            toast.innerHTML = `
                <div style="
                    position: fixed;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #1976d2, #0d47a1);
                    color: white;
                    padding: 12px 24px;
                    border-radius: 25px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    z-index: 10000;
                    animation: fadeInUp 0.3s ease-out;
                    box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
                    display: flex;
                    align-items: center;
                    min-width: 200px;
                    justify-content: center;
                ">
                    <i class="fas fa-calculator me-2"></i>${message}
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
        
        // Hitung gradien dari input
        document.getElementById('hitung-btn').addEventListener('click', function() {
            titikA.x = parseInt(document.getElementById('x1-input').value);
            titikA.y = parseInt(document.getElementById('y1-input').value);
            titikB.x = parseInt(document.getElementById('x2-input').value);
            titikB.y = parseInt(document.getElementById('y2-input').value);
            updateGrafik();
        });
        
        // Reset ke default
        document.getElementById('reset-btn').addEventListener('click', function() {
            titikA = { x: 2, y: 3 };
            titikB = { x: 6, y: 7 };
            document.getElementById('x1-input').value = titikA.x;
            document.getElementById('y1-input').value = titikA.y;
            document.getElementById('x2-input').value = titikB.x;
            document.getElementById('y2-input').value = titikB.y;
            updateGrafik();
            showToast('Reset ke nilai default');
        });
        
        // Contoh soal
        function setContoh(contoh) {
            switch(contoh) {
                case 'contoh1':
                    titikA = { x: 1, y: 2 };
                    titikB = { x: 4, y: 5 };
                    break;
                case 'contoh2':
                    titikA = { x: 2, y: 5 };
                    titikB = { x: 6, y: 1 };
                    break;
                case 'contoh3':
                    titikA = { x: 3, y: 4 };
                    titikB = { x: 7, y: 4 };
                    break;
                case 'contoh4':
                    titikA = { x: 1, y: 1 };
                    titikB = { x: 3, y: 7 };
                    break;
            }
            
            document.getElementById('x1-input').value = titikA.x;
            document.getElementById('y1-input').value = titikA.y;
            document.getElementById('x2-input').value = titikB.x;
            document.getElementById('y2-input').value = titikB.y;
            
            updateGrafik();
            showToast(`Contoh soal diterapkan`);
        }
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const height = getGrafikHeight();
                document.getElementById('grafik-container').style.height = height + 'px';
                Plotly.relayout('grafik-container', {
                    height: height
                });
            }, 250);
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial height
            const initialHeight = getGrafikHeight();
            document.getElementById('grafik-container').style.height = initialHeight + 'px';
            
            // Inisialisasi grafik
            updateGrafik();
            
            // Update saat input berubah
            ['x1-input', 'y1-input', 'x2-input', 'y2-input'].forEach(id => {
                document.getElementById(id).addEventListener('input', function() {
                    document.getElementById('hitung-btn').classList.add('btn-warning');
                    document.getElementById('hitung-btn').innerHTML = '<i class="fas fa-calculator me-2"></i>Klik untuk Hitung';
                });
            });
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Perbesar input untuk touch
                document.querySelectorAll('input').forEach(input => {
                    input.style.minHeight = '44px';
                    input.style.fontSize = '16px'; // Mencegah zoom di iOS
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
            
            .touch-device .contoh-item {
                padding: 15px;
            }
            
            .touch-device .btn {
                padding: 12px;
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #1976d2;
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #1565c0;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>