<?php
// student/simulasi_desain.php
require_once '../includes/auth.php';
$auth = new Auth();
$auth->requireRole(['admin', 'guru']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Simulasi Desain Taman - MATHLine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.plot.ly/plotly-2.24.1.min.js"></script>
    <style>
        :root {
            --primary: #1976d2;
            --success: #2e7d32;
            --danger: #f44336;
            --warning: #ff9800;
            --light-bg: #f8fafc;
        }
        
        body {
            background: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        /* Header Responsive */
        .header-taman {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            padding: clamp(1rem, 3vw, 1.8rem) 0;
            border-radius: 0 0 25px 25px;
            box-shadow: 0 6px 25px rgba(46, 125, 50, 0.15);
            margin-bottom: clamp(1rem, 3vw, 2rem);
        }
        
        .header-taman h1 {
            font-weight: 700;
            font-size: clamp(1.2rem, 4vw, 1.8rem);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .header-taman p {
            font-size: clamp(0.8rem, 2vw, 1rem);
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Main Container Responsive */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 clamp(0.5rem, 2vw, 20px);
            width: 100%;
        }
        
        /* Cards Responsive */
        .card-taman {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: clamp(1rem, 3vw, 2rem);
            transition: transform 0.3s ease;
        }
        
        .card-taman:hover {
            transform: translateY(-5px);
        }
        
        .card-header-taman {
            background: linear-gradient(135deg, var(--primary), #1565c0);
            color: white;
            padding: clamp(0.75rem, 2vw, 1.2rem) clamp(0.75rem, 2vw, 1.5rem);
            border-bottom: none;
        }
        
        .card-header-taman h5 {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            margin-bottom: 0;
        }
        
        .card-body-taman {
            padding: clamp(1rem, 2vw, 1.8rem);
        }
        
        /* Canvas Container Responsive */
        .canvas-container {
            background: white;
            border-radius: 12px;
            border: 2px dashed #e0e0e0;
            padding: clamp(0.5rem, 1.5vw, 15px);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            position: relative;
        }
        
        #desain-container {
            width: 100%;
            height: clamp(300px, 50vh, 520px);
            min-height: 300px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e3f2fd;
        }
        
        /* Control Panel Responsive */
        .control-panel {
            background: white;
            border-radius: 15px;
            padding: clamp(1rem, 2vw, 1.5rem);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            height: 100%;
        }
        
        .control-panel h5 {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }
        
        .control-group {
            background: #f8f9fa;
            border-radius: 10px;
            padding: clamp(0.75rem, 2vw, 1.2rem);
            margin-bottom: clamp(0.75rem, 2vw, 1.2rem);
            border: 1px solid #e9ecef;
        }
        
        .control-title {
            font-weight: 600;
            color: #333;
            margin-bottom: clamp(0.5rem, 1.5vw, 1rem);
            font-size: clamp(0.85rem, 1.8vw, 1.1rem);
        }
        
        /* Equation Box Responsive */
        .equation-box {
            background: linear-gradient(135deg, #f3f7ff 0%, #e3f2fd 100%);
            border: 2px solid var(--primary);
            border-radius: 10px;
            padding: clamp(0.75rem, 1.5vw, 1.2rem);
            margin: clamp(0.5rem, 1.5vw, 1rem) 0;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-align: center;
            font-size: clamp(0.9rem, 2vw, 1.2rem);
            color: #1976d2;
        }
        
        /* Buttons Responsive */
        .btn-taman {
            background: linear-gradient(135deg, var(--success), #1b5e20);
            color: white;
            border: none;
            padding: clamp(0.5rem, 1.5vw, 12px) clamp(1rem, 3vw, 30px);
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 10px;
            font-size: clamp(0.8rem, 1.8vw, 0.95rem);
            min-height: 44px;
        }
        
        .btn-taman:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(46, 125, 50, 0.3);
        }
        
        .btn-taman-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        /* Kembali Button Responsive */
        .btn-outline-light {
            min-height: 44px;
            font-size: clamp(0.8rem, 1.8vw, 0.95rem);
            padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.75rem, 2vw, 1rem);
        }
        
        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .header-taman {
                text-align: center;
            }
            
            .header-taman .col-md-4 {
                text-align: center !important;
                margin-top: 1rem;
            }
            
            .card-header-taman {
                text-align: center;
            }
            
            /* Stack columns on mobile */
            .row > div {
                margin-bottom: 1rem;
            }
            
            .row > div:last-child {
                margin-bottom: 0;
            }
            
            /* Improve touch targets */
            button, .btn, input, select, textarea {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        @media (max-width: 576px) {
            .header-taman {
                border-radius: 0 0 15px 15px;
            }
            
            .card-taman {
                border-radius: 12px;
            }
            
            .control-panel {
                border-radius: 12px;
            }
            
            /* Smaller font sizes for very small screens */
            .card-header-taman h5 {
                font-size: 0.9rem;
            }
            
            .control-title {
                font-size: 0.85rem;
            }
            
            .equation-box {
                font-size: 0.85rem;
            }
            
            /* Stack form inputs vertically */
            .row.g-2 {
                --bs-gutter-y: 0.5rem;
            }
            
            /* Optimize canvas height for mobile */
            #desain-container {
                height: clamp(280px, 40vh, 350px);
                min-height: 280px;
            }
        }
        
        @media (max-width: 375px) {
            .main-container {
                padding: 0 0.5rem;
            }
            
            .card-body-taman {
                padding: 0.75rem;
            }
            
            .control-panel {
                padding: 0.75rem;
            }
            
            .control-group {
                padding: 0.75rem;
            }
            
            #desain-container {
                height: 250px;
                min-height: 250px;
            }
            
            .btn-taman {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }
        }
        
        /* Tablet Landscape Optimization */
        @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
            #desain-container {
                height: 350px;
                min-height: 350px;
            }
            
            .card-body-taman {
                padding: 1rem;
            }
        }
        
        /* Large Desktop Optimization */
        @media (min-width: 1400px) {
            .main-container {
                padding: 0 2rem;
            }
            
            #desain-container {
                height: 600px;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .card-taman:hover {
                transform: none;
            }
            
            .design-example:hover {
                transform: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            
            .btn-taman:hover {
                transform: none;
                box-shadow: none;
            }
            
            /* Larger touch targets */
            .form-range::-webkit-slider-thumb {
                width: 28px;
                height: 28px;
            }
        }
        
        /* Slider Custom */
        .form-range::-webkit-slider-thumb {
            background: var(--success);
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .form-range:focus::-webkit-slider-thumb {
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.25);
        }
        
        /* Animations */
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
    <div class="header-taman">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 col-12">
                    <h1 class="mb-2">
                        <i class="fas fa-drafting-compass me-2"></i>
                        Simulasi Desain Taman Sekolah
                    </h1>
                    <p class="mb-0 opacity-90">
                        Aplikasikan konsep garis sejajar dan tegak lurus dalam desain taman yang realistis
                    </p>
                </div>
                <div class="col-md-4 col-12 text-md-end text-center mt-3 mt-md-0">
                    <a href="materials.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-container">
        <div class="row">
            <!-- Left Column - Canvas -->
            <div class="col-lg-8 col-12 mb-3 mb-lg-0">
                <div class="card-taman fade-in">
                    <div class="card-header-taman">
                        <h5 class="mb-0">
                            <i class="fas fa-palette me-2"></i>
                            Canvas Desain Taman
                        </h5>
                    </div>
                    <div class="card-body-taman">
                        <div class="canvas-container">
                            <div id="desain-container"></div>
                        </div>
                        <!-- Legend Helper -->
                        <div class="alert alert-info alert-sm mb-0 mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Klik pada legenda untuk menampilkan/sembunyikan jalur tertentu</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Controls -->
            <div class="col-lg-4 col-12">
                <div class="control-panel fade-in">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-sliders-h me-2"></i>
                        Kontrol Desain
                    </h5>
                    
                    <!-- Jalur Utama -->
                    <div class="control-group">
                        <div class="control-title">
                            <i class="fas fa-road text-primary me-2"></i>
                            Jalur Utama
                        </div>
                        <div class="equation-box">
                            y = 2x + 1
                        </div>
                        <p class="small text-muted mb-0">
                            Jalur utama taman yang sudah ditentukan
                        </p>
                    </div>
                    
                    <!-- Jalur Sejajar -->
                    <div class="control-group">
                        <div class="control-title">
                            <i class="fas fa-grip-lines text-success me-2"></i>
                            Jalur Sejajar
                        </div>
                        <label class="form-label small fw-bold">Jarak dari jalur utama: <span id="jarak-value" class="text-success">3m</span></label>
                        <input type="range" class="form-range" id="jarak-sejajar" 
                               min="-5" max="5" step="1" value="3">
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>-5m</span>
                            <span>0m</span>
                            <span>5m</span>
                        </div>
                        <div class="equation-box mt-3">
                            y = 2x + <span id="c-sejajar" class="fw-bold text-success">7</span>
                        </div>
                    </div>
                    
                    <!-- Jalur Tegak Lurus -->
                    <div class="control-group">
                        <div class="control-title">
                            <i class="fas fa-perpendicular text-danger me-2"></i>
                            Jalur Tegak Lurus
                        </div>
                        <label class="form-label small fw-bold mb-2">Titik Potong dengan jalur utama</label>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">X</span>
                                    <input type="number" id="titik-x" class="form-control" 
                                           min="-10" max="10" value="2" step="0.5">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Y</span>
                                    <input type="number" id="titik-y" class="form-control" 
                                           min="-10" max="10" value="5" step="0.5">
                                </div>
                            </div>
                        </div>
                        <div class="equation-box">
                            y = -0.5x + <span id="c-tegak-lurus" class="fw-bold text-danger">6</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-taman" onclick="updateDesain()">
                            <i class="fas fa-sync-alt me-2"></i>Perbarui Desain
                        </button>
                        <button class="btn btn-taman btn-taman-secondary" onclick="resetDesain()">
                            <i class="fas fa-redo me-2"></i>Reset ke Default
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JavaScript -->
    <script>
        // Data desain
        const jalurUtama = { m: 2, c: 1 };
        let jalurSejajar = { m: 2, c: 7 };
        let jalurTegakLurus = { m: -0.5, c: 6 };
        let titikPotong = { x: 2, y: 5 };
        
        // Fungsi untuk mendapatkan tinggi canvas responsif
        function getCanvasHeight() {
            const width = window.innerWidth;
            if (width < 576) return 320;        // HP kecil (sedikit lebih tinggi untuk legenda)
            if (width < 768) return 380;        // HP besar/tablet kecil
            if (width < 992) return 420;        // Tablet
            if (width < 1200) return 480;       // Laptop kecil
            return 520;                         // Desktop besar
        }
        
        // Inisialisasi desain
        function initDesain() {
            updateJarakValue();
            updateDesain();
            
            // Event listeners
            document.getElementById('jarak-sejajar').addEventListener('input', function() {
                updateJarakValue();
                updateDesain();
            });
            
            document.getElementById('titik-x').addEventListener('change', updateDesain);
            document.getElementById('titik-y').addEventListener('change', updateDesain);
            
            // Responsive canvas height
            updateCanvasHeight();
            window.addEventListener('resize', updateCanvasHeight);
        }
        
        // Update tinggi canvas
        function updateCanvasHeight() {
            const height = getCanvasHeight();
            document.getElementById('desain-container').style.height = height + 'px';
        }
        
        // Update jarak value display
        function updateJarakValue() {
            const jarak = document.getElementById('jarak-sejajar').value;
            document.getElementById('jarak-value').textContent = jarak + 'm';
        }
        
        // Update desain
        function updateDesain() {
            // Update nilai dari input
            const jarak = parseInt(document.getElementById('jarak-sejajar').value);
            jalurSejajar.c = jalurUtama.c + jarak;
            
            titikPotong.x = parseFloat(document.getElementById('titik-x').value);
            titikPotong.y = parseFloat(document.getElementById('titik-y').value);
            
            // Hitung c untuk jalur tegak lurus
            jalurTegakLurus.c = titikPotong.y - jalurTegakLurus.m * titikPotong.x;
            
            // Update display
            document.getElementById('c-sejajar').textContent = jalurSejajar.c;
            document.getElementById('c-tegak-lurus').textContent = jalurTegakLurus.c.toFixed(1);
            
            // Generate data untuk plot
            const xRange = [];
            for (let i = -10; i <= 10; i += 0.5) {
                xRange.push(i);
            }
            
            // Data traces - Diperbaiki untuk legenda yang lebih baik
            const traces = [
                // Area taman - dibuat lebih transparan
                {
                    x: [-10, 10, 10, -10, -10],
                    y: [-10, -10, 10, 10, -10],
                    mode: 'lines',
                    type: 'scatter',
                    name: 'Area Taman',
                    line: {
                        color: '#e8f5e9',
                        width: 2,
                        dash: 'dash'
                    },
                    fill: 'toself',
                    fillcolor: 'rgba(232, 245, 233, 0.05)', // Lebih transparan
                    hoverinfo: 'name',
                    showlegend: true
                },
                // Jalur utama
                {
                    x: xRange,
                    y: xRange.map(x => jalurUtama.m * x + jalurUtama.c),
                    mode: 'lines',
                    type: 'scatter',
                    name: `Jalur Utama`,
                    line: {
                        color: '#1976d2',
                        width: 4
                    },
                    showlegend: true,
                    hoverinfo: 'name+y'
                },
                // Jalur sejajar
                {
                    x: xRange,
                    y: xRange.map(x => jalurSejajar.m * x + jalurSejajar.c),
                    mode: 'lines',
                    type: 'scatter',
                    name: `Jalur Sejajar`,
                    line: {
                        color: '#4caf50',
                        width: 3,
                        dash: 'dash'
                    },
                    showlegend: true,
                    hoverinfo: 'name+y'
                },
                // Jalur tegak lurus
                {
                    x: xRange,
                    y: xRange.map(x => jalurTegakLurus.m * x + jalurTegakLurus.c),
                    mode: 'lines',
                    type: 'scatter',
                    name: `Jalur Tegak Lurus`,
                    line: {
                        color: '#f44336',
                        width: 3,
                        dash: 'dot'
                    },
                    showlegend: true,
                    hoverinfo: 'name+y'
                },
                // Titik potong - tanpa text label agar tidak bertabrakan
                {
                    x: [titikPotong.x],
                    y: [titikPotong.y],
                    mode: 'markers',
                    type: 'scatter',
                    name: `Titik Potong`,
                    marker: {
                        size: window.innerWidth < 768 ? 10 : 12,
                        color: '#ff9800',
                        symbol: 'circle',
                        line: {
                            color: 'white',
                            width: 2
                        }
                    },
                    showlegend: false, // Tidak ditampilkan di legenda
                    hoverinfo: 'x+y'
                }
            ];
            
            // Layout responsif - Diperbaiki untuk mencegah overlap
            const isMobile = window.innerWidth < 768;
            const isSmall = window.innerWidth < 576;
            
            const layout = {
                title: {
                    text: '<b>Desain Taman Sekolah</b>',
                    font: {
                        size: isSmall ? 14 : (isMobile ? 16 : 20),
                        color: '#1976d2'
                    },
                    x: 0.5,
                    xanchor: 'center',
                    y: isMobile ? 0.95 : 0.98,
                    pad: {
                        b: isMobile ? 20 : 10
                    }
                },
                xaxis: {
                    title: {
                        text: '<b>Koordinat X (meter)</b>',
                        font: {
                            size: isSmall ? 10 : (isMobile ? 11 : 13),
                            color: '#666'
                        }
                    },
                    range: [-10, 10],
                    gridcolor: 'rgba(0, 0, 0, 0.08)',
                    zerolinecolor: '#ccc',
                    zerolinewidth: 1,
                    showgrid: true,
                    tickmode: 'linear',
                    tick0: -10,
                    dtick: isSmall ? 4 : 2,
                    tickfont: {
                        size: isSmall ? 9 : (isMobile ? 10 : 11)
                    },
                    tickangle: isSmall ? -45 : 0, // Rotasi label untuk mobile
                    tickformat: isSmall ? '.0f' : '.0f',
                    side: 'bottom',
                    position: isMobile ? 0.05 : 0,
                    ticklen: 5,
                    tickwidth: 1
                },
                yaxis: {
                    title: {
                        text: '<b>Koordinat Y (meter)</b>',
                        font: {
                            size: isSmall ? 10 : (isMobile ? 11 : 13),
                            color: '#666'
                        }
                    },
                    range: [-10, 10],
                    gridcolor: 'rgba(0, 0, 0, 0.08)',
                    zerolinecolor: '#ccc',
                    zerolinewidth: 1,
                    showgrid: true,
                    tickmode: 'linear',
                    tick0: -10,
                    dtick: isSmall ? 4 : 2,
                    tickfont: {
                        size: isSmall ? 9 : (isMobile ? 10 : 11)
                    },
                    tickformat: '.0f'
                },
                plot_bgcolor: 'white',
                paper_bgcolor: 'white',
                showlegend: true,
                legend: {
                    x: 0.5,
                    y: isMobile ? -0.45 : -0.25, // Posisi lebih rendah untuk mobile
                    xanchor: 'center',
                    yanchor: 'top',
                    orientation: 'h',
                    bgcolor: 'rgba(255, 255, 255, 0.95)',
                    bordercolor: '#e0e0e0',
                    borderwidth: 1,
                    font: {
                        size: isSmall ? 9 : (isMobile ? 10 : 11)
                    },
                    itemsizing: 'constant',
                    itemwidth: isMobile ? 60 : 80,
                    traceorder: 'normal',
                    tracegroupgap: 5,
                    x: 0.5,
                    xanchor: 'center'
                },
                margin: {
                    l: isSmall ? 50 : (isMobile ? 55 : 70),
                    r: isSmall ? 30 : (isMobile ? 35 : 50),
                    b: isMobile ? 120 : 100, // Bottom margin lebih besar untuk legenda
                    t: isSmall ? 50 : (isMobile ? 60 : 80),
                    pad: 5
                },
                annotations: [
                    {
                        x: titikPotong.x,
                        y: titikPotong.y,
                        text: `(${titikPotong.x.toFixed(1)},${titikPotong.y.toFixed(1)})`,
                        showarrow: true,
                        arrowhead: 2,
                        arrowsize: 1,
                        arrowwidth: 1.5,
                        arrowcolor: '#ff9800',
                        ax: isMobile ? 20 : 30,
                        ay: isMobile ? -30 : -40,
                        font: {
                            size: isSmall ? 9 : (isMobile ? 10 : 11),
                            color: '#ff9800'
                        },
                        bgcolor: 'white',
                        bordercolor: '#ff9800',
                        borderwidth: 1,
                        borderpad: 3,
                        opacity: 0.9
                    }
                ],
                hovermode: 'closest',
                dragmode: false
            };
            
            // Konfigurasi
            const config = {
                responsive: true,
                displayModeBar: true,
                displaylogo: false,
                modeBarButtonsToRemove: ['pan2d', 'select2d', 'lasso2d', 'autoScale2d', 'zoom2d'],
                modeBarButtonsToAdd: [],
                scrollZoom: false,
                showTips: true,
                showAxisDragHandles: false,
                showAxisRangeEntryBoxes: false
            };
            
            // Render plot
            Plotly.newPlot('desain-container', traces, layout, config).then(function() {
                // Tambahkan event untuk klik legenda
                const graph = document.getElementById('desain-container');
                graph.on('plotly_legendclick', function(event) {
                    // Biarkan perilaku default (menyembunyikan/menampilkan trace)
                    return true;
                });
            });
        }
        
        // Reset desain ke default
        function resetDesain() {
            if (confirm('Reset desain ke pengaturan default?')) {
                document.getElementById('jarak-sejajar').value = 3;
                document.getElementById('titik-x').value = 2;
                document.getElementById('titik-y').value = 5;
                updateJarakValue();
                updateDesain();
                
                // Show success message
                showNotification('Desain telah direset ke pengaturan default!', 'success');
            }
        }
        
        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'var(--success)' : 'var(--primary)';
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
                max-width: 300px;
            `;
            notification.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Tambahkan style untuk animasi notifikasi
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            
            /* Perbaikan untuk legenda Plotly */
            .legend {
                pointer-events: all !important;
            }
            
            .legend text {
                cursor: pointer !important;
            }
            
            .legendtoggle {
                cursor: pointer !important;
            }
        `;
        document.head.appendChild(style);
        
        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', initDesain);
        
        // Handle window resize untuk memperbarui layout
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateDesain();
            }, 250);
        });
    </script>
</body>
</html>