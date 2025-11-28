// Job Application Tracker CMS JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Status change confirmation
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(function(select) {
        let originalValue = select.value;
        select.addEventListener('change', function() {
            if (this.value === 'rejected' || this.value === 'withdrawn') {
                if (!confirm('Are you sure you want to change the status to "' + this.options[this.selectedIndex].text + '"?')) {
                    this.value = originalValue;
                    return;
                }
            }
            originalValue = this.value;
        });
    });

    // Search and filter functionality
    const searchInput = document.getElementById('searchApplications');
    const statusFilter = document.getElementById('statusFilter');
    const applicationCards = document.querySelectorAll('.application-card');

    function filterApplications() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : '';

        applicationCards.forEach(function(card) {
            const companyName = card.querySelector('.company-name')?.textContent.toLowerCase() || '';
            const jobTitle = card.querySelector('.job-title')?.textContent.toLowerCase() || '';
            const cardStatus = card.dataset.status || '';

            const matchesSearch = companyName.includes(searchTerm) || jobTitle.includes(searchTerm);
            const matchesStatus = !statusValue || cardStatus === statusValue;

            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Update results count
        const visibleCards = document.querySelectorAll('.application-card[style="display: block"], .application-card:not([style*="display: none"])');
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            resultsCount.textContent = visibleCards.length;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterApplications);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterApplications);
    }

    // Auto-save form data to localStorage
    const formInputs = document.querySelectorAll('form input, form textarea, form select');
    formInputs.forEach(function(input) {
        // Load saved data
        const savedValue = localStorage.getItem('form_' + input.name);
        if (savedValue && !input.value) {
            input.value = savedValue;
        }

        // Save data on change
        input.addEventListener('change', function() {
            localStorage.setItem('form_' + this.name, this.value);
        });
    });

    // Clear saved form data on successful submission
    const forms_with_autosave = document.querySelectorAll('form[data-autosave]');
    forms_with_autosave.forEach(function(form) {
        form.addEventListener('submit', function() {
            const inputs = this.querySelectorAll('input, textarea, select');
            inputs.forEach(function(input) {
                localStorage.removeItem('form_' + input.name);
            });
        });
    });

    // Follow-up date highlighting
    const followUpDates = document.querySelectorAll('.follow-up-date');
    followUpDates.forEach(function(element) {
        const date = new Date(element.textContent);
        const today = new Date();
        const diffTime = date - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) {
            element.classList.add('text-danger');
            element.title = 'Overdue';
        } else if (diffDays === 0) {
            element.classList.add('text-warning');
            element.title = 'Due today';
        } else if (diffDays <= 3) {
            element.classList.add('text-info');
            element.title = 'Due soon';
        }
    });

    // Quick status update
    const quickStatusButtons = document.querySelectorAll('.quick-status-btn');
    quickStatusButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const applicationId = this.dataset.applicationId;
            const newStatus = this.dataset.status;
            const currentRow = this.closest('tr');

            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            // Make AJAX request to update status
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'application_id=' + applicationId + '&status=' + newStatus
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status badge in the row
                    const statusBadge = currentRow.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = 'badge status-badge ' + data.badgeClass;
                        statusBadge.textContent = data.statusText;
                    }
                    
                    // Show success message
                    showToast('Status updated successfully!', 'success');
                } else {
                    showToast('Failed to update status: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Error updating status', 'error');
            })
            .finally(() => {
                // Restore button
                this.innerHTML = this.dataset.originalText || 'Update';
                this.disabled = false;
            });
        });
    });

    // Chart initialization (if Chart.js is loaded)
    if (typeof Chart !== 'undefined') {
        const statusChartCanvas = document.getElementById('statusChart');
        if (statusChartCanvas) {
            const ctx = statusChartCanvas.getContext('2d');
            const chartData = JSON.parse(statusChartCanvas.dataset.chartData || '{}');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        data: chartData.data || [],
                        backgroundColor: [
                            '#007bff',
                            '#ffc107',
                            '#17a2b8',
                            '#28a745',
                            '#dc3545',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
});

// Utility functions
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

