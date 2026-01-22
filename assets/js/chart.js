// assets/js/chart.js
/**
 * MATHLine - Chart Utilities
 * Visualisasi data untuk siswa
 */

class StudentCharts {
    constructor() {
        this.charts = new Map();
        this.colors = {
            primary: '#667eea',
            secondary: '#764ba2',
            success: '#1cc88a',
            info: '#36b9cc',
            warning: '#f6c23e',
            danger: '#e74a3b',
            light: '#f8f9fa',
            dark: '#5a5c69'
        };

        this.theme = localStorage.getItem('theme') || 'light';
        this.initializeThemeListener();
    }

    // Inisialisasi listener untuk tema
    initializeThemeListener() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'data-theme') {
                    this.theme = document.documentElement.getAttribute('data-theme') || 'light';
                    this.updateAllChartsTheme();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    }

    // Update semua chart sesuai tema
    updateAllChartsTheme() {
        this.charts.forEach((chart, id) => {
            const isDark = this.theme === 'dark';
            chart.options.plugins.legend.labels.color = isDark ? '#fff' : '#666';
            chart.options.scales.x.ticks.color = isDark ? '#fff' : '#666';
            chart.options.scales.y.ticks.color = isDark ? '#fff' : '#666';
            chart.update();
        });
    }

    // Buat chart progress per fase
    createPhaseProgressChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        const labels = data.map(item => item.phase);
        const submitted = data.map(item => item.submitted);
        const total = data.map(item => item.total);
        const percentages = data.map(item => item.percentage);

        // Warna berdasarkan persentase
        const backgroundColors = percentages.map(p => {
            if (p >= 80) return this.colors.success;
            if (p >= 60) return this.colors.warning;
            return this.colors.danger;
        });

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Progress (%)',
                    data: percentages,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(c => this.darkenColor(c, 20)),
                    borderWidth: 1,
                    borderRadius: 5,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: isDark ? '#fff' : '#666',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const phaseData = data[context.dataIndex];
                                return [
                                    `Progress: ${phaseData.percentage}%`,
                                    `Terkumpul: ${phaseData.submitted}/${phaseData.total}`,
                                    `Nilai Rata-rata: ${phaseData.average_score || 0}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: isDark ? '#fff' : '#666'
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat chart nilai per tugas
    createScoresChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        const labels = data.map(item => item.title.substring(0, 15) + (item.title.length > 15 ? '...' : ''));
        const scores = data.map(item => item.score);
        const maxScores = data.map(item => item.max_score);
        const percentages = data.map(item => Math.round((item.score / item.max_score) * 100));

        // Line chart untuk trend nilai
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Nilai (%)',
                        data: percentages,
                        borderColor: this.colors.primary,
                        backgroundColor: this.hexToRgba(this.colors.primary, 0.1),
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: this.colors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Nilai Mentah',
                        data: scores,
                        borderColor: this.colors.success,
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        borderDash: [5, 5],
                        pointBackgroundColor: this.colors.success,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: isDark ? '#fff' : '#666',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return `Persentase: ${context.parsed.y}%`;
                                } else {
                                    const dataItem = data[context.dataIndex];
                                    return `Nilai: ${dataItem.score}/${dataItem.max_score}`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            maxRotation: 45
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        min: 0,
                        max: 100,
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        title: {
                            display: true,
                            text: 'Persentase (%)',
                            color: isDark ? '#fff' : '#666'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 0,
                        ticks: {
                            color: isDark ? '#fff' : '#666'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Nilai Mentah',
                            color: isDark ? '#fff' : '#666'
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat pie chart untuk distribusi status tugas
    createSubmissionStatusChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        const labels = ['Sudah Dinilai', 'Menunggu', 'Dikembalikan', 'Belum Dikerjakan'];
        const values = [
            data.graded || 0,
            data.pending || 0,
            data.returned || 0,
            data.unsubmitted || 0
        ];

        const backgroundColors = [
            this.colors.success,
            this.colors.warning,
            this.colors.danger,
            this.colors.secondary
        ];

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: backgroundColors,
                    borderColor: isDark ? '#333' : '#fff',
                    borderWidth: 2,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: isDark ? '#fff' : '#666',
                            padding: 20,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.parsed / total) * 100);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat radar chart untuk kompetensi
    createCompetencyRadarChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        const competencies = ['Konsep Dasar', 'Perhitungan', 'Pemecahan Masalah', 'Aplikasi', 'Analisis'];
        
        // Data contoh - dalam implementasi nyata akan dari API
        const scores = data || [85, 70, 90, 75, 80];

        const chart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: competencies,
                datasets: [{
                    label: 'Tingkat Kemampuan',
                    data: scores,
                    backgroundColor: this.hexToRgba(this.colors.primary, 0.2),
                    borderColor: this.colors.primary,
                    pointBackgroundColor: this.colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        pointLabels: {
                            color: isDark ? '#fff' : '#666',
                            font: {
                                size: 11
                            }
                        },
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            backdropColor: 'transparent',
                            showLabelBackdrop: false
                        },
                        min: 0,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: isDark ? '#fff' : '#666'
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat heatmap untuk aktivitas belajar
    createActivityHeatmap(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        
        // Data contoh - dalam implementasi nyata akan dari API
        const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        const weeks = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        
        // Generate sample data
        const chartData = {
            labels: days,
            datasets: weeks.map((week, weekIndex) => ({
                label: week,
                data: days.map(() => Math.floor(Math.random() * 5)), // 0-4 activity level
                backgroundColor: days.map((_, dayIndex) => {
                    const activityLevel = Math.floor(Math.random() * 5);
                    return this.getHeatmapColor(activityLevel);
                })
            }))
        };

        const chart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: isDark ? '#fff' : '#666'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const activityLevel = context.raw;
                                const activityText = ['Tidak ada', 'Sedikit', 'Sedang', 'Banyak', 'Sangat Banyak'];
                                return `Aktivitas: ${activityText[activityLevel]}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: isDark ? '#fff' : '#666'
                        }
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            callback: function(value) {
                                const levels = ['Tidak ada', 'Sedikit', 'Sedang', 'Banyak', 'Sangat Banyak'];
                                return levels[value] || '';
                            }
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat gauge chart untuk progress keseluruhan
    createProgressGauge(canvasId, percentage) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        const gaugeColor = percentage >= 80 ? this.colors.success : 
                          percentage >= 60 ? this.colors.warning : 
                          this.colors.danger;

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percentage, 100 - percentage],
                    backgroundColor: [gaugeColor, isDark ? '#333' : '#eee'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [{
                id: 'gaugeText',
                afterDraw: (chart) => {
                    const { ctx, chartArea: { width, height } } = chart;
                    ctx.save();
                    
                    const text = `${percentage}%`;
                    const subText = 'Progress Total';
                    
                    // Draw main text
                    ctx.font = 'bold 24px Arial';
                    ctx.fillStyle = isDark ? '#fff' : '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(text, width / 2, height / 2 - 10);
                    
                    // Draw subtext
                    ctx.font = '12px Arial';
                    ctx.fillStyle = isDark ? '#aaa' : '#666';
                    ctx.fillText(subText, width / 2, height / 2 + 15);
                    
                    ctx.restore();
                }
            }]
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Buat timeline untuk riwayat belajar
    createLearningTimeline(canvasId, events) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const isDark = this.theme === 'dark';
        
        // Data contoh
        const timelineData = events || [
            { date: '2024-01-01', event: 'Mulai Belajar', type: 'start' },
            { date: '2024-01-05', event: 'Selesaikan Fase 1', type: 'milestone' },
            { date: '2024-01-10', event: 'Nilai Tertinggi', type: 'achievement' },
            { date: '2024-01-15', event: 'Selesaikan Fase 2', type: 'milestone' },
            { date: '2024-01-20', event: 'Aktif 15 Hari Berturut', type: 'streak' }
        ];

        const dates = timelineData.map(item => new Date(item.date).toLocaleDateString('id-ID'));
        const eventTypes = timelineData.map(item => item.type);

        const typeColors = {
            'start': this.colors.primary,
            'milestone': this.colors.success,
            'achievement': this.colors.warning,
            'streak': this.colors.info
        };

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Event',
                    data: eventTypes.map((_, i) => i + 1),
                    borderColor: this.colors.primary,
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointBackgroundColor: eventTypes.map(type => typeColors[type] || this.colors.secondary),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const event = timelineData[context.dataIndex];
                                return event.event;
                            },
                            afterLabel: function(context) {
                                const event = timelineData[context.dataIndex];
                                return `Tanggal: ${dates[context.dataIndex]}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: isDark ? '#fff' : '#666',
                            maxRotation: 45
                        },
                        grid: {
                            color: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        }
                    },
                    y: {
                        display: false
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Helper function: Darken color
    darkenColor(color, percent) {
        const num = parseInt(color.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        
        return '#' + (
            0x1000000 +
            (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)
        ).toString(16).slice(1);
    }

    // Helper function: Hex to RGBA
    hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Helper function: Get heatmap color
    getHeatmapColor(level) {
        const colors = [
            this.hexToRgba(this.colors.light, 0.5),
            this.hexToRgba(this.colors.info, 0.3),
            this.hexToRgba(this.colors.success, 0.5),
            this.hexToRgba(this.colors.warning, 0.7),
            this.hexToRgba(this.colors.danger, 0.9)
        ];
        return colors[level] || colors[0];
    }

    // Destroy chart by ID
    destroyChart(canvasId) {
        const chart = this.charts.get(canvasId);
        if (chart) {
            chart.destroy();
            this.charts.delete(canvasId);
        }
    }

    // Destroy all charts
    destroyAllCharts() {
        this.charts.forEach((chart, id) => {
            chart.destroy();
        });
        this.charts.clear();
    }

    // Export chart as image
    exportChartAsImage(canvasId, filename = 'chart.png') {
        const chart = this.charts.get(canvasId);
        if (!chart) {
            console.error('Chart not found');
            return;
        }

        const link = document.createElement('a');
        link.download = filename;
        link.href = chart.toBase64Image();
        link.click();
    }

    // Update chart data
    updateChartData(canvasId, newData) {
        const chart = this.charts.get(canvasId);
        if (!chart) {
            console.error('Chart not found');
            return;
        }

        chart.data = newData;
        chart.update();
    }

    // Responsive chart resizing
    setupResponsiveCharts() {
        window.addEventListener('resize', () => {
            this.charts.forEach(chart => {
                chart.resize();
            });
        });
    }

    // Initialize semua chart yang ada di halaman
    initializePageCharts() {
        // Cari semua canvas dengan class khusus
        const chartCanvases = document.querySelectorAll('[data-chart-type]');
        
        chartCanvases.forEach(canvas => {
            const chartType = canvas.getAttribute('data-chart-type');
            const chartData = canvas.getAttribute('data-chart-data');
            
            try {
                const data = chartData ? JSON.parse(chartData) : null;
                
                switch(chartType) {
                    case 'phase-progress':
                        this.createPhaseProgressChart(canvas.id, data);
                        break;
                    case 'scores':
                        this.createScoresChart(canvas.id, data);
                        break;
                    case 'submission-status':
                        this.createSubmissionStatusChart(canvas.id, data);
                        break;
                    case 'competency-radar':
                        this.createCompetencyRadarChart(canvas.id, data);
                        break;
                    case 'activity-heatmap':
                        this.createActivityHeatmap(canvas.id, data);
                        break;
                    case 'progress-gauge':
                        this.createProgressGauge(canvas.id, data);
                        break;
                    case 'learning-timeline':
                        this.createLearningTimeline(canvas.id, data);
                        break;
                    default:
                        console.warn(`Unknown chart type: ${chartType}`);
                }
            } catch (error) {
                console.error(`Error creating chart ${canvas.id}:`, error);
            }
        });

        this.setupResponsiveCharts();
    }

    // Initialize
    init() {
        console.log('StudentCharts initialized');
        this.initializePageCharts();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah Chart.js sudah dimuat
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Please include Chart.js before chart.js');
        return;
    }

    window.StudentCharts = new StudentCharts();
    window.StudentCharts.init();
});

// Export untuk penggunaan modular
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StudentCharts;
}