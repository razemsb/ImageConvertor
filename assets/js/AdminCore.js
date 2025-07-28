class AdminConverter {

    static config = {
        scrollThreshold: 200,
        chartColors: {
            background: [
                'rgba(59, 130, 246, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(139, 92, 246, 0.7)'
            ],
            border: [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(139, 92, 246, 1)'
            ]
        }
    };


    static init() {
        this.initScrollToTop();
        this.initPagination();
        this.initPerPageSelector();
        this.initErrorDetails();
        this.initChart();
    }
    


    static initScrollToTop() {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        if (!scrollToTopBtn) return;

        window.addEventListener('scroll', () => {
            scrollToTopBtn.classList.toggle('visible', window.scrollY > this.config.scrollThreshold);
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }


    static initPagination() {
        this.setupPaginationButton('prevPage', (page) => page - 1);
        this.setupPaginationButton('nextPage', (page, totalPages) => Math.min(page + 1, totalPages));
    }

    static setupPaginationButton(buttonId, pageCallback) {
        const button = document.getElementById(buttonId);
        if (!button) return;

        button.addEventListener('click', () => {
            if (button.disabled) return;

            const currentPage = parseInt(button.dataset.currentPage) || 1;
            const totalPages = parseInt(button.dataset.totalPages) || 1;
            const newPage = pageCallback(currentPage, totalPages);

            this.updateUrlParams({ page: newPage });
        });
    }


    static initPerPageSelector() {
        const perPageSelect = document.getElementById('perPageSelect');
        if (!perPageSelect) return;

        perPageSelect.addEventListener('change', () => {
            this.updateUrlParams({
                perPage: perPageSelect.value,
                page: 1
            });
        });
    }


    static initErrorDetails() {
        document.addEventListener('click', (e) => {

            if (e.target.closest('[data-error-details]')) {
                const btn = e.target.closest('[data-error-details]');
                const detailsId = btn.getAttribute('data-error-details');
                this.toggleErrorDetails(detailsId, btn);
                return;
            }


            if (!e.target.closest('.error-details')) {
                this.closeAllErrorDetails();
            }
        });
    }

    static toggleErrorDetails(detailsId, triggerElement) {
        const details = document.getElementById(detailsId);
        if (!details) return;


        this.closeAllErrorDetails();


        const isVisible = details.style.display === 'block';
        details.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
            const rect = triggerElement.getBoundingClientRect();
            details.style.top = `${rect.bottom + window.scrollY}px`;
            details.style.left = `${rect.left + window.scrollX}px`;
        }
    }

    static closeAllErrorDetails() {
        document.querySelectorAll('.error-details').forEach(el => {
            el.style.display = 'none';
        });
    }


    static initChart() {
        const canvas = document.getElementById('formatChart');
        if (!canvas) {
            console.warn('Chart canvas not found');
            return;
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Failed to get canvas context');
            return;
        }


        const chartData = {
            labels: JSON.parse(canvas.dataset.labels || '[]'),
            counts: JSON.parse(canvas.dataset.counts || '[]')
        };

        if (!chartData.labels.length || !chartData.counts.length) {
            this.showNoDataMessage(canvas);
            return;
        }

        this.renderChart(ctx, chartData);
    }

    static showNoDataMessage(canvas) {
        canvas.style.display = 'none';
        const noDataMsg = document.createElement('p');
        noDataMsg.textContent = 'Нет данных о популярных форматах';
        noDataMsg.className = 'text-gray-400 text-center py-10';
        canvas.parentNode.appendChild(noDataMsg);
    }

    static renderChart(ctx, { labels, counts }) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: this.config.chartColors.background,
                    borderColor: this.config.chartColors.border,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#d1d5db',
                            font: {
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    static updateUrlParams(params) {
        const url = new URL(window.location.href);

        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        window.location.href = url.toString();
    }
    setupErrorHandlers() {
        document.addEventListener('click', (e) => {

            if (e.target.closest('.error-details-btn')) {
                const btn = e.target.closest('.error-details-btn');
                const errorId = btn.dataset.errorId;
                this.toggleError(errorId, btn);
                return;
            }


            if (!e.target.closest('.error-details')) {
                this.hideCurrentError();
            }
        });
    }

    toggleError(errorId, triggerBtn) {
        const errorElement = document.getElementById(`error-${errorId}`);

        if (this.currentError === errorElement) {
            this.hideCurrentError();
            return;
        }

        this.hideCurrentError();


        errorElement.classList.remove('hidden');
        this.positionError(errorElement, triggerBtn);
        this.currentError = errorElement;
    }

    positionError(element, trigger) {
        const rect = trigger.getBoundingClientRect();
        element.style.top = `${rect.bottom + window.scrollY + 5}px`;
        element.style.left = `${rect.left + window.scrollX}px`;
    }

    hideCurrentError() {
        if (this.currentError) {
            this.currentError.classList.add('hidden');
            this.currentError = null;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => AdminConverter.init());