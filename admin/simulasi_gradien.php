<?php
// student/simulasi_gradien.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Konsep Gradien - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
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
            background: linear-gradient(135deg, #fff3e0 0%, #ffecb3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            padding-bottom: 20px;
        }
        
        /* Header Responsive */
        .simulasi-header {
            background: linear-gradient(135deg, var(--oranye-utama), #f57c00);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.2);
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
            background: linear-gradient(135deg, #4caf50, #2e7d32) !important;
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
            
            .back-btn span {
                display: inline;
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
        
        /* Cards Responsive */
        .simulasi-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: none;
            border-left: 4px solid var(--oranye-utama);
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
        #gradien-container {
            width: 100%;
            height: 300px;
            background: white;
            border-radius: 8px;
            border: 2px solid var(--oranye-muda);
            margin-bottom: 1rem;
        }
        
        @media (min-width: 576px) {
            #gradien-container {
                height: 350px;
            }
        }
        
        @media (min-width: 768px) {
            #gradien-container {
                height: 400px;
            }
        }
        
        @media (min-width: 992px) {
            #gradien-container {
                height: 450px;
            }
        }
        
        /* Info Gradien Responsive */
        .info-gradien {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        @media (min-width: 768px) {
            .info-gradien {
                padding: 1.5rem;
            }
        }
        
        .gradien-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--oranye-utama);
            text-align: center;
            margin: 0.5rem 0;
        }
        
        @media (min-width: 768px) {
            .gradien-value {
                font-size: 3rem;
            }
        }
        
        /* Buttons Responsive */
        .btn-oranye {
            background: linear-gradient(135deg, var(--oranye-utama), #f57c00);
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
        
        .btn-oranye:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        }
        
        .btn-outline-secondary {
            min-height: 44px;
            font-size: 0.9rem;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .btn-oranye, .btn-outline-secondary {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
        }
        
        /* Form Controls Responsive */
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .form-control {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
            min-height: 40px;
        }
        
        /* Arrow Container Responsive */
        .arrow-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.75rem 0;
            height: 40px;
        }
        
        @media (min-width: 768px) {
            .arrow-container {
                height: 50px;
                margin: 1rem 0;
            }
        }
        
        .arrow {
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
        }
        
        @media (min-width: 768px) {
            .arrow {
                border-left: 20px solid transparent;
                border-right: 20px solid transparent;
            }
        }
        
        .arrow-up {
            border-bottom: 30px solid #4caf50;
        }
        
        @media (min-width: 768px) {
            .arrow-up {
                border-bottom: 40px solid #4caf50;
            }
        }
        
        .arrow-right {
            border-left: 30px solid #1976d2;
            border-top: 15px solid transparent;
            border-bottom: 15px solid transparent;
        }
        
        @media (min-width: 768px) {
            .arrow-right {
                border-left: 40px solid #1976d2;
                border-top: 20px solid transparent;
                border-bottom: 20px solid transparent;
            }
        }
        
        /* Delta Box Responsive */
        .delta-box {
            background: #f3e5f5;
            border: 2px solid #7b1fa2;
            border-radius: 8px;
            padding: 0.5rem;
            margin: 0.5rem 0;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        @media (min-width: 768px) {
            .delta-box {
                font-size: 1rem;
                padding: 0.75rem;
            }
        }
        
        /* Jenis Gradien Cards */
        .jenis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        @media (max-width: 768px) {
            .jenis-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .jenis-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .jenis-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
            height: 100%;
        }
        
        .jenis-card:hover {
            border-color: var(--oranye-utama);
            transform: translateY(-2px);
        }
        
        /* Badge Responsive */
        .gradien-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }
        
        @media (min-width: 768px) {
            .gradien-badge {
                font-size: 1rem;
                padding: 0.375rem 1rem;
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
        button, .btn {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Footer Responsive */
        .simulasi-footer {
            background: linear-gradient(135deg, #ffecb3, #ffcc80);
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
            color: #5d4037;
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
                        <i class="fas fa-mountain me-2"></i>Simulasi Konsep Gradien
                    </h1>
                    <p class="mb-0 opacity-90">
                        Pahami konsep gradien sebagai rasio perubahan vertikal terhadap horizontal
                    </p>
                </div>
                <div class="col-3 col-md-2 text-end">
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
            <div class="spinner-border text-warning mb-2" role="status">
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
                        <h4 class="fw-bold mb-3 text-warning">
                            <i class="fas fa-chart-line me-2"></i>Visualisasi Gradien
                        </h4>
                        
                        <!-- Gradien Container -->
                        <div id="gradien-container"></div>
                        
                        <!-- Info Gradien -->
                        <div class="info-gradien mt-3">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-calculator me-2"></i>Perhitungan Gradien
                                    </h6>
                                    <div id="perhitungan-text" class="small">
                                        m = Δy / Δx = (y₂ - y₁) / (x₂ - x₁)
                                    </div>
                                    <div class="delta-box mt-2">
                                        Δy = <span id="delta-y">4</span>, Δx = <span id="delta-x">4</span>
                                    </div>
                                </div>
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
                            <i class="fas fa-sliders-h me-2"></i>Kontrol Titik
                        </h5>
                        
                        <!-- Kontrol Titik A -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary mb-2">
                                <i class="fas fa-circle me-2"></i>Titik A (x₁, y₁)
                            </h6>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">X₁</label>
                                    <input type="number" id="x1-input" class="form-control form-control-sm" 
                                           min="-10" max="10" value="2">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Y₁</label>
                                    <input type="number" id="y1-input" class="form-control form-control-sm" 
                                           min="-10" max="10" value="3">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kontrol Titik B -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-success mb-2">
                                <i class="fas fa-circle me-2"></i>Titik B (x₂, y₂)
                            </h6>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">X₂</label>
                                    <input type="number" id="x2-input" class="form-control form-control-sm" 
                                           min="-10" max="10" value="6">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Y₂</label>
                                    <input type="number" id="y2-input" class="form-control form-control-sm" 
                                           min="-10" max="10" value="7">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nilai Gradien -->
                        <div class="text-center mb-3">
                            <div class="gradien-value">
                                m = <span id="gradien-value">1.00</span>
                            </div>
                            <div id="gradien-kategori" class="gradien-badge bg-success">
                                Gradien Positif
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="mt-3">
                            <button class="btn btn-oranye mb-2" id="update-btn">
                                <i class="fas fa-sync me-2"></i>Update Gradien
                            </button>
                            <button class="btn btn-outline-secondary" id="reset-btn">
                                <i class="fas fa-redo me-2"></i>Reset ke Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Jenis-jenis Gradien -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-layer-group me-2"></i>Jenis-jenis Gradien
                        </h5>
                        
                        <div class="jenis-grid">
                            <div class="jenis-card">
                                <div class="arrow-container">
                                    <div class="arrow arrow-up"></div>
                                </div>
                                <h6 class="fw-bold text-success mt-2">Gradien Positif</h6>
                                <p class="small mb-0">m > 0<br>Garis naik ke kanan</p>
                            </div>
                            
                            <div class="jenis-card">
                                <div class="arrow-container">
                                    <div class="arrow arrow-up" style="transform: rotate(180deg);"></div>
                                </div>
                                <h6 class="fw-bold text-danger mt-2">Gradien Negatif</h6>
                                <p class="small mb-0">m < 0<br>Garis turun ke kanan</p>
                            </div>
                            
                            <div class="jenis-card">
                                <div class="arrow-container">
                                    <div class="arrow arrow-right"></div>
                                </div>
                                <h6 class="fw-bold text-warning mt-2">Gradien Nol</h6>
                                <p class="small mb-0">m = 0<br>Garis horizontal</p>
                            </div>
                            
                            <div class="jenis-card">
                                <div class="arrow-container">
                                    <div style="width: 30px; height: 30px; border-left: 3px solid #000; margin: 0 auto;"></div>
                                </div>
                                <h6 class="fw-bold text-info mt-2">Tidak Terdefinisi</h6>
                                <p class="small mb-0">Δx = 0<br>Garis vertikal</p>
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
                    size: 12,
                    color: ['#1976d2', '#4caf50']
                },
                text: ['A', 'B'],
                textposition: 'top center',
                textfont: {
                    size: 14,
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
                fillcolor: 'rgba(25, 118, 210, 0.2)',
                line: {
                    color: '#1976d2',
                    width: 2,
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
                scrollZoom: false,
                doubleClick: 'reset'
            };
            
            // Render grafik
            Plotly.react('gradien-container', data, layout, config).then(() => {
                hideLoading();
            });
            
            // Update nilai
            updateNilai();
        }
        
        // Update nilai-nilai
        function updateNilai() {
            const deltaY = titikB.y - titikA.y;
            const deltaX = titikB.x - titikA.x;
            
            // Update gradien value
            document.getElementById('gradien-value').textContent = 
                deltaX !== 0 ? gradien.toFixed(2) : '∞';
            
            // Update delta values
            document.getElementById('delta-y').textContent = deltaY;
            document.getElementById('delta-x').textContent = deltaX;
            
            // Update perhitungan
            document.getElementById('perhitungan-text').innerHTML = 
                `m = Δy / Δx = (${titikB.y} - ${titikA.y}) / (${titikB.x} - ${titikA.x}) = ` +
                `${deltaY} / ${deltaX} = ` +
                `<strong>${deltaX !== 0 ? gradien.toFixed(2) : '∞'}</strong>`;
            
            // Update kategori gradien
            if (deltaX === 0) {
                document.getElementById('gradien-kategori').className = 'gradien-badge bg-info';
                document.getElementById('gradien-kategori').textContent = 'Tidak Terdefinisi';
            } else if (gradien > 0) {
                document.getElementById('gradien-kategori').className = 'gradien-badge bg-success';
                document.getElementById('gradien-kategori').textContent = 'Gradien Positif';
            } else if (gradien < 0) {
                document.getElementById('gradien-kategori').className = 'gradien-badge bg-danger';
                document.getElementById('gradien-kategori').textContent = 'Gradien Negatif';
            } else {
                document.getElementById('gradien-kategori').className = 'gradien-badge bg-warning';
                document.getElementById('gradien-kategori').textContent = 'Gradien Nol';
            }
            
            // Tampilkan toast notifikasi
            showToast(`Gradien diperbarui: ${deltaX !== 0 ? gradien.toFixed(2) : '∞'}`);
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
                    background: linear-gradient(135deg, #ff9800, #f57c00);
                    color: white;
                    padding: 12px 24px;
                    border-radius: 25px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    z-index: 10000;
                    animation: fadeInUp 0.3s ease-out;
                    box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
                    display: flex;
                    align-items: center;
                    min-width: 200px;
                    justify-content: center;
                ">
                    <i class="fas fa-sync-alt me-2"></i>${message}
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
        
        // Update gradien dari input
        document.getElementById('update-btn').addEventListener('click', function() {
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
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const height = getGrafikHeight();
                document.getElementById('gradien-container').style.height = height + 'px';
                Plotly.relayout('gradien-container', {
                    height: height
                });
            }, 250);
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial height
            const initialHeight = getGrafikHeight();
            document.getElementById('gradien-container').style.height = initialHeight + 'px';
            
            // Inisialisasi grafik
            updateGrafik();
            
            // Update input saat berubah
            ['x1-input', 'y1-input', 'x2-input', 'y2-input'].forEach(id => {
                document.getElementById(id).addEventListener('input', function() {
                    const btn = document.getElementById('update-btn');
                    btn.classList.add('btn-warning');
                    btn.innerHTML = '<i class="fas fa-sync me-2"></i>Klik untuk Update';
                    
                    // Kembalikan setelah 3 detik
                    setTimeout(() => {
                        btn.classList.remove('btn-warning');
                        btn.innerHTML = '<i class="fas fa-sync me-2"></i>Update Gradien';
                    }, 3000);
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
                background: #ff9800;
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #f57c00;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>