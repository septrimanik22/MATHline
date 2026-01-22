<?php
// student/simulasi_koordinat.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Koordinat Titik - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --hijau-utama: #4caf50;
            --hijau-muda: #c8e6c9;
            --mobile-breakpoint: 768px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }
        
        /* Header Responsive */
        .simulasi-header {
            background: linear-gradient(135deg, var(--hijau-utama), #2e7d32);
            color: white;
            padding: 1rem 0;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.2);
            position: relative;
        }
        
        @media (min-width: 768px) {
            .simulasi-header {
                padding: 1.5rem 0;
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
            color: var(--hijau-utama) !important;
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
            border-left: 4px solid var(--hijau-utama);
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
        #koordinat-container {
            width: 100%;
            height: 300px;
            background: white;
            border-radius: 10px;
            border: 2px solid var(--hijau-muda);
            margin-bottom: 1rem;
        }
        
        @media (min-width: 576px) {
            #koordinat-container {
                height: 350px;
            }
        }
        
        @media (min-width: 768px) {
            #koordinat-container {
                height: 400px;
            }
        }
        
        @media (min-width: 992px) {
            #koordinat-container {
                height: 450px;
            }
        }
        
        @media (min-width: 1200px) {
            #koordinat-container {
                height: 500px;
            }
        }
        
        /* Titik Info Responsive */
        .titik-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            max-height: 200px;
            overflow-y: auto;
        }
        
        @media (min-width: 768px) {
            .titik-info {
                max-height: 250px;
            }
        }
        
        .titik-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        
        @media (min-width: 768px) {
            .titik-item {
                font-size: 1rem;
            }
        }
        
        .titik-color {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        @media (min-width: 768px) {
            .titik-color {
                width: 20px;
                height: 20px;
            }
        }
        
        /* Buttons Responsive */
        .btn-hijau {
            background: linear-gradient(135deg, var(--hijau-utama), #388e3c);
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
        
        .btn-hijau:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        
        .btn-outline-secondary, .btn-outline-success, .btn-outline-primary {
            min-height: 44px;
            font-size: 0.9rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .btn-hijau, .btn-outline-secondary, .btn-outline-success, .btn-outline-primary {
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
        
        .koordinat-input {
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
            padding: 0.5rem;
            min-height: 40px;
        }
        
        @media (min-width: 768px) {
            .koordinat-input {
                font-size: 1rem;
            }
        }
        
        /* Hasil Persamaan Responsive */
        .hasil-persamaan {
            background: #fff8e1;
            border: 2px solid #ffb300;
            border-radius: 10px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            text-align: center;
            margin: 1rem 0;
        }
        
        #persamaan-hasil {
            font-size: 1rem;
            font-weight: bold;
            color: #d84315;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }
        
        @media (min-width: 576px) {
            #persamaan-hasil {
                font-size: 1.1rem;
            }
        }
        
        @media (min-width: 768px) {
            #persamaan-hasil {
                font-size: 1.2rem;
            }
        }
        
        #persamaan-info {
            font-size: 0.8rem;
        }
        
        /* Petunjuk Cards Responsive */
        .petunjuk-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            height: 100%;
            border: 1px solid #e9ecef;
        }
        
        .petunjuk-card h6 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .petunjuk-card p {
            font-size: 0.8rem;
            margin-bottom: 0;
        }
        
        @media (min-width: 768px) {
            .petunjuk-card h6 {
                font-size: 1rem;
            }
            
            .petunjuk-card p {
                font-size: 0.85rem;
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
            background: var(--hijau-utama);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #388e3c;
        }
        
        /* Responsive Grid Layout */
        @media (max-width: 767px) {
            .row-cols-md-3 > * {
                margin-bottom: 1rem;
            }
            
            .petunjuk-card {
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
                border-radius: 0 0 15px 15px;
            }
        }
        
        /* HP Sangat Kecil */
        @media (max-width: 375px) {
            .simulasi-body {
                padding: 1rem;
            }
            
            #koordinat-container {
                height: 280px;
            }
            
            .koordinat-input {
                font-size: 0.85rem;
                padding: 0.375rem;
            }
            
            .btn-hijau, .btn-outline-secondary, .btn-outline-success, .btn-outline-primary {
                min-height: 42px;
                font-size: 0.8rem;
            }
        }
        
        /* Landscape Mode */
        @media (max-height: 500px) and (orientation: landscape) {
            #koordinat-container {
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
                        <i class="fas fa-crosshairs me-2"></i>Simulasi Koordinat Titik
                    </h1>
                    <p class="mb-0 opacity-90">
                        Tentukan titik dan temukan persamaan garis
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
            <div class="spinner-border text-success mb-2" role="status">
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
                        <h4 class="fw-bold mb-3 text-success">
                            <i class="fas fa-map-marked-alt me-2"></i>Peta Koordinat
                        </h4>
                        
                        <!-- Koordinat Container -->
                        <div id="koordinat-container"></div>
                        
                        <!-- Hasil Persamaan -->
                        <div class="hasil-persamaan mt-3">
                            <div id="persamaan-hasil">Pilih minimal 2 titik</div>
                            <small class="text-muted" id="persamaan-info">Menghitung persamaan garis...</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Control Section -->
            <div class="col-lg-4">
                <div class="simulasi-card fade-in">
                    <div class="simulasi-body">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Titik
                        </h5>
                        
                        <!-- Input Koordinat -->
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Koordinat X</label>
                                    <input type="number" id="input-x" class="form-control koordinat-input" 
                                           min="-10" max="10" value="2" step="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Koordinat Y</label>
                                    <input type="number" id="input-y" class="form-control koordinat-input" 
                                           min="-10" max="10" value="3" step="1">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Tambah -->
                        <button class="btn btn-hijau mb-2" id="tambah-titik-btn">
                            <i class="fas fa-plus me-2"></i>Tambah Titik
                        </button>
                        
                        <!-- Tombol Reset -->
                        <button class="btn btn-outline-secondary mb-3" id="reset-titik-btn">
                            <i class="fas fa-trash me-2"></i>Hapus Semua Titik
                        </button>
                        
                        <!-- Daftar Titik -->
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-list me-2"></i>Titik yang Dipilih
                        </h6>
                        <div class="titik-info">
                            <div id="daftar-titik">
                                <!-- Titik akan ditambahkan di sini -->
                            </div>
                        </div>
                        
                        <!-- Tombol Contoh -->
                        <div class="mt-3">
                            <button class="btn btn-outline-success mb-2" id="contoh-petani-btn">
                                <i class="fas fa-tractor me-2"></i>Contoh: Petani
                            </button>
                            <button class="btn btn-outline-primary" id="contoh-segitiga-btn">
                                <i class="fas fa-shapes me-2"></i>Contoh: Segitiga
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Petunjuk -->
        <div class="simulasi-card fade-in">
            <div class="simulasi-body">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-graduation-cap me-2"></i>Cara Menggunakan Simulasi
                </h5>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="petunjuk-card">
                            <h6 class="fw-bold text-success">
                                <i class="fas fa-1 me-2"></i>Tambah Titik
                            </h6>
                            <p class="mb-0">Masukkan koordinat X dan Y, lalu klik "Tambah Titik"</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="petunjuk-card">
                            <h6 class="fw-bold text-success">
                                <i class="fas fa-2 me-2"></i>Amati Garis
                            </h6>
                            <p class="mb-0">Sistem otomatis menghitung persamaan garis yang melalui titik-titik</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="petunjuk-card">
                            <h6 class="fw-bold text-success">
                                <i class="fas fa-3 me-2"></i>Verifikasi
                            </h6>
                            <p class="mb-0">Coba titik lain untuk memverifikasi apakah berada pada garis yang sama</p>
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
        // Data titik-titik
        let titik = [];
        const warna = ['#FF0000', '#00FF00', '#0000FF', '#FF00FF', '#00FFFF', '#FFA500', '#800080', '#008080'];
        
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
        
        // Update grafik
        function updateGrafik() {
            // Tampilkan loading
            showLoading();
            
            // Data untuk titik
            const titikTrace = {
                x: titik.map(t => t.x),
                y: titik.map(t => t.y),
                mode: 'markers',
                type: 'scatter',
                name: 'Titik',
                marker: {
                    size: titik.length > 10 ? 10 : 12,
                    color: titik.map(t => t.color),
                    symbol: 'circle',
                    line: {
                        color: 'white',
                        width: 2
                    }
                },
                text: titik.map(t => `Titik ${t.label} (${t.x},${t.y})`),
                hoverinfo: 'text'
            };
            
            // Data untuk garis (jika ada minimal 2 titik)
            const garisTraces = [];
            if (titik.length >= 2) {
                // Hitung persamaan garis
                const persamaan = hitungPersamaanGaris();
                
                if (persamaan && !isNaN(persamaan.m)) {
                    // Generate data garis
                    const xGaris = [];
                    const yGaris = [];
                    for (let i = -10; i <= 10; i += 0.5) {
                        xGaris.push(i);
                        yGaris.push(persamaan.m * i + persamaan.c);
                    }
                    
                    garisTraces.push({
                        x: xGaris,
                        y: yGaris,
                        mode: 'lines',
                        type: 'scatter',
                        name: `y = ${persamaan.m.toFixed(2)}x + ${persamaan.c.toFixed(2)}`,
                        line: {
                            color: '#4CAF50',
                            width: 3,
                            dash: 'solid'
                        },
                        hoverinfo: 'none'
                    });
                    
                    // Update persamaan
                    document.getElementById('persamaan-hasil').textContent = 
                        `y = ${persamaan.m.toFixed(2)}x + ${persamaan.c.toFixed(2)}`;
                    document.getElementById('persamaan-info').textContent = 
                        `Melalui ${titik.length} titik`;
                } else {
                    // Garis vertikal atau tidak valid
                    document.getElementById('persamaan-hasil').textContent = 'x = konstan';
                    document.getElementById('persamaan-info').textContent = 'Garis vertikal';
                }
            } else {
                document.getElementById('persamaan-hasil').textContent = 'Pilih minimal 2 titik';
                document.getElementById('persamaan-info').textContent = 'Menghitung persamaan garis...';
            }
            
            // Gabungkan semua traces
            const data = [titikTrace, ...garisTraces];
            
            // Layout responsif
            const layout = {
                title: {
                    text: 'Koordinat Titik dan Garis',
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
                hovermode: 'closest'
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
            Plotly.react('koordinat-container', data, layout, config).then(() => {
                hideLoading();
            });
            
            // Update daftar titik
            updateDaftarTitik();
        }
        
        // Hitung persamaan garis dari titik-titik
        function hitungPersamaanGaris() {
            if (titik.length < 2) return null;
            
            // Gunakan dua titik pertama untuk menghitung
            const titik1 = titik[0];
            const titik2 = titik[1];
            
            // Cek jika garis vertikal
            if (titik2.x === titik1.x) {
                return null; // Garis vertikal, m tidak terdefinisi
            }
            
            // Hitung gradien
            const m = (titik2.y - titik1.y) / (titik2.x - titik1.x);
            
            // Hitung intercept
            const c = titik1.y - m * titik1.x;
            
            return { m, c };
        }
        
        // Update daftar titik
        function updateDaftarTitik() {
            const container = document.getElementById('daftar-titik');
            container.innerHTML = '';
            
            titik.forEach((t, index) => {
                const div = document.createElement('div');
                div.className = 'titik-item';
                div.innerHTML = `
                    <div class="titik-color" style="background-color: ${t.color}"></div>
                    <div class="flex-grow-1">
                        <strong>Titik ${t.label}:</strong> (${t.x}, ${t.y})
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusTitik(${index})" title="Hapus titik">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);
            });
            
            if (titik.length === 0) {
                container.innerHTML = '<p class="text-muted text-center mb-0">Belum ada titik</p>';
            }
        }
        
        // Tambah titik baru
        document.getElementById('tambah-titik-btn').addEventListener('click', function() {
            const x = parseInt(document.getElementById('input-x').value);
            const y = parseInt(document.getElementById('input-y').value);
            
            if (isNaN(x) || isNaN(y)) {
                showToast('Masukkan koordinat yang valid!', 'danger');
                return;
            }
            
            if (x < -10 || x > 10 || y < -10 || y > 10) {
                showToast('Koordinat harus antara -10 dan 10!', 'warning');
                return;
            }
            
            // Cek jika titik sudah ada
            const titikSudahAda = titik.some(t => t.x === x && t.y === y);
            if (titikSudahAda) {
                showToast('Titik ini sudah ada!', 'info');
                return;
            }
            
            const label = String.fromCharCode(65 + titik.length); // A, B, C, ...
            const color = warna[titik.length % warna.length];
            
            titik.push({
                x: x,
                y: y,
                label: label,
                color: color
            });
            
            updateGrafik();
            showToast(`Titik ${label}(${x},${y}) ditambahkan`, 'success');
            
            // Reset input
            document.getElementById('input-x').value = '';
            document.getElementById('input-y').value = '';
            document.getElementById('input-x').focus();
        });
        
        // Hapus titik
        function hapusTitik(index) {
            const titikDihapus = titik[index];
            titik.splice(index, 1);
            updateGrafik();
            showToast(`Titik ${titikDihapus.label} dihapus`, 'info');
        }
        
        // Reset semua titik
        document.getElementById('reset-titik-btn').addEventListener('click', function() {
            if (titik.length > 0) {
                if (confirm('Apakah Anda yakin ingin menghapus semua titik?')) {
                    titik = [];
                    updateGrafik();
                    showToast('Semua titik telah dihapus', 'info');
                }
            } else {
                showToast('Belum ada titik untuk dihapus', 'info');
            }
        });
        
        // Contoh kasus petani
        document.getElementById('contoh-petani-btn').addEventListener('click', function() {
            titik = [
                { x: 2, y: 3, label: 'A', color: '#FF0000' },
                { x: 6, y: 7, label: 'B', color: '#00FF00' },
                { x: 0, y: 1, label: 'C', color: '#0000FF' }
            ];
            updateGrafik();
            showToast('Contoh kasus petani: A(2,3), B(6,7), C(0,1)', 'success');
        });
        
        // Contoh segitiga
        document.getElementById('contoh-segitiga-btn').addEventListener('click', function() {
            titik = [
                { x: 0, y: 0, label: 'A', color: '#FF0000' },
                { x: 4, y: 0, label: 'B', color: '#00FF00' },
                { x: 2, y: 3, label: 'C', color: '#0000FF' }
            ];
            updateGrafik();
            showToast('Contoh segitiga: A(0,0), B(4,0), C(2,3)', 'success');
        });
        
        // Toast notification
        function showToast(message, type = 'info') {
            // Hapus toast sebelumnya jika ada
            const oldToast = document.querySelector('.custom-toast');
            if (oldToast) oldToast.remove();
            
            // Tentukan warna berdasarkan type
            let bgColor = '#4CAF50'; // default success
            if (type === 'danger') bgColor = '#f44336';
            if (type === 'warning') bgColor = '#ff9800';
            if (type === 'info') bgColor = '#2196F3';
            
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
                document.getElementById('koordinat-container').style.height = height + 'px';
                Plotly.relayout('koordinat-container', {
                    height: height
                });
            }, 250);
        });
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial height
            const initialHeight = getGrafikHeight();
            document.getElementById('koordinat-container').style.height = initialHeight + 'px';
            
            // Inisialisasi grafik dengan contoh awal
            titik = [
                { x: 2, y: 3, label: 'A', color: '#FF0000' },
                { x: 6, y: 7, label: 'B', color: '#00FF00' }
            ];
            updateGrafik();
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Perbesar input untuk touch
                document.querySelectorAll('input').forEach(input => {
                    input.style.minHeight = '44px';
                    input.style.fontSize = '16px'; // Mencegah zoom di iOS
                });
            }
            
            // Enter key untuk tambah titik
            document.getElementById('input-y').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('tambah-titik-btn').click();
                }
            });
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