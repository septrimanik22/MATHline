// assets/js/student.js
/**
 * MATHLine - Student Utilities
 * File JavaScript untuk fungsionalitas siswa
 */

class StudentUtils {
    constructor() {
        this.initializeEventListeners();
        this.setupAutoSave();
        this.setupNotifications();
    }

    // Inisialisasi event listeners
    initializeEventListeners() {
        // Toggle sidebar untuk mobile
        const toggleBtn = document.getElementById('toggleSidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleSidebar());
        }

        // Close sidebar saat klik di luar (mobile)
        document.addEventListener('click', (event) => this.handleOutsideClick(event));

        // Auto-capitalize untuk input nama
        const nameInputs = document.querySelectorAll('input[name="full_name"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', (e) => this.autoCapitalizeName(e.target));
        });

        // Validasi form
        const forms = document.querySelectorAll('form[data-validate="true"]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.validateForm(e));
        });

        // Update word count untuk textarea
        const textareas = document.querySelectorAll('textarea[data-word-count="true"]');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', () => this.updateWordCount(textarea));
        });

        // Countdown untuk deadline
        this.initializeCountdowns();
    }

    // Toggle sidebar untuk mobile
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (sidebar && mainContent) {
            sidebar.classList.toggle('active');
            if (sidebar.classList.contains('active')) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '250px';
            }
        }
    }

    // Handle klik di luar sidebar (mobile)
    handleOutsideClick(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        
        if (window.innerWidth <= 768 && 
            sidebar && 
            toggleBtn && 
            !sidebar.contains(event.target) && 
            !toggleBtn.contains(event.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            document.getElementById('mainContent').style.marginLeft = '0';
        }
    }

    // Auto capitalize nama (huruf pertama setiap kata)
    autoCapitalizeName(input) {
        input.value = input.value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    }

    // Validasi form
    validateForm(event) {
        const form = event.target;
        let isValid = true;
        let firstInvalidField = null;

        // Cek semua required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;
                
                // Tambah class error
                field.classList.add('is-invalid');
                
                // Buat pesan error jika belum ada
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Field ini wajib diisi';
                    field.parentNode.appendChild(errorDiv);
                }
            } else {
                field.classList.remove('is-invalid');
                
                // Hapus pesan error jika ada
                const errorDiv = field.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            }
        });

        if (!isValid && firstInvalidField) {
            event.preventDefault();
            firstInvalidField.focus();
            this.showToast('error', 'Harap lengkapi semua field yang wajib diisi');
            return false;
        }

        return true;
    }

    // Update word count untuk textarea
    updateWordCount(textarea) {
        const text = textarea.value.trim();
        const words = text.split(/\s+/).filter(word => word.length > 0);
        const characters = text.length;

        let counter = textarea.parentNode.querySelector('.word-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'word-counter text-muted small mt-1';
            textarea.parentNode.appendChild(counter);
        }

        counter.innerHTML = `
            <i class="fas fa-font me-1"></i>
            Kata: <strong>${words.length}</strong> | 
            Karakter: <strong>${characters}</strong>
        `;
    }

    // Inisialisasi countdown deadline
    initializeCountdowns() {
        const countdownElements = document.querySelectorAll('[data-countdown]');
        countdownElements.forEach(element => {
            const dueDate = new Date(element.getAttribute('data-countdown'));
            this.updateCountdown(element, dueDate);
            
            // Update setiap menit
            setInterval(() => {
                this.updateCountdown(element, dueDate);
            }, 60000);
        });
    }

    // Update countdown
    updateCountdown(element, dueDate) {
        const now = new Date();
        const timeDiff = dueDate - now;

        if (timeDiff > 0) {
            const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));

            if (days > 0) {
                element.innerHTML = `<i class="fas fa-clock me-1"></i>Tersisa: ${days} hari ${hours} jam`;
                element.className = 'badge bg-success';
            } else if (hours > 0) {
                element.innerHTML = `<i class="fas fa-clock me-1"></i>Tersisa: ${hours} jam ${minutes} menit`;
                element.className = 'badge bg-warning';
            } else {
                element.innerHTML = `<i class="fas fa-clock me-1"></i>Tersisa: ${minutes} menit`;
                element.className = 'badge bg-danger';
            }
        } else {
            element.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>Deadline telah lewat`;
            element.className = 'badge bg-danger';
        }
    }

    // Setup auto-save untuk form
    setupAutoSave() {
        const forms = document.querySelectorAll('form[data-auto-save="true"]');
        forms.forEach(form => {
            const formId = form.id || 'form_' + Math.random().toString(36).substr(2, 9);
            form.dataset.formId = formId;

            // Load saved data
            this.loadFormData(form);

            // Auto-save setiap 30 detik
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    this.saveFormData(form);
                });
            });

            // Clear saved data saat submit
            form.addEventListener('submit', () => {
                localStorage.removeItem(`form_${formId}`);
                this.showToast('success', 'Data berhasil disimpan');
            });
        });
    }

    // Save form data ke localStorage
    saveFormData(form) {
        const formId = form.dataset.formId;
        const formData = {};

        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.name) {
                formData[input.name] = input.value;
            }
        });

        localStorage.setItem(`form_${formId}`, JSON.stringify(formData));
    }

    // Load form data dari localStorage
    loadFormData(form) {
        const formId = form.dataset.formId;
        const savedData = localStorage.getItem(`form_${formId}`);

        if (savedData) {
            const formData = JSON.parse(savedData);
            const inputs = form.querySelectorAll('input, textarea, select');

            inputs.forEach(input => {
                if (input.name && formData[input.name] !== undefined) {
                    input.value = formData[input.name];
                }
            });

            // Tampilkan notifikasi bahwa ada data tersimpan
            if (Object.keys(formData).length > 0) {
                this.showToast('info', 'Data draft sebelumnya ditemukan dan dimuat otomatis');
            }
        }
    }

    // Setup notifikasi
    setupNotifications() {
        // Check for new notifications every 2 minutes
        setInterval(() => {
            this.checkNotifications();
        }, 120000);

        // Initial check
        this.checkNotifications();
    }

    // Check for new notifications
    async checkNotifications() {
        try {
            const response = await fetch('../api/check_notifications.php');
            const data = await response.json();

            if (data.success && data.count > 0) {
                this.updateNotificationBadge(data.count);
                
                // Show desktop notification jika diizinkan
                if (Notification.permission === 'granted') {
                    this.showDesktopNotification('MATHLine', `Anda memiliki ${data.count} notifikasi baru`);
                }
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    // Update notification badge
    updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = Math.min(count, 9);
            badge.style.display = 'flex';
        }
    }

    // Show desktop notification
    showDesktopNotification(title, message) {
        if (!('Notification' in window)) return;

        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '../assets/images/logo.png'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification(title, {
                        body: message,
                        icon: '../assets/images/logo.png'
                    });
                }
            });
        }
    }

    // Show toast notification
    showToast(type, message) {
        // Create toast container if not exists
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        // Create toast
        const toastId = 'toast_' + Date.now();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${icons[type] || 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 5000
        });
        bsToast.show();

        // Remove toast after hide
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Format tanggal Indonesia
    formatDate(date, includeTime = false) {
        const d = new Date(date);
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const day = d.getDate();
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        
        let formatted = `${day} ${month} ${year}`;
        
        if (includeTime) {
            const hours = d.getHours().toString().padStart(2, '0');
            const minutes = d.getMinutes().toString().padStart(2, '0');
            formatted += ` ${hours}:${minutes}`;
        }
        
        return formatted;
    }

    // Format angka dengan separator ribuan
    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Confirmation dialog
    confirmDialog(message, callback) {
        if (confirm(message)) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    }

    // Loading spinner
    showLoading(selector = 'body') {
        const container = document.querySelector(selector);
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div class="spinner-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-text mt-2">Memuat...</div>
            </div>
        `;
        
        // Add styles if not exists
        if (!document.querySelector('#loading-styles')) {
            const styles = document.createElement('style');
            styles.id = 'loading-styles';
            styles.textContent = `
                .loading-spinner {
                    position: relative;
                }
                .spinner-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    border-radius: 8px;
                }
            `;
            document.head.appendChild(styles);
        }
        
        container.appendChild(spinner);
    }

    hideLoading(selector = 'body') {
        const spinner = document.querySelector(`${selector} .loading-spinner`);
        if (spinner) {
            spinner.remove();
        }
    }

    // Copy to clipboard
    copyToClipboard(text, showToast = true) {
        navigator.clipboard.writeText(text).then(() => {
            if (showToast) {
                this.showToast('success', 'Berhasil disalin ke clipboard');
            }
        }).catch(err => {
            console.error('Failed to copy: ', err);
            if (showToast) {
                this.showToast('error', 'Gagal menyalin ke clipboard');
            }
        });
    }

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // AJAX helper
    async ajax(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('AJAX error:', error);
            this.showToast('error', 'Terjadi kesalahan saat memuat data');
            throw error;
        }
    }

    // File upload helper
    async uploadFile(file, url, onProgress = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            formData.append('file', file);

            xhr.open('POST', url, true);

            if (onProgress) {
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percentComplete = (event.loaded / event.total) * 100;
                        onProgress(percentComplete);
                    }
                };
            }

            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        resolve(xhr.responseText);
                    }
                } else {
                    reject(new Error(`Upload failed: ${xhr.statusText}`));
                }
            };

            xhr.onerror = () => reject(new Error('Network error during upload'));
            xhr.send(formData);
        });
    }

    // Generate PDF (stub for future implementation)
    generatePDF(elementId, filename = 'document.pdf') {
        console.log(`Generating PDF from element ${elementId} as ${filename}`);
        this.showToast('info', 'Fitur ekspor PDF akan segera tersedia');
    }

    // Export data to CSV
    exportToCSV(data, filename = 'data.csv') {
        if (!Array.isArray(data) || data.length === 0) {
            this.showToast('error', 'Tidak ada data untuk diekspor');
            return;
        }

        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => {
                const cell = row[header];
                return typeof cell === 'string' && cell.includes(',') ? `"${cell}"` : cell;
            }).join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showToast('success', `Data berhasil diekspor sebagai ${filename}`);
    }

    // Print element
    printElement(elementId) {
        const element = document.getElementById(elementId);
        if (!element) {
            this.showToast('error', 'Element tidak ditemukan');
            return;
        }

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @media print {
                            .no-print { display: none !important; }
                        }
                        body { padding: 20px; }
                    </style>
                </head>
                <body>
                    ${element.innerHTML}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() {
                                window.close();
                            };
                        }
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Theme switcher (light/dark mode)
    setupThemeSwitcher() {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            // Check saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            this.setTheme(savedTheme);

            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
            });
        }
    }

    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update icon
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
    }

    // Initialize semua fitur
    init() {
        console.log('StudentUtils initialized');
        this.setupThemeSwitcher();
        
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(() => {
                Notification.requestPermission();
            }, 3000);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.StudentUtils = new StudentUtils();
    window.StudentUtils.init();
});

// Export untuk penggunaan modular (jika menggunakan module system)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StudentUtils;
}