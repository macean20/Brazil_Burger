// public/js/app.js - VERSION COMPLÈTE
class BrasilBurgerApp {
    constructor() {
        this.init();
    }

    init() {
        this.initComponents();
        this.initEventListeners();
        this.initCharts();
        this.initNotifications();
        console.log('🍔 Brasil Burger Dashboard initialisé');
    }

    initComponents() {
        // Bootstrap components
        this.initTooltips();
        this.initPopovers();
        this.initModals();
        this.initToasts();
    }

    initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    initPopovers() {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }

    initModals() {
        // Initialisation des modales Bootstrap
        const modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(modalEl => {
            if (modalEl.id) {
                window[modalEl.id] = new bootstrap.Modal(modalEl);
            }
        });
    }

    initToasts() {
        // Auto-hide toasts after 5 seconds
        const toastElList = document.querySelectorAll('.toast');
        [...toastElList].map(toastEl => {
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            return toast;
        });
    }

    initEventListeners() {
        // Delete confirmation
        this.initDeleteConfirmations();
        
        // Form validations
        this.initFormValidations();
        
        // Image upload previews
        this.initImageUploads();
        
        // Menu price calculator
        this.initMenuPriceCalculator();
        
        // Search and filters
        this.initSearchFilters();
        
        // Toggle actions
        this.initToggleActions();
    }

    initDeleteConfirmations() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const message = btn.dataset.confirm || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
                const url = btn.href;
                
                if (confirm(message)) {
                    window.location.href = url;
                }
            });
        });
    }

    initFormValidations() {
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    initImageUploads() {
        document.querySelectorAll('.file-upload-area').forEach(area => {
            const input = area.nextElementSibling;
            if (input && input.type === 'file') {
                area.addEventListener('click', () => input.click());
                
                input.addEventListener('change', e => {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            this.showNotification('Le fichier est trop volumineux (max 2MB)', 'error');
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = e => {
                            // Update preview if exists
                            const previewId = input.dataset.preview;
                            if (previewId) {
                                const preview = document.getElementById(previewId);
                                if (preview) {
                                    preview.src = e.target.result;
                                    preview.classList.remove('d-none');
                                }
                            }
                            
                            // Update upload area
                            area.innerHTML = `
                                <i class="bi bi-check-circle text-success"></i>
                                <p class="mb-1 fw-semibold">${file.name}</p>
                                <p class="small text-muted">${this.formatFileSize(file.size)}</p>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                });
                
                // Drag and drop
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    area.addEventListener(eventName, e => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });
                
                ['dragenter', 'dragover'].forEach(eventName => {
                    area.addEventListener(eventName, () => {
                        area.classList.add('border-primary', 'bg-light');
                    });
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    area.addEventListener(eventName, () => {
                        area.classList.remove('border-primary', 'bg-light');
                    });
                });
                
                area.addEventListener('drop', e => {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
        });
    }

    initMenuPriceCalculator() {
        const burgerSelect = document.getElementById('menu_burger');
        const boissonSelect = document.getElementById('menu_boisson');
        const friteSelect = document.getElementById('menu_frite');
        const priceDisplay = document.getElementById('menu_prix_total');
        
        if (burgerSelect && boissonSelect && friteSelect && priceDisplay) {
            const calculatePrice = () => {
                const burgerPrice = parseFloat(burgerSelect.selectedOptions[0]?.dataset?.price || 0);
                const boissonPrice = parseFloat(boissonSelect.selectedOptions[0]?.dataset?.price || 0);
                const fritePrice = parseFloat(friteSelect.selectedOptions[0]?.dataset?.price || 0);
                
                // Apply 10% discount for menu
                const totalPrice = (burgerPrice + boissonPrice + fritePrice) * 0.9;
                
                // Update display
                priceDisplay.textContent = this.formatPrice(totalPrice);
                
                // Update hidden input if exists
                const priceInput = document.getElementById('menu_prixTotal');
                if (priceInput) {
                    priceInput.value = totalPrice.toFixed(2);
                }
            };
            
            [burgerSelect, boissonSelect, friteSelect].forEach(select => {
                select.addEventListener('change', calculatePrice);
            });
            
            // Initial calculation
            calculatePrice();
        }
    }

    initSearchFilters() {
        // Real-time search
        const searchInputs = document.querySelectorAll('[data-search]');
        searchInputs.forEach(input => {
            input.addEventListener('input', e => {
                const searchTerm = e.target.value.toLowerCase();
                const target = input.dataset.search;
                const items = document.querySelectorAll(target);
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });
        
        // Filter forms
        const filterForms = document.querySelectorAll('.filter-form');
        filterForms.forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                this.submitFilterForm(form);
            });
        });
    }

    initToggleActions() {
        // Toggle disponibilité
        document.querySelectorAll('.toggle-disponibilite').forEach(btn => {
            btn.addEventListener('click', async e => {
                e.preventDefault();
                
                const url = btn.dataset.url;
                const csrfToken = btn.dataset.csrf;
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-Token': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showNotification(data.message, 'success');
                        // Update button state
                        const icon = btn.querySelector('i');
                        if (icon) {
                            icon.className = data.disponible ? 'bi bi-eye-slash' : 'bi bi-eye';
                        }
                        btn.classList.toggle('btn-warning');
                        btn.classList.toggle('btn-success');
                    } else {
                        this.showNotification(data.error || 'Erreur', 'error');
                    }
                } catch (error) {
                    this.showNotification('Erreur de connexion', 'error');
                }
            });
        });
    }

    initCharts() {
        // Orders chart
        const ordersChart = document.getElementById('ordersChart');
        if (ordersChart) {
            this.initOrdersChart();
        }
        
        // Revenue chart
        const revenueChart = document.getElementById('revenueChart');
        if (revenueChart) {
            this.initRevenueChart();
        }
    }

    initOrdersChart() {
        const ctx = document.getElementById('ordersChart').getContext('2d');
        
        // Get data from data attributes or API
        const labels = JSON.parse(document.getElementById('ordersChart').dataset.labels || '[]');
        const data = JSON.parse(document.getElementById('ordersChart').dataset.data || '[]');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Commandes',
                    data: data,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Recettes (FCFA)',
                    data: [120000, 150000, 180000, 140000, 200000, 250000, 180000],
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: '#3b82f6',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => this.formatPrice(value)
                        }
                    }
                }
            }
        });
    }

    initNotifications() {
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert:not(.toast)');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    }

    // Utility methods
    showNotification(message, type = 'success') {
        const container = document.getElementById('toastContainer') || document.body;
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${this.getNotificationIcon(type)} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    formatPrice(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'XOF',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    async submitFilterForm(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        window.location.search = params.toString();
    }

    // Order status management
    async changeOrderStatus(orderId, newStatus) {
        const confirmed = confirm('Changer le statut de cette commande ?');
        if (!confirmed) return;
        
        try {
            const response = await fetch(`/gestionnaire/commande/${orderId}/changer-statut/${newStatus}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                // Update UI
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
        }
    }

    // Livreur affectation
    async affecterLivreur(commandeId) {
        const livreurSelect = document.getElementById(`livreur-select-${commandeId}`);
        if (!livreurSelect || !livreurSelect.value) {
            this.showNotification('Veuillez sélectionner un livreur', 'warning');
            return;
        }
        
        try {
            const response = await fetch(`/gestionnaire/commande/${commandeId}/affecter-livreur`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    livreur_id: livreurSelect.value
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur de connexion', 'error');
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new BrasilBurgerApp();
    
    // Global functions for HTML onclick attributes
    window.confirmDelete = function(message, url) {
        if (confirm(message || 'Êtes-vous sûr de vouloir supprimer ?')) {
            window.location.href = url;
        }
    };
    
    window.changeOrderStatus = function(orderId, newStatus) {
        window.app.changeOrderStatus(orderId, newStatus);
    };
    
    window.affecterLivreur = function(commandeId) {
        window.app.affecterLivreur(commandeId);
    };
    
    // Auto-refresh dashboard stats every 30 seconds
    if (document.getElementById('dashboardPage')) {
        setInterval(() => {
            fetch('/gestionnaire/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    // Update stats cards if they exist
                    const elements = {
                        'stat-pending': data.en_cours,
                        'stat-completed': data.validees,
                        'stat-revenue': window.app.formatPrice(data.recettes),
                        'stat-cancelled': data.annulees
                    };
                    
                    Object.entries(elements).forEach(([id, value]) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = value;
                    });
                })
                .catch(console.error);
        }, 30000);
    }
});