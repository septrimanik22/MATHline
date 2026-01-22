<?php
// student/simulasi_tegak_lurus.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Garis Tegak Lurus - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --merah-utama: #f44336;
            --biru-utama: #1976d2;
            --hijau-utama: #4caf50;
        }
        
        body {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .simulasi-header {
            background: linear-gradient(135deg, var(--merah-utama), #c62828);
            color: white;
            padding: 1.5rem 0;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(244, 67, 54, 0.2);
        }
        
        .simulasi-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .simulasi-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .simulasi-body {
            padding: 1.5rem;
        }
        
        /* Responsif container grafik */
        #tegak-lurus-container {
            width: 100%;
            height: 400px; /* Default untuk desktop */
            background: white;
            border-radius: 10px;
            border: 2px solid #ffcdd2;
        }
        
        /* Media query untuk perangkat mobile */
        @media (max-width: 768px) {
            #tegak-lurus-container {
                height: 300px; /* Lebih kecil untuk mobile */
            }
            
            .simulasi-body {
                padding: 1rem;
            }
            
            .simulasi-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .simulasi-header {
                padding: 1rem 0;
                border-radius: 0 0 15px 15px;
            }
            
            .control-panel {
                padding: 1rem;
            }
            
            .prinsip-box {
                padding: 0.75rem;
                margin: 0.75rem 0;
            }
            
            .produk-gradien {
                font-size: 1.5rem;
            }
            
            .sudut-indicator {
                width: 80px;
                height: 80px;
                margin: 0.5rem auto;
            }
            
            .sudut-text {
                font-size: 0.9rem;
            }
        }
        
        /* Media query untuk perangkat sangat kecil */
        @media (max-width: 576px) {
            #tegak-lurus-container {
                height: 250px;
            }
            
            .simulasi-body {
                padding: 0.75rem;
            }
            
            .simulasi-header h1 {
                font-size: 1.25rem;
            }
            
            .status-box .display-4 {
                font-size: 2.5rem;
            }
            
            .persamaan-box {
                font-size: 0.9rem;
                padding: 0.75rem;
            }
        }
        
        /* Media query untuk perangkat besar */
        @media (min-width: 992px) {
            #tegak-lurus-container {
                height: 450px;
            }
        }
        
        @media (min-width: 1200px) {
            #tegak-lurus-container {
                height: 500px;
            }
        }
        
        /* Layout grid responsif */
        .control-panel .row > [class*="col-"] {
            margin-bottom: 1rem;
        }
        
        /* Perbaikan layout untuk mobile */
        @media (max-width: 768px) {
            .prinsip-box .row {
                flex-direction: column;
            }
            
            .prinsip-box .col-md-6 {
                margin-bottom: 1rem;
            }
            
            .status-box .row {
                flex-direction: column;
                text-align: center;
            }
            
            .status-box .col-md-4,
            .status-box .col-md-8 {
                width: 100%;
                max-width: 100%;
            }
        }
        
        .prinsip-box {
            background: #e3f2fd;
            border: 2px solid var(--biru-utama);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .btn-merah {
            background: linear-gradient(135deg, var(--merah-utama), #d32f2f);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-merah:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
        }
        
        .control-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .produk-gradien {
            font-size: 2rem;
            font-weight: bold;
            color: var(--merah-utama);
            text-align: center;
            margin: 1rem 0;
        }
        
        .persamaan-box {
            background: #fff3e0;
            border-radius: 10px;
            padding: 1rem;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            text-align: center;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        .sudut-indicator {
            width: 100px;
            height: 100px;
            margin: 1rem auto;
            position: relative;
        }
        
        .sudut-line {
            position: absolute;
            background: #333;
        }
        
        .sudut-line-1 {
            width: 70px;
            height: 3px;
            top: 50px;
            left: 15px;
            transform-origin: left center;
        }
        
        .sudut-line-2 {
            width: 70px;
            height: 3px;
            top: 50px;
            left: 15px;
            transform-origin: left center;
            transform: rotate(90deg);
        }
        
        .sudut-text {
            position: absolute;
            top: 30px;
            left: 50px;
            font-weight: bold;
            color: var(--merah-utama);
        }
        
        .status-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
        }
        
        /* Perbaikan untuk tombol responsif */
        .btn-responsive {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .btn-responsive {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="simulasi-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-8">
                    <h1 class="h3 fw-bold mb-2">
                        <i class="fas fa-perpendicular me-2"></i>Simulasi Garis Tegak Lurus
                    </h1>
                    <p class="mb-0 opacity-90 small">
                        Pelajari sifat garis tegak lurus: m₁ × m₂ = -1
                    </p>
                </div>
                <div class="col-md-6 col-4 text-end">
                    <a href="materials.php" class="btn btn-light btn-sm btn-responsive">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Simulasi Container -->
    <div class="simulasi-container">
        <div class="row">
            <!-- Grafik Utama -->
            <div class="col-lg-8 col-12">
                <div class="simulasi-card">
                    <div class="simulasi-body">
                        <h4 class="fw-bold mb-4 text-danger">
                            <i class="fas fa-crosshairs me-2"></i>Visualisasi Garis Tegak Lurus
                        </h4>
                        
                        <!-- Tegak Lurus Container -->
                        <div id="tegak-lurus-container"></div>
                        
                        <!-- Prinsip Garis Tegak Lurus -->
                        <div class="prinsip-box mt-4">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <h6 class="fw-bold">
                                        <i class="fas fa-ruler-combined me-2"></i>Sifat Garis Tegak Lurus
                                    </h6>
                                    <p class="mb-0 small">
                                        Dua garis dikatakan tegak lurus jika hasil kali gradiennya -1.
                                        <br><strong>m₁ × m₂ = -1</strong>
                                    </p>
                                </div>
                                <div class="col-md-6 col-12">
                                    <h6 class="fw-bold">
                                        <i class="fas fa-info-circle me-2"></i>Karakteristik
                                    </h6>
                                    <p class="mb-0 small">
                                        • Berpotongan membentuk sudut 90°<br>
                                        • Gradien saling berkebalikan negatif<br>
                                        • m₂ = -1/m₁
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Panel Kontrol -->
            <div class="col-lg-4 col-12">
                <div class="control-panel">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-sliders-h me-2"></i>Kontrol Garis
                    </h5>
                    
                    <!-- Garis Pertama -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-line me-2"></i>Garis Pertama
                        </h6>
                        <div class="mb-3">
                            <label class="form-label small">Gradien (m₁)</label>
                            <input type="range" class="form-range" id="m1-slider" 
                                   min="-3" max="3" step="0.5" value="2">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">-3</small>
                                <small class="text-muted">Kemiringan</small>
                                <small class="text-muted">3</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Intercept (c₁)</label>
                            <input type="range" class="form-range" id="c1-slider" 
                                   min="-5" max="5" step="1" value="0">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">-5</small>
                                <small class="text-muted">Titik Potong</small>
                                <small class="text-muted">5</small>
                            </div>
                        </div>
                        <div class="persamaan-box">
                            <span id="persamaan-1">y = 2x + 0</span>
                        </div>
                    </div>
                    
                    <!-- Garis Tegak Lurus -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-danger mb-3">
                            <i class="fas fa-perpendicular me-2"></i>Garis Tegak Lurus
                        </h6>
                        <div class="mb-3">
                            <label class="form-label small">Intercept (c₂)</label>
                            <input type="range" class="form-range" id="c2-slider" 
                                   min="-5" max="5" step="1" value="0">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">-5</small>
                                <small class="text-muted">Titik Potong</small>
                                <small class="text-muted">5</small>
                            </div>
                        </div>
                        <div class="persamaan-box">
                            <span id="persamaan-2">y = -0.5x + 0</span>
                        </div>
                    </div>
                    
                    <!-- Produk Gradien -->
                    <div class="text-center mb-4">
                        <div class="produk-gradien">
                            m₁ × m₂ = <span id="produk-value">-1.00</span>
                        </div>
                        <div class="sudut-indicator">
                            <div class="sudut-line sudut-line-1"></div>
                            <div class="sudut-line sudut-line-2"></div>
                            <div class="sudut-text">90°</div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="mt-4">
                        <button class="btn btn-merah w-100 mb-2 btn-responsive" id="update-btn">
                            <i class="fas fa-sync me-2"></i>Update Garis
                        </button>
                        <button class="btn btn-outline-secondary w-100 btn-responsive" id="reset-btn">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Tegak Lurus -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="simulasi-card">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-check-circle me-2"></i>Status Garis
                        </h5>
                        
                        <div class="status-box">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-12 text-center mb-3 mb-md-0">
                                    <div id="status-icon" class="display-4 mb-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div id="status-text" class="fw-bold text-success">
                                        TEGAK LURUS
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <div class="persamaan-box mb-3">
                                        Garis 1: y = <span id="eq1-display">2x + 0</span>
                                        <br>Garis 2: y = <span id="eq2-display">-0.5x + 0</span>
                                    </div>
                                    <div class="text-center">
                                        <p class="mb-1">
                                            m₁ = <span id="m1-display">2.0</span>, 
                                            m₂ = <span id="m2-display">-0.5</span>
                                        </p>
                                        <p class="mb-0">
                                            m₁ × m₂ = <span id="hasil-display" class="fw-bold">-1.00</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Data garis
        let m1 = 2.0;
        let c1 = 0;
        let m2 = -0.5; // m2 = -1/m1
        let c2 = 0;
        
        // Titik potong
        let titikPotong = { x: 0, y: 0 };
        
        // Fungsi untuk mengatur ukuran grafik berdasarkan lebar layar
        function getGraphHeight() {
            const width = window.innerWidth;
            if (width < 576) return 250; // Smartphone kecil
            if (width < 768) return 300; // Smartphone
            if (width < 992) return 350; // Tablet
            if (width < 1200) return 400; // Laptop kecil
            return 450; // Desktop besar
        }
        
        // Inisialisasi grafik
        function initGrafik() {
            updateGrafik();
        }
        
        // Update grafik dengan visualisasi yang lebih baik
        function updateGrafik() {
            // Hitung titik potong
            if (m1 !== m2) {
                titikPotong.x = (c2 - c1) / (m1 - m2);
                titikPotong.y = m1 * titikPotong.x + c1;
            }
            
            const range = 8;
            
            // Generate data untuk garis pertama (lebih halus)
            const x1 = [];
            const y1 = [];
            for (let i = titikPotong.x - range; i <= titikPotong.x + range; i += 0.1) {
                x1.push(i);
                y1.push(m1 * i + c1);
            }
            
            // Generate data untuk garis kedua (lebih halus)
            const x2 = [];
            const y2 = [];
            for (let i = titikPotong.x - range; i <= titikPotong.x + range; i += 0.1) {
                x2.push(i);
                y2.push(m2 * i + c2);
            }
            
            // Data untuk garis pertama (biru)
            const garis1Trace = {
                x: x1,
                y: y1,
                mode: 'lines',
                type: 'scatter',
                name: `G1: y = ${m1.toFixed(1)}x + ${c1}`,
                line: {
                    color: '#1976d2',
                    width: 4
                },
                hoverinfo: 'name'
            };
            
            // Data untuk garis kedua (merah)
            const garis2Trace = {
                x: x2,
                y: y2,
                mode: 'lines',
                type: 'scatter',
                name: `G2: y = ${m2.toFixed(2)}x + ${c2}`,
                line: {
                    color: '#f44336',
                    width: 4
                },
                hoverinfo: 'name'
            };
            
            // Data untuk titik potong
            const titikTrace = {
                x: [titikPotong.x],
                y: [titikPotong.y],
                mode: 'markers',
                type: 'scatter',
                name: 'Titik Potong',
                marker: {
                    size: 10,
                    color: '#4caf50',
                    symbol: 'circle',
                    line: {
                        color: '#2e7d32',
                        width: 2
                    }
                },
                hoverinfo: 'x+y'
            };
            
            // Gabungkan semua traces
            const data = [garis1Trace, garis2Trace, titikTrace];
            
            // Layout dengan visualisasi yang lebih baik
            const layout = {
                title: {
                    text: `m₁ × m₂ = ${(m1 * m2).toFixed(2)}`,
                    font: {
                        size: window.innerWidth < 768 ? 14 : 18,
                        family: 'Arial, sans-serif'
                    }
                },
                xaxis: {
                    title: {
                        text: 'Sumbu X',
                        font: {
                            size: window.innerWidth < 768 ? 12 : 14,
                            family: 'Arial, sans-serif'
                        }
                    },
                    range: [titikPotong.x - range, titikPotong.x + range],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    zerolinewidth: 2,
                    showgrid: true,
                    tickmode: 'linear',
                    tick0: Math.floor(titikPotong.x - range),
                    dtick: 2
                },
                yaxis: {
                    title: {
                        text: 'Sumbu Y',
                        font: {
                            size: window.innerWidth < 768 ? 12 : 14,
                            family: 'Arial, sans-serif'
                        }
                    },
                    range: [titikPotong.y - range, titikPotong.y + range],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    zerolinewidth: 2,
                    showgrid: true,
                    tickmode: 'linear',
                    tick0: Math.floor(titikPotong.y - range),
                    dtick: 2
                },
                plot_bgcolor: '#f8f9fa',
                paper_bgcolor: '#fff',
                showlegend: true,
                legend: {
                    x: 0.02,
                    y: 1.02,
                    bgcolor: 'rgba(255,255,255,0.8)',
                    bordercolor: '#ddd',
                    borderwidth: 1,
                    font: {
                        size: window.innerWidth < 768 ? 10 : 12
                    }
                },
                margin: window.innerWidth < 768 ? {
                    l: 50, r: 20, b: 50, t: 50
                } : {
                    l: 60, r: 30, b: 60, t: 60
                },
                height: getGraphHeight()
            };
            
            // Render grafik
            Plotly.newPlot('tegak-lurus-container', data, layout);
            
            // Update nilai-nilai
            updateNilai();
        }
        
        // Update nilai-nilai
        function updateNilai() {
            // Update slider values
            document.getElementById('m1-slider').value = m1;
            document.getElementById('c1-slider').value = c1;
            document.getElementById('c2-slider').value = c2;
            
            // Update persamaan
            document.getElementById('persamaan-1').textContent = 
                `y = ${m1.toFixed(1)}x + ${c1}`;
            document.getElementById('persamaan-2').textContent = 
                `y = ${m2.toFixed(2)}x + ${c2}`;
            
            // Update produk
            const produk = m1 * m2;
            document.getElementById('produk-value').textContent = produk.toFixed(2);
            
            // Update status
            const statusIcon = document.getElementById('status-icon');
            const statusText = document.getElementById('status-text');
            const hasilDisplay = document.getElementById('hasil-display');
            
            if (Math.abs(produk + 1) < 0.01) { // Toleransi
                statusIcon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                statusText.innerHTML = 'TEGAK LURUS';
                statusText.className = 'fw-bold text-success';
                hasilDisplay.className = 'fw-bold text-success';
            } else {
                statusIcon.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                statusText.innerHTML = 'TIDAK TEGAK LURUS';
                statusText.className = 'fw-bold text-danger';
                hasilDisplay.className = 'fw-bold text-danger';
            }
            
            // Update display
            document.getElementById('eq1-display').textContent = `${m1.toFixed(1)}x + ${c1}`;
            document.getElementById('eq2-display').textContent = `${m2.toFixed(2)}x + ${c2}`;
            document.getElementById('m1-display').textContent = m1.toFixed(1);
            document.getElementById('m2-display').textContent = m2.toFixed(2);
            document.getElementById('hasil-display').textContent = produk.toFixed(2);
            
            // Warna produk
            const produkEl = document.getElementById('produk-value');
            if (Math.abs(produk + 1) < 0.01) {
                produkEl.style.color = '#4caf50';
            } else {
                produkEl.style.color = '#f44336';
            }
        }
        
        // Event listeners untuk slider
        document.getElementById('m1-slider').addEventListener('input', function(e) {
            m1 = parseFloat(e.target.value);
            if (m1 === 0) {
                m2 = Infinity; // Garis vertikal
            } else {
                m2 = -1 / m1; // Hitung gradien tegak lurus
            }
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
        });
        
        // Reset button
        document.getElementById('reset-btn').addEventListener('click', function() {
            m1 = 2.0;
            c1 = 0;
            m2 = -0.5;
            c2 = 0;
            updateGrafik();
        });
        
        // Responsif saat ukuran layar berubah
        window.addEventListener('resize', function() {
            updateGrafik();
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initGrafik();
        });
    </script>
</body>
</html>