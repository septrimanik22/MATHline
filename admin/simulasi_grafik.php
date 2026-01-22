<?php
// student/simulasi_grafik.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Grafik Persamaan Garis - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --biru-utama: #1976d2;
            --biru-muda: #bbdefb;
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
        
        .back-btn {
            min-height: 40px;
            min-width: 40px;
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
        }
        
        @media (max-width: 768px) {
            .back-btn {
                font-size: 0.85rem;
                padding: 0.25rem 0.5rem;
            }
            
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
        
        @media (min-width: 992px) {
            .simulasi-container {
                margin: 2rem auto;
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
        
        /* Control Panel Responsive */
        .control-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.25rem;
            border: 2px solid #e9ecef;
            height: 100%;
        }
        
        .control-panel h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        
        /* Slider Responsive */
        .slider-container {
            margin-bottom: 1.25rem;
        }
        
        .slider-label {
            font-weight: 600;
            color: var(--biru-utama);
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .slider-value {
            background: var(--biru-utama);
            color: white;
            padding: 0.125rem 0.5rem;
            border-radius: 10px;
            font-size: 0.85rem;
            min-width: 45px;
            text-align: center;
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
            width: 20px;
            height: 20px;
            background: var(--biru-utama);
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Buttons Responsive */
        .btn-simulasi {
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
        
        .btn-simulasi:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
        .btn-outline-primary {
            min-height: 44px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .btn-simulasi, .btn-outline-primary {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
        }
        
        /* Info Box Responsive */
        .info-box {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-left: 4px solid #4caf50;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .info-box h6 {
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .info-box p {
            font-size: 0.85rem;
            margin-bottom: 0;
            line-height: 1.4;
        }
        
        /* Persamaan Box Responsive */
        .persamaan-box {
            background: #fff3e0;
            border: 2px solid #ff9800;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            text-align: center;
            margin: 1rem 0;
        }
        
        #persamaan-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e65100;
            margin-bottom: 0.25rem;
        }
        
        @media (max-width: 576px) {
            #persamaan-text {
                font-size: 1rem;
            }
        }
        
        .persamaan-box small {
            font-size: 0.8rem;
        }
        
        /* Step Box Responsive */
        .step-box {
            background: #f3e5f5;
            border: 2px dashed #7b1fa2;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .step-box h6 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .step-box ol {
            padding-left: 1.25rem;
            margin-bottom: 0;
        }
        
        .step-box li {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        /* Grid System for Layout */
        .main-layout {
            display: flex;
            flex-direction: column;
        }
        
        @media (min-width: 992px) {
            .main-layout {
                flex-direction: row;
                gap: 1.5rem;
            }
            
            .grafik-section {
                flex: 2;
            }
            
            .control-section {
                flex: 1;
                min-width: 300px;
                max-width: 350px;
            }
        }
        
        /* Responsive untuk contoh */
        @media (max-width: 768px) {
            .contoh-item {
                padding: 0.5rem;
            }
        }
        
        /* Touch Optimizations */
        button, .btn, .contoh-item {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        input[type="range"] {
            touch-action: manipulation;
        }
        
        /* Footer Responsive */
        .simulasi-footer {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 1rem 0;
            margin-top: 2rem;
            text-align: center;
            color: #2d3748;
            font-size: 0.9rem;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
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
                        <i class="fas fa-chart-line me-2"></i>Simulasi Grafik Persamaan Garis
                    </h1>
                    <p class="mb-0 opacity-90">
                        Pelajari bentuk y = mx + c dengan interaktif
                    </p>
                </div>
                <div class="col-3 col-md-2 text-end">
                    <!-- PERUBAHAN DI SINI: Kembali ke view_materials.php -->
                    <a href="materials.php" class="btn btn-light back-btn">
                        <i class="fas fa-arrow-left me-1"></i><span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container simulasi-container">
        <div class="main-layout">
            <!-- Grafik Section -->
            <div class="grafik-section">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Grafik Interaktif y = mx + c
                        </h5>
                        
                        <!-- Grafik Container -->
                        <div id="grafik-container"></div>
                        
                        <!-- Persamaan -->
                        <div class="persamaan-box mt-3">
                            <div id="persamaan-text">y = 1x + 0</div>
                            <small class="text-muted">Bentuk umum persamaan garis lurus: y = mx + c</small>
                        </div>
                        
                        <!-- Informasi -->
                        <div class="info-box">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Penjelasan Grafik
                            </h6>
                            <p id="grafik-info" class="mb-0">
                                Garis dengan gradien m = 1 dan konstanta c = 0. Garis ini melalui titik (0,0).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Section -->
            <div class="control-section">
                <div class="control-panel fade-in">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-sliders-h me-2"></i>Kontrol Parameter
                    </h5>
                    
                    <!-- Slider Gradien (m) -->
                    <div class="slider-container">
                        <div class="slider-label">
                            <span>Gradien (m)</span>
                            <span id="m-value" class="slider-value">1.0</span>
                        </div>
                        <input type="range" class="form-range" id="m-slider" 
                               min="-3" max="3" step="0.5" value="1">
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">-3</small>
                            <small class="text-muted">Kemiringan</small>
                            <small class="text-muted">3</small>
                        </div>
                        <small class="text-muted d-block mt-1">
                            m > 0: garis naik | m < 0: garis turun | m = 0: horizontal
                        </small>
                    </div>
                    
                    <!-- Slider Konstanta (c) -->
                    <div class="slider-container">
                        <div class="slider-label">
                            <span>Konstanta (c)</span>
                            <span id="c-value" class="slider-value">0</span>
                        </div>
                        <input type="range" class="form-range" id="c-slider" 
                               min="-5" max="5" step="1" value="0">
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">-5</small>
                            <small class="text-muted">Titik Potong Y</small>
                            <small class="text-muted">5</small>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Titik potong garis dengan sumbu Y: (0, c)
                        </small>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="mt-4">
                        <button class="btn btn-simulasi" id="reset-btn">
                            <i class="fas fa-redo me-2"></i>Reset ke Default
                        </button>
                        <button class="btn btn-outline-primary w-100" id="random-btn">
                            <i class="fas fa-random me-2"></i>Contoh Acak
                        </button>
                    </div>
                    
                    <!-- Langkah-langkah -->
                    <div class="step-box mt-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-list-ol me-2"></i>Cara Menggunakan:
                        </h6>
                        <ol class="mb-0">
                            <li class="mb-2">Geser slider gradien (m) untuk mengubah kemiringan garis</li>
                            <li class="mb-2">Geser slider konstanta (c) untuk menggeser garis naik/turun</li>
                            <li class="mb-2">Klik contoh kasus untuk melihat penerapan dalam soal</li>
                            <li>Amati perubahan grafik secara real-time</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inisialisasi variabel
        let m = 1.0;
        let c = 0;
        
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
            // Generate data untuk grafik
            const x = [];
            const y = [];
            for (let i = -10; i <= 10; i += 0.5) {
                x.push(i);
                y.push(m * i + c);
            }
            
            // Data untuk plot
            const trace = {
                x: x,
                y: y,
                mode: 'lines',
                type: 'scatter',
                name: `y = ${m}x ${c >= 0 ? '+' : ''}${c}`,
                line: {
                    color: '#1976d2',
                    width: 3
                }
            };
            
            // Layout responsif
            const layout = {
                title: {
                    text: `Grafik y = ${m}x ${c >= 0 ? '+' : ''}${c}`,
                    font: { size: 14 }
                },
                xaxis: {
                    title: 'Nilai x',
                    range: [-10, 10],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    titlefont: { size: 12 }
                },
                yaxis: {
                    title: 'Nilai y',
                    range: [-10, 10],
                    gridcolor: '#e0e0e0',
                    zerolinecolor: '#999',
                    titlefont: { size: 12 }
                },
                plot_bgcolor: '#f8f9fa',
                paper_bgcolor: '#fff',
                showlegend: true,
                legend: {
                    x: 0,
                    y: 1.1,
                    font: { size: 11 }
                },
                margin: {
                    l: 50,
                    r: 20,
                    b: 50,
                    t: 50,
                    pad: 5
                },
                autosize: true
            };
            
            // Config untuk responsif
            const config = {
                responsive: true,
                displayModeBar: true,
                displaylogo: false,
                modeBarButtonsToRemove: ['select2d', 'lasso2d', 'toggleSpikelines'],
                scrollZoom: false
            };
            
            // Render grafik
            Plotly.newPlot('grafik-container', [trace], layout, config);
            
            // Update persamaan
            document.getElementById('persamaan-text').textContent = 
                `y = ${m}x ${c >= 0 ? '+' : ''}${c}`;
        }
        
        // Update informasi
        function updateInfo() {
            let info = '';
            
            if (m > 0) {
                info = `Gradien positif (${m}) → Garis naik dari kiri ke kanan. `;
            } else if (m < 0) {
                info = `Gradien negatif (${m}) → Garis turun dari kiri ke kanan. `;
            } else {
                info = `Gradien nol → Garis horizontal sejajar sumbu X. `;
            }
            
            if (c > 0) {
                info += `Memotong sumbu Y di atas titik asal (0, ${c}).`;
            } else if (c < 0) {
                info += `Memotong sumbu Y di bawah titik asal (0, ${c}).`;
            } else {
                info += `Melewati titik asal (0,0).`;
            }
            
            document.getElementById('grafik-info').textContent = info;
        }
        
        // Update slider values
        function updateSliderValues() {
            document.getElementById('m-value').textContent = m.toFixed(1);
            document.getElementById('c-value').textContent = c;
        }
        
        // Set contoh kasus
        function setContoh(mVal, cVal, deskripsi) {
            m = mVal;
            c = cVal;
            document.getElementById('m-slider').value = m;
            document.getElementById('c-slider').value = c;
            updateSliderValues();
            updateGrafik();
            updateInfo();
            
            // Tampilkan notifikasi kecil
            showToast(`Contoh: ${deskripsi}`);
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
                    background: rgba(0,0,0,0.8);
                    color: white;
                    padding: 10px 20px;
                    border-radius: 20px;
                    font-size: 0.9rem;
                    z-index: 10000;
                    animation: fadeInUp 0.3s ease-out;
                ">
                    <i class="fas fa-check-circle me-2"></i>${message}
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
        
        // Event listeners untuk slider
        document.getElementById('m-slider').addEventListener('input', function(e) {
            m = parseFloat(e.target.value);
            updateSliderValues();
            updateGrafik();
            updateInfo();
        });
        
        document.getElementById('c-slider').addEventListener('input', function(e) {
            c = parseInt(e.target.value);
            updateSliderValues();
            updateGrafik();
            updateInfo();
        });
        
        // Reset to default
        document.getElementById('reset-btn').addEventListener('click', function() {
            m = 1.0;
            c = 0;
            document.getElementById('m-slider').value = m;
            document.getElementById('c-slider').value = c;
            updateSliderValues();
            updateGrafik();
            updateInfo();
            showToast('Reset ke nilai default');
        });
        
        // Contoh acak
        document.getElementById('random-btn').addEventListener('click', function() {
            const contoh = [
                { m: 2, c: 1, name: 'Garis naik cepat' },
                { m: -1.5, c: -2, name: 'Garis turun' },
                { m: 0, c: 3, name: 'Garis horizontal' },
                { m: 0.5, c: -1, name: 'Garis landai' },
                { m: -0.5, c: 2, name: 'Garis turun perlahan' }
            ];
            
            const randomContoh = contoh[Math.floor(Math.random() * contoh.length)];
            m = randomContoh.m;
            c = randomContoh.c;
            
            document.getElementById('m-slider').value = m;
            document.getElementById('c-slider').value = c;
            updateSliderValues();
            updateGrafik();
            updateInfo();
            
            showToast(`Contoh acak: ${randomContoh.name}`);
        });
        
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
            updateInfo();
            updateSliderValues();
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                // Tambahkan class untuk touch device
                document.body.classList.add('touch-device');
                
                // Optimasi slider untuk touch
                document.querySelectorAll('input[type="range"]').forEach(slider => {
                    slider.style.height = '12px';
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
                padding: 12px;
            }
            
            .touch-device .btn {
                padding: 12px;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>