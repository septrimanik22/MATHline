/**
 * MATHLine - Main JavaScript File
 * File utama yang di-load di semua halaman
 */

// IIFE untuk menghindari polusi global scope
(function() {
    'use strict';
    
    // DOM Ready Function
    document.addEventListener('DOMContentLoaded', function() {
        console.log('MATHLine loaded successfully');
        
        // Initialize semua komponen
        initComponents();
        initEventListeners();
        initAnimations();
        
        // Cek jika ada pesan alert
        handleAlerts();
        
        // Cek jika ada form
        handleForms();
    });
    
    /**
     * Initialize semua komponen
     */
    function initComponents() {
        // Initialize tooltips
        initTooltips();
        
        // Initialize popovers
        initPopovers();
        
        // Initialize modals
        initModals();
        
        // Initialize dropdowns
        initDropdowns();
        
        // Initialize sidebar untuk mobile
        initMobileSidebar();
    }
    
    /**
     * Initialize tooltips Bootstrap
     */
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            });
        });
    }
    
    /**
     * Initialize popovers Bootstrap
     */
    function initPopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
    
    /**
     * Initialize modals Bootstrap
     */
    function initModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                // Auto focus pada input pertama dalam modal
                const input = this.querySelector('input, textarea, select');
                if (input) {
                    input.focus();
                }
            });
            
            // Clear form saat modal ditutup
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form && form.hasAttribute('data-clear-on-close')) {
                    form.reset();
                }
            });
        });
    }
    
    /**
     * Initialize dropdowns
     */
    function initDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function() {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                const parent = dropdown.closest('.dropdown');
                if (parent) {
                    const toggle = parent.querySelector('.dropdown-toggle');
                    if (toggle) {
                        bootstrap.Dropdown.getInstance(toggle)?.hide();
                    }
                }
            });
        });
    }
    
    /**
     * Initialize mobile sidebar
     */
    function initMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        
        if (sidebar && toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            });
            
            // Tutup sidebar saat klik di luar (mobile only)
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    sidebar.classList.contains('active') &&
                    !sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }
    }
    
    /**
     * Initialize event listeners
     */
    function initEventListeners() {
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });
            
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Navbar shadow on scroll
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    navbar.classList.add('shadow-sm');
                } else {
                    navbar.classList.remove('shadow-sm');
                }
            });
        }
        
        // Auto-dismiss alerts setelah 5 detik
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Konfirmasi untuk aksi delete
        document.querySelectorAll('[data-confirm]').forEach(button => {
            button.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm-message') || 
                               'Apakah Anda yakin ingin melanjutkan?';
                
                if (!confirm(message)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });
    }
    
    /**
     * Initialize animations
     */
    function initAnimations() {
        // Animate on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Observe elements dengan class animate-on-scroll
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
        
        // Progress bar animation
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.transition = 'width 1s ease-in-out';
                bar.style.width = width;
            }, 100);
        });
    }
    
    /**
     * Handle alerts
     */
    function handleAlerts() {
        // Auto-hide success alerts setelah 5 detik
        const successAlerts = document.querySelectorAll('.alert-success');
        successAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Tambahkan tombol close ke semua alerts
        const alerts = document.querySelectorAll('.alert:not(.alert-dismissible)');
        alerts.forEach(alert => {
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close';
            closeBtn.setAttribute('data-bs-dismiss', 'alert');
            closeBtn.setAttribute('aria-label', 'Close');
            alert.classList.add('alert-dismissible', 'fade', 'show');
            alert.appendChild(closeBtn);
        });
    }
    
    /**
     * Handle forms
     */
    function handleForms() {
        // Form validation dengan Bootstrap
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
        
        // Real-time validation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        // Auto format input
        document.querySelectorAll('[data-format]').forEach(input => {
            const format = input.getAttribute('data-format');
            
            input.addEventListener('input', function() {
                switch (format) {
                    case 'uppercase':
                        this.value = this.value.toUpperCase();
                        break;
                    case 'capitalize':
                        this.value = this.value.toLowerCase().replace(/\b\w/g, function(l) {
                            return l.toUpperCase();
                        });
                        break;
                    case 'phone':
                        this.value = this.value.replace(/\D/g, '');
                        break;
                    case 'numeric':
                        this.value = this.value.replace(/\D/g, '');
                        break;
                }
            });
        });
    }
    
    /**
     * Validate individual field
     */
    function validateField(field) {
        const value = field.value.trim();
        const feedback = field.nextElementSibling;
        
        // Clear previous validation
        field.classList.remove('is-valid', 'is-invalid');
        if (feedback) {
            feedback.classList.remove('valid-feedback', 'invalid-feedback');
            feedback.textContent = '';
        }
        
        // Skip empty required fields (handled by browser)
        if (field.required && value === '') return;
        
        let isValid = true;
        let message = '';
        
        // Custom validations berdasarkan input type
        switch (field.type) {
            case 'email':
                if (value && !isValidEmail(value)) {
                    isValid = false;
                    message = 'Format email tidak valid';
                }
                break;
                
            case 'tel':
                if (value && !isValidPhone(value)) {
                    isValid = false;
                    message = 'Format nomor telepon tidak valid (10-15 digit)';
                }
                break;
                
            case 'url':
                if (value && !isValidUrl(value)) {
                    isValid = false;
                    message = 'Format URL tidak valid';
                }
                break;
        }
        
        // Custom validations berdasarkan attributes
        if (field.hasAttribute('minlength')) {
            const minLength = parseInt(field.getAttribute('minlength'));
            if (value.length < minLength) {
                isValid = false;
                message = `Minimal ${minLength} karakter`;
            }
        }
        
        if (field.hasAttribute('maxlength')) {
            const maxLength = parseInt(field.getAttribute('maxlength'));
            if (value.length > maxLength) {
                isValid = false;
                message = `Maksimal ${maxLength} karakter`;
            }
        }
        
        if (field.hasAttribute('pattern')) {
            const pattern = new RegExp(field.getAttribute('pattern'));
            if (value && !pattern.test(value)) {
                isValid = false;
                message = field.getAttribute('data-pattern-message') || 'Format tidak sesuai';
            }
        }
        
        // Apply validation result
        if (isValid && value) {
            field.classList.add('is-valid');
            if (feedback) {
                feedback.classList.add('valid-feedback');
                feedback.textContent = field.getAttribute('data-valid-message') || 'Valid';
            }
        } else if (!isValid) {
            field.classList.add('is-invalid');
            if (feedback) {
                feedback.classList.add('invalid-feedback');
                feedback.textContent = message;
            }
        }
    }
    
    /**
     * Helper function untuk validasi email
     */
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    /**
     * Helper function untuk validasi phone
     */
    function isValidPhone(phone) {
        const re = /^[0-9]{10,15}$/;
        return re.test(phone);
    }
    
    /**
     * Helper function untuk validasi URL
     */
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    /**
     * Helper function untuk format date
     */
    function formatDate(date, format = 'id-ID') {
        const d = new Date(date);
        return d.toLocaleDateString(format, {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }
    
    /**
     * Helper function untuk format time
     */
    function formatTime(date, format = 'id-ID') {
        const d = new Date(date);
        return d.toLocaleTimeString(format, {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    /**
     * Helper function untuk format datetime
     */
    function formatDateTime(date) {
        return `${formatDate(date)} ${formatTime(date)}`;
    }
    
    /**
     * Helper function untuk debounce
     */
    function debounce(func, wait) {
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
    
    /**
     * Helper function untuk throttle
     */
    function throttle(func, limit) {
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
    
    /**
     * AJAX helper function
     */
    function ajaxRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };
        
        const config = { ...defaultOptions, ...options };
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text();
            })
            .catch(error => {
                console.error('AJAX Request failed:', error);
                throw error;
            });
    }
    
    /**
     * Show loading spinner
     */
    function showLoading(element) {
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border spinner-border-sm';
        spinner.setAttribute('role', 'status');
        spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
        
        element.disabled = true;
        element.innerHTML = '';
        element.appendChild(spinner);
    }
    
    /**
     * Hide loading spinner
     */
    function hideLoading(element, originalText) {
        element.disabled = false;
        element.innerHTML = originalText;
    }
    
    /**
     * Show toast notification
     */
    function showToast(type, message, duration = 3000) {
        // Cek jika sudah ada toast container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }
        
        // Buat toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Initialize dan show toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: duration
        });
        
        bsToast.show();
        
        // Hapus toast setelah hidden
        toast.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
        
        return toastId;
    }
    
    /**
     * Copy text to clipboard
     */
    function copyToClipboard(text) {
        return navigator.clipboard.writeText(text)
            .then(() => {
                showToast('success', 'Teks berhasil disalin ke clipboard');
                return true;
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
                showToast('danger', 'Gagal menyalin teks');
                return false;
            });
    }
    
    /**
     * Export sebagai global functions
     */
    window.MATHLine = {
        utils: {
            formatDate,
            formatTime,
            formatDateTime,
            debounce,
            throttle,
            isValidEmail,
            isValidPhone,
            isValidUrl
        },
        ui: {
            showLoading,
            hideLoading,
            showToast,
            copyToClipboard
        },
        ajax: ajaxRequest
    };
    
})();

// Export untuk module system (jika menggunakan ES6 modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.MATHLine;
}