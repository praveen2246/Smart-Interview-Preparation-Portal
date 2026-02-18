/**
 * SIPP - Smart Interview Preparation Portal
 * Main JavaScript File
 */

// Show alert message
function showAlert(message, type = 'success') {
    const alertToast = document.getElementById('alertToast');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    
    if (!alertToast) return;
    
    alertTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1);
    alertMessage.textContent = message;
    
    // Remove previous classes
    alertToast.className = 'toast';
    alertToast.classList.add('show');
    
    // Add appropriate class based on type
    if (type === 'success') {
        alertToast.classList.add('border-success');
    } else if (type === 'danger') {
        alertToast.classList.add('border-danger');
    } else if (type === 'warning') {
        alertToast.classList.add('border-warning');
    } else if (type === 'info') {
        alertToast.classList.add('border-info');
    }
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertToast.classList.remove('show');
    }, 5000);
}

// Validate form fields
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Format time in seconds to HH:MM:SS
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}

// Start countdown timer
function startTimer(duration, onTick, onComplete) {
    let remaining = duration;
    
    const interval = setInterval(() => {
        if (remaining <= 0) {
            clearInterval(interval);
            onComplete();
        } else {
            remaining--;
            onTick(remaining);
        }
    }, 1000);
    
    return interval;
}

// Update timer display
function updateTimerDisplay(seconds, elementId) {
    const timerElement = document.getElementById(elementId);
    if (!timerElement) return;
    
    timerElement.textContent = formatTime(seconds);
    
    // Change color based on remaining time
    if (seconds <= 60) {
        timerElement.classList.remove('warning');
        timerElement.classList.add('danger');
    } else if (seconds <= 300) {
        timerElement.classList.remove('danger');
        timerElement.classList.add('warning');
    }
}

// Disable option buttons during test
function disableTestOptions(disabled) {
    const options = document.querySelectorAll('.option input[type="radio"]');
    options.forEach(option => {
        option.disabled = disabled;
    });
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Copy text to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Copied to clipboard!', 'success');
    }).catch(() => {
        showAlert('Failed to copy', 'danger');
    });
}

// Confirm action dialog
function confirmAction(message) {
    return confirm(message);
}

// Format number with commas
function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Calculate percentage
function calculatePercentage(value, total) {
    if (total === 0) return 0;
    return Math.round((value / total) * 100);
}

// Initialize Chart.js chart
function initializeChart(canvasId, type, labels, datasets, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    };
    
    const mergedOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: mergedOptions
    });
}

// Debounce function for search
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

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Initialize bootstrap tooltips and popovers on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeTooltips();
    
    // Add smooth transitions to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

// Export functions for use in other scripts
window.SIPP = {
    showAlert,
    validateForm,
    formatTime,
    startTimer,
    updateTimerDisplay,
    disableTestOptions,
    copyToClipboard,
    confirmAction,
    formatNumber,
    calculatePercentage,
    initializeChart,
    debounce,
    formatDate
};
