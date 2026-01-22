<?php
// student/simulasi_jenis_gradien.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Jenis-jenis Gradien - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --warna-positif: #4caf50;
            --warna-negatif: #f44336;
            --warna-nol: #ff9800;
            --warna-tak-terdefinisi: #2196f3;
            --mobile-breakpoint: 768px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            padding-bottom: 20px;
        }
        
        /* Header Responsive */
        .simulasi-header {
            background: linear-gradient(135deg, #666, #333);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
            border-left: 4px solid #666;
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
        #jenis-container {
            width: 100%;
            height: 300px;
            background: white;
            border-radius: 8px;
            border: 2px solid #ddd;
            margin-bottom: 1rem;
        }
        
        @media (min-width: 576px) {
            #jenis-container {
                height: 350px;
            }
        }
        
        @media (min-width: 768px) {
            #jenis-container {
                height: 400px;
            }
        }
        
        @media (min-width: 992px) {
            #jenis-container {
                height: 450px;
            }
        }
        
        /* Jenis Cards Responsive */
        .jenis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .jenis-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .jenis-card {
            border-radius: 10px;
            padding: 1.25rem;
            color: white;
            transition: all 0.3s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .jenis-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .jenis-positif {
            background: linear-gradient(135deg, var(--warna-positif), #2e7d32);
        }
        
        .jenis-negatif {
            background: linear-gradient(135deg, var(--warna-negatif), #c62828);
        }
        
        .jenis-nol {
            background: linear-gradient(135deg, var(--warna-nol), #ef6c00);
        }
        
        .jenis-tak-terdefinisi {
            background: linear-gradient(135deg, var(--warna-tak-terdefinisi), #0d47a1);
        }
        
        /* Button Jenis Responsive */
        .btn-jenis-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            margin: 1rem 0;
        }
        
        @media (min-width: 576px) {
            .btn-jenis-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .btn-jenis {
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .btn-jenis:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }
        
        .btn-positif { background: var(--warna-positif); }
        .btn-negatif { background: var(--warna-negatif); }
        .btn-nol { background: var(--warna-nol); }
        .btn-tak-terdefinisi { background: var(--warna-tak-terdefinisi); }
        
        /* Gradien Display Responsive */
        .gradien-display {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
        
        @media (min-width: 768px) {
            .gradien-display {
                font-size: 3rem;
            }
        }
        
        /* Icon Jenis Responsive */
        .icon-jenis {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (min-width: 768px) {
            .icon-jenis {
                font-size: 2rem;
                width: 60px;
                height: 60px;
            }
        }
        
        /* Slider Responsive */
        .slider-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.25rem;
            border: 2px solid #e9ecef;
            margin: 1rem 0;
        }
        
        .slider-label {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        input[type="range"] {
            width: 100%;
            height: 8px;
            -webkit-appearance: none;
            background: #dee2e6;
            border-radius: 4px;
            outline: none;
        }
        
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            background: #666;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        
        /* Latihan Game Responsive */
        .game-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .game-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .game-card {
            background: white;
            border-radius: 10px;
            padding: 1.25rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .score-display {
            font-size: 2.5rem;
            font-weight: 800;
            color: #333;
            text-align: center;
            margin: 0.5rem 0;
        }
        
        @media (min-width: 768px) {
            .score-display {
                font-size: 3.5rem;
            }
        }
        
        .answer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        
        @media (min-width: 576px) {
            .answer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .btn-answer {
            min-height: 44px;
            font-size: 0.9rem;
        }
        
        /* Feedback Responsive */
        .feedback-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            min-height: 80px;
            border: 2px solid #e9ecef;
            font-size: 0.9rem;
            line-height: 1.4;
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
        button, .btn, .jenis-card {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Footer Responsive */
        .simulasi-footer {
            background: linear-gradient(135deg, #e0e0e0, #bdbdbd);
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
            color: #333;
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
                        <i class="fas fa-layer-group me-2"></i>Simulasi Jenis-jenis Gradien
                    </h1>
                    <p class="mb-0 opacity-90">
                        Pelajari 4 jenis gradien: positif, negatif, nol, dan tak terdefinisi
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
            <div class="spinner-border text-secondary mb-2" role="status">
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
                        <h4 class="fw-bold mb-3">
                            <i class="fas fa-chart-area me-2"></i>Visualisasi 4 Jenis Gradien
                        </h4>
                        
                        <!-- Grafik Container -->
                        <div id="jenis-container"></div>
                        
                        <!-- Gradien Display -->
                        <div class="gradien-display">
                            m = <span id="gradien-value">1</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Section -->
            <div class="col-lg-4">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-palette me-2"></i>Pilih Jenis Gradien
                        </h5>
                        
                        <!-- Tombol Jenis -->
                        <div class="btn-jenis-grid">
                            <button class="btn-jenis btn-positif" onclick="setJenis('positif')">
                                <i class="fas fa-arrow-up me-2"></i>Gradien Positif (m > 0)
                            </button>
                            <button class="btn-jenis btn-negatif" onclick="setJenis('negatif')">
                                <i class="fas fa-arrow-down me-2"></i>Gradien Negatif (m < 0)
                            </button>
                            <button class="btn-jenis btn-nol" onclick="setJenis('nol')">
                                <i class="fas fa-minus me-2"></i>Gradien Nol (m = 0)
                            </button>
                            <button class="btn-jenis btn-tak-terdefinisi" onclick="setJenis('tak-terdefinisi')">
                                <i class="fas fa-ban me-2"></i>Tidak Terdefinisi (Δx = 0)
                            </button>
                        </div>
                        
                        <!-- Kontrol Gradien -->
                        <div class="slider-container">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-sliders-h me-2"></i>Kontrol Gradien
                            </h6>
                            <div class="mb-3">
                                <label class="slider-label">Nilai Gradien (m)</label>
                                <input type="range" class="form-range" id="gradien-slider" 
                                       min="-3" max="3" step="0.5" value="1">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">-3</small>
                                    <small class="text-muted">Kemiringan</small>
                                    <small class="text-muted">3</small>
                                </div>
                            </div>
                            <button class="btn btn-secondary w-100" onclick="updateFromSlider()">
                                <i class="fas fa-sync me-2"></i>Update dari Slider
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- Latihan Interaktif -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-gamepad me-2"></i>Latihan Identifikasi Gradien
                        </h5>
                        
                        <div class="game-grid">
                            <div class="game-card">
                                <h6 class="fw-bold mb-2">Tantangan</h6>
                                <p class="small mb-3">Identifikasi jenis gradien berdasarkan grafik yang ditampilkan</p>
                                
                                <div class="answer-grid">
                                    <button class="btn btn-outline-success btn-answer" onclick="checkAnswer('positif')">
                                        Gradien Positif
                                    </button>
                                    <button class="btn btn-outline-danger btn-answer" onclick="checkAnswer('negatif')">
                                        Gradien Negatif
                                    </button>
                                    <button class="btn btn-outline-warning btn-answer" onclick="checkAnswer('nol')">
                                        Gradien Nol
                                    </button>
                                    <button class="btn btn-outline-primary btn-answer" onclick="checkAnswer('tak-terdefinisi')">
                                        Tidak Terdefinisi
                                    </button>
                                </div>
                                
                                <button class="btn btn-primary w-100 mt-2" onclick="generateRandomGrafik()">
                                    <i class="fas fa-random me-2"></i>Grafik Acak Baru
                                </button>
                            </div>
                            
                            <div class="game-card">
                                <div class="text-center">
                                    <h6 class="fw-bold mb-2">Skor Anda</h6>
                                    <div class="score-display" id="score">0</div>
                                    <div class="feedback-box" id="feedback">
                                        Pilih "Grafik Acak Baru" untuk mulai latihan
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm w-100 mt-2" onclick="resetScore()">
                                        <i class="fas fa-redo me-2"></i>Reset Skor
                                    </button>
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
        // Variabel
        let currentM = 1.0;
        let currentType = 'positif';
        let score = 0;
        let currentChallengeM = null;
        
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
        function updateGrafik(m = currentM, type = currentType) {
            // Tampilkan loading
            showLoading();
            
            currentM = m;
            currentType = type;
            
            let garisTrace;
            let warna;
            let nama;
            
            if (type === 'tak-terdefinisi') {
                // Garis vertikal
                const x = [0, 0, 0, 0];
                const y = [-10, -5, 5, 10];
                
                garisTrace = {
                    x: x,
                    y: y,
                    mode: 'lines',
                    type: 'scatter',
                    name: 'Garis Vertikal (x = 0)',
                    line: {
                        color: '#2196f3',
                        width: 4
                    },
                    hoverinfo: 'none'
                };
                warna = '#2196f3';
                nama = 'Tidak Terdefinisi';
            } else {
                // Garis dengan gradien
                const x = [];
                const y = [];
                for (let i = -10; i <= 10; i += 0.5) {
                    x.push(i);
                    y.push(m * i);
                }
                
                garisTrace = {
                    x: x,
                    y: y,
                    mode: 'lines',
                    type: 'scatter',
                    name: `y = ${m.toFixed(1)}x`,
                    line: {
                        color: getWarna(type),
                        width: 4
                    },
                    hoverinfo: 'none'
                };
                warna = getWarna(type);
                nama = getNama(type);
            }
            
            // Layout responsif
            const layout = {
                title: {
                    text: `${nama} - Gradien: ${type === 'tak-terdefinisi' ? '∞' : m.toFixed(1)}`,
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
                shapes: [
                    // Sumbu X
                    {
                        type: 'line',
                        x0: -10,
                        y0: 0,
                        x1: 10,
                        y1: 0,
                        line: {
                            color: '#999',
                            width: 1,
                            dash: 'dot'
                        }
                    },
                    // Sumbu Y
                    {
                        type: 'line',
                        x0: 0,
                        y0: -10,
                        x1: 0,
                        y1: 10,
                        line: {
                            color: '#999',
                            width: 1,
                            dash: 'dot'
                        }
                    }
                ]
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
            Plotly.react('jenis-container', [garisTrace], layout, config).then(() => {
                hideLoading();
            });
            
            // Update display
            document.getElementById('gradien-value').textContent = 
                type === 'tak-terdefinisi' ? '∞' : m.toFixed(1);
            document.getElementById('gradien-value').style.color = warna;
            
            // Update slider
            if (type !== 'tak-terdefinisi') {
                document.getElementById('gradien-slider').value = m;
            }
            
            // Tampilkan toast notifikasi
            showToast(`${nama} - m = ${type === 'tak-terdefinisi' ? '∞' : m.toFixed(1)}`);
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
                    background: linear-gradient(135deg, #666, #333);
                    color: white;
                    padding: 12px 24px;
                    border-radius: 25px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    z-index: 10000;
                    animation: fadeInUp 0.3s ease-out;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                    display: flex;
                    align-items: center;
                    min-width: 200px;
                    justify-content: center;
                ">
                    <i class="fas fa-chart-line me-2"></i>${message}
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
        
        // Get warna berdasarkan jenis
        function getWarna(type) {
            switch(type) {
                case 'positif': return '#4caf50';
                case 'negatif': return '#f44336';
                case 'nol': return '#ff9800';
                case 'tak-terdefinisi': return '#2196f3';
                default: return '#666';
            }
        }
        
        // Get nama berdasarkan jenis
        function getNama(type) {
            switch(type) {
                case 'positif': return 'Gradien Positif';
                case 'negatif': return 'Gradien Negatif';
                case 'nol': return 'Gradien Nol';
                case 'tak-terdefinisi': return 'Tidak Terdefinisi';
                default: return 'Unknown';
            }
        }
        
        // Set jenis gradien
        function setJenis(type) {
            let m;
            switch(type) {
                case 'positif':
                    m = 1.0;
                    break;
                case 'negatif':
                    m = -1.0;
                    break;
                case 'nol':
                    m = 0;
                    break;
                case 'tak-terdefinisi':
                    m = Infinity;
                    break;
            }
            updateGrafik(m, type);
        }
        
        // Update dari slider
        function updateFromSlider() {
            const m = parseFloat(document.getElementById('gradien-slider').value);
            let type;
            if (m > 0) type = 'positif';
            else if (m < 0) type = 'negatif';
            else type = 'nol';
            updateGrafik(m, type);
        }
        
        // Generate grafik acak untuk latihan
        function generateRandomGrafik() {
            const types = ['positif', 'negatif', 'nol', 'tak-terdefinisi'];
            const randomType = types[Math.floor(Math.random() * types.length)];
            
            let m;
            switch(randomType) {
                case 'positif':
                    m = parseFloat((Math.random() * 2 + 0.5).toFixed(1)); // 0.5 to 2.5
                    break;
                case 'negatif':
                    m = parseFloat(-(Math.random() * 2 + 0.5).toFixed(1)); // -0.5 to -2.5
                    break;
                case 'nol':
                    m = 0;
                    break;
                case 'tak-terdefinisi':
                    m = Infinity;
                    break;
            }
            
            currentChallengeM = randomType;
            updateGrafik(m, randomType);
            document.getElementById('feedback').innerHTML = 
                '<span class="text-primary fw-bold">Grafik baru ditampilkan!</span><br>' +
                '<small>Identifikasi jenis gradiennya</small>';
        }
        
        // Check answer
        function checkAnswer(userAnswer) {
            if (currentChallengeM === null) {
                document.getElementById('feedback').innerHTML = 
                    '<span class="text-warning">Klik "Grafik Acak Baru" dulu untuk mulai!</span>';
                return;
            }
            
            const feedbackEl = document.getElementById('feedback');
            const scoreEl = document.getElementById('score');
            
            if (userAnswer === currentChallengeM) {
                score++;
                scoreEl.textContent = score;
                feedbackEl.innerHTML = 
                    '<span class="text-success fw-bold">✓ Benar!</span><br>' +
                    '<small>' + getNama(currentChallengeM) + '</small><br>' +
                    '<small>Gradien: ' + (currentChallengeM === 'tak-terdefinisi' ? '∞' : currentM.toFixed(1)) + '</small>';
            } else {
                feedbackEl.innerHTML = 
                    '<span class="text-danger fw-bold">✗ Salah!</span><br>' +
                    '<small>Jawaban benar: ' + getNama(currentChallengeM) + '</small><br>' +
                    '<small>Gradien: ' + (currentChallengeM === 'tak-terdefinisi' ? '∞' : currentM.toFixed(1)) + '</small>';
            }
            
            // Generate grafik baru setelah 1.5 detik
            setTimeout(generateRandomGrafik, 1500);
        }
        
        // Reset score
        function resetScore() {
            score = 0;
            document.getElementById('score').textContent = '0';
            document.getElementById('feedback').innerHTML = 
                '<span class="text-primary">Skor direset!</span><br>' +
                '<small>Klik "Grafik Acak Baru" untuk mulai lagi</small>';
        }
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const height = getGrafikHeight();
                document.getElementById('jenis-container').style.height = height + 'px';
                Plotly.relayout('jenis-container', {
                    height: height
                });
            }, 250);
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial height
            const initialHeight = getGrafikHeight();
            document.getElementById('jenis-container').style.height = initialHeight + 'px';
            
            // Inisialisasi grafik
            updateGrafik();
            
            // Mulai dengan grafik acak
            setTimeout(generateRandomGrafik, 500);
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Perbesar tombol untuk touch
                document.querySelectorAll('.btn, .jenis-card').forEach(element => {
                    element.style.padding = '12px';
                });
                
                // Optimasi slider untuk touch
                document.getElementById('gradien-slider').style.height = '12px';
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
            
            .touch-device .jenis-card {
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
                background: #666;
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #333;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>