// assets/js/admin.js
// JavaScript khusus untuk panel admin

// ========== DASHBOARD FUNCTIONS ==========

/**
 * Update dashboard statistics secara real-time
 */
function updateDashboardStats() {
    const updateElements = () => {
        // Update current time
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        
        // Update date
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    };
    
    // Update segera
    updateElements();
    
    // Update setiap detik
    setInterval(updateElements, 1000);
}

/**
 * Load chart data untuk dashboard
 */
function loadDashboardCharts() {
    // Student Progress Chart
    const progressCtx = document.getElementById('studentProgressChart');
    if (progressCtx) {
        new Chart(progressCtx, {
            type: 'bar',
            data: {
                labels: ['Fase 1', 'Fase 2', 'Fase 3', 'Fase 4', 'Fase 5'],
                datasets: [{
                    label: 'Siswa yang Menyelesaikan',
                    data: [85, 72, 68, 55, 45],
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgb(102, 126, 234)',
                        'rgb(118, 75, 162)',
                        'rgb(28, 200, 138)',
                        'rgb(54, 185, 204)',
                        'rgb(255, 193, 7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Progress Siswa per Fase PBL'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Persentase (%)'
                        }
                    }
                }
            }
        });
    }
    
    // Submission Status Chart
    const statusCtx = document.getElementById('submissionStatusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Belum Dinilai', 'Sudah Dinilai', 'Dikembalikan'],
                datasets: [{
                    data: [15, 65, 20],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgb(255, 193, 7)',
                        'rgb(28, 200, 138)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Status Pengumpulan Tugas'
                    }
                }
            }
        });
    }
}

// ========== STUDENT MANAGEMENT ==========

/**
 * Bulk actions untuk manajemen siswa
 */
function setupStudentBulkActions() {
    const bulkCheckbox = document.getElementById('bulkSelectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    
    if (!bulkCheckbox || !studentCheckboxes.length) return;
    
    // Toggle select all
    bulkCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActions();
    });
    
    // Update select all checkbox
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
            const anyChecked = Array.from(studentCheckboxes).some(cb => cb.checked);
            
            bulkCheckbox.checked = allChecked;
            bulkCheckbox.indeterminate = anyChecked && !allChecked;
            
            updateBulkActions();
        });
    });
    
    function updateBulkActions() {
        const selectedCount = Array.from(studentCheckboxes).filter(cb => cb.checked).length;
        
        if (selectedCount > 0) {
            bulkActions.classList.remove('d-none');
            document.getElementById('selectedCount').textContent = selectedCount;
        } else {
            bulkActions.classList.add('d-none');
        }
    }
    
    // Bulk delete
    const bulkDeleteBtn = document.getElementById('bulkDelete');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedIds = Array.from(studentCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                MATHLine.showToast('Pilih setidaknya satu siswa', 'warning');
                return;
            }
            
            MATHLine.showConfirm({
                title: 'Hapus Siswa Terpilih',
                message: `Anda akan menghapus ${selectedIds.length} siswa. Tindakan ini tidak dapat dibatalkan.`,
                confirmText: 'Hapus',
                cancelText: 'Batal',
                confirmColor: 'danger'
            }).then(confirmed => {
                if (confirmed) {
                    const loading = MATHLine.showLoading('Menghapus siswa...');
                    
                    // Kirim request ke server
                    fetch('../api/bulk_delete_students.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ student_ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        loading.hide();
                        
                        if (data.success) {
                            MATHLine.showToast(data.message, 'success');
                            // Reload halaman setelah 2 detik
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            MATHLine.showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        loading.hide();
                        MATHLine.showToast('Terjadi kesalahan saat menghapus', 'error');
                        console.error('Error:', error);
                    });
                }
            });
        });
    }
}

/**
 * Export data siswa ke CSV
 */
function exportStudentsToCSV() {
    const loading = MATHLine.showLoading('Mengekspor data...');
    
    fetch('../api/export_students.php')
        .then(response => response.blob())
        .then(blob => {
            loading.hide();
            
            // Buat download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `siswa_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            MATHLine.showToast('Data berhasil diekspor', 'success');
        })
        .catch(error => {
            loading.hide();
            MATHLine.showToast('Gagal mengekspor data', 'error');
            console.error('Error:', error);
        });
}

// ========== SUBMISSION MANAGEMENT ==========

/**
 * Quick grading untuk tugas
 */
function setupQuickGrading() {
    const gradeButtons = document.querySelectorAll('.quick-grade-btn');
    
    gradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const submissionId = this.getAttribute('data-submission-id');
            const studentName = this.getAttribute('data-student-name');
            const problemTitle = this.getAttribute('data-problem-title');
            const maxPoints = this.getAttribute('data-max-points');
            
            // Show quick grade modal
            showQuickGradeModal(submissionId, studentName, problemTitle, maxPoints);
        });
    });
}

/**
 * Tampilkan modal quick grade
 */
function showQuickGradeModal(submissionId, studentName, problemTitle, maxPoints) {
    const modalHtml = `
        <div class="modal fade" id="quickGradeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="small text-muted">Siswa</div>
                            <div class="fw-bold">${studentName}</div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-muted">Tugas</div>
                            <div class="fw-bold">${problemTitle}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickScore" class="form-label">Nilai (0-${maxPoints})</label>
                            <input type="number" class="form-control" id="quickScore" 
                                   min="0" max="${maxPoints}" value="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="quickFeedback" class="form-label">Feedback Singkat</label>
                            <textarea class="form-control" id="quickFeedback" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="saveQuickGrade">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Tambah modal ke body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHtml;
    document.body.appendChild(modalContainer);
    
    // Inisialisasi modal
    const modal = new bootstrap.Modal(document.getElementById('quickGradeModal'));
    modal.show();
    
    // Handle save button
    document.getElementById('saveQuickGrade').addEventListener('click', function() {
        const score = document.getElementById('quickScore').value;
        const feedback = document.getElementById('quickFeedback').value;
        
        if (!score || score < 0 || score > maxPoints) {
            MATHLine.showToast('Nilai tidak valid', 'error');
            return;
        }
        
        const loading = MATHLine.showLoading('Menyimpan nilai...');
        
        fetch('../api/quick_grade.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                submission_id: submissionId,
                score: score,
                feedback: feedback
            })
        })
        .then(response => response.json())
        .then(data => {
            loading.hide();
            modal.hide();
            
            if (data.success) {
                MATHLine.showToast('Nilai berhasil disimpan', 'success');
                // Update UI
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                MATHLine.showToast(data.message, 'error');
            }
        })
        .catch(error => {
            loading.hide();
            MATHLine.showToast('Terjadi kesalahan', 'error');
            console.error('Error:', error);
        });
    });
    
    // Hapus modal setelah ditutup
    modalContainer.addEventListener('hidden.bs.modal', function() {
        modalContainer.remove();
    });
}

// ========== PROGRESS MONITORING ==========

/**
 * Filter dan sort progress siswa
 */
function setupProgressFilter() {
    const filterInput = document.getElementById('progressFilter');
    const sortSelect = document.getElementById('progressSort');
    const progressRows = document.querySelectorAll('#progressTable tbody tr');
    
    if (!filterInput || !sortSelect) return;
    
    filterInput.addEventListener('input', function() {
        const filterText = this.value.toLowerCase();
        
        progressRows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            const className = row.querySelector('.class-name').textContent.toLowerCase();
            
            if (studentName.includes(filterText) || className.includes(filterText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    sortSelect.addEventListener('change', function() {
        const sortBy = this.value;
        const tbody = document.querySelector('#progressTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch (sortBy) {
                case 'name':
                    aValue = a.querySelector('.student-name').textContent;
                    bValue = b.querySelector('.student-name').textContent;
                    return aValue.localeCompare(bValue);
                    
                case 'score':
                    aValue = parseFloat(a.querySelector('.average-score').textContent) || 0;
                    bValue = parseFloat(b.querySelector('.average-score').textContent) || 0;
                    return bValue - aValue;
                    
                case 'submissions':
                    aValue = parseInt(a.querySelector('.total-submissions').textContent) || 0;
                    bValue = parseInt(b.querySelector('.total-submissions').textContent) || 0;
                    return bValue - aValue;
                    
                default:
                    return 0;
            }
        });
        
        // Hapus dan tambah ulang rows yang sudah di-sort
        rows.forEach(row => tbody.appendChild(row));
    });
}

/**
 * Generate progress report
 */
function generateProgressReport() {
    const loading = MATHLine.showLoading('Membuat laporan...');
    
    const classFilter = document.getElementById('classFilter')?.value || '';
    const format = document.getElementById('reportFormat')?.value || 'pdf';
    
    fetch(`../api/generate_progress_report.php?class=${classFilter}&format=${format}`)
        .then(response => response.blob())
        .then(blob => {
            loading.hide();
            
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `laporan_progress_${new Date().toISOString().split('T')[0]}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            MATHLine.showToast('Laporan berhasil dibuat', 'success');
        })
        .catch(error => {
            loading.hide();
            MATHLine.showToast('Gagal membuat laporan', 'error');
            console.error('Error:', error);
        });
}

// ========== SYSTEM FUNCTIONS ==========

/**
 * Backup database
 */
function backupDatabase() {
    MATHLine.showConfirm({
        title: 'Backup Database',
        message: 'Anda akan membuat backup database. Proses ini mungkin memakan waktu beberapa saat.',
        confirmText: 'Backup',
        cancelText: 'Batal',
        confirmColor: 'warning'
    }).then(confirmed => {
        if (confirmed) {
            const loading = MATHLine.showLoading('Membuat backup...');
            
            fetch('../api/backup_database.php')
                .then(response => response.blob())
                .then(blob => {
                    loading.hide();
                    
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `backup_${new Date().toISOString().replace(/[:.]/g, '-')}.sql`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    MATHLine.showToast('Backup berhasil dibuat', 'success');
                })
                .catch(error => {
                    loading.hide();
                    MATHLine.showToast('Gagal membuat backup', 'error');
                    console.error('Error:', error);
                });
        }
    });
}

/**
 * Clear system cache
 */
function clearSystemCache() {
    MATHLine.showConfirm({
        title: 'Bersihkan Cache',
        message: 'Anda akan membersihkan cache sistem. Ini akan meningkatkan performa sistem.',
        confirmText: 'Bersihkan',
        cancelText: 'Batal',
        confirmColor: 'info'
    }).then(confirmed => {
        if (confirmed) {
            const loading = MATHLine.showLoading('Membersihkan cache...');
            
            fetch('../api/clear_cache.php')
                .then(response => response.json())
                .then(data => {
                    loading.hide();
                    
                    if (data.success) {
                        MATHLine.showToast(data.message, 'success');
                    } else {
                        MATHLine.showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    loading.hide();
                    MATHLine.showToast('Terjadi kesalahan', 'error');
                    console.error('Error:', error);
                });
        }
    });
}

// ========== INITIALIZATION ==========

/**
 * Inisialisasi fungsi admin
 */
function initializeAdmin() {
    // Dashboard functions
    if (document.querySelector('.admin-dashboard')) {
        updateDashboardStats();
        loadDashboardCharts();
    }
    
    // Student management functions
    if (document.querySelector('.student-management')) {
        setupStudentBulkActions();
        
        const exportBtn = document.getElementById('exportStudentsBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportStudentsToCSV);
        }
    }
    
    // Submission management functions
    if (document.querySelector('.submission-management')) {
        setupQuickGrading();
    }
    
    // Progress monitoring functions
    if (document.querySelector('.progress-monitoring')) {
        setupProgressFilter();
        
        const generateReportBtn = document.getElementById('generateReportBtn');
        if (generateReportBtn) {
            generateReportBtn.addEventListener('click', generateProgressReport);
        }
    }
    
    // System functions
    const backupBtn = document.getElementById('backupDatabaseBtn');
    if (backupBtn) {
        backupBtn.addEventListener('click', backupDatabase);
    }
    
    const clearCacheBtn = document.getElementById('clearCacheBtn');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', clearSystemCache);
    }
}

// Auto-initialize saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Jalankan fungsi umum terlebih dahulu
    MATHLine.initializeApp();
    
    // Kemudian jalankan fungsi admin
    initializeAdmin();
});