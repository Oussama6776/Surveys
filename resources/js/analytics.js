/**
 * Analytics Dashboard JavaScript
 */

class AnalyticsDashboard {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        this.initCharts();
        this.initFilters();
        this.initExport();
        this.initRealTimeUpdates();
    }

    // Chart.js Integration
    initCharts() {
        this.initResponseTrendsChart();
        this.initQuestionAnalyticsChart();
        this.initCompletionRatesChart();
        this.initDemographicsChart();
    }

    initResponseTrendsChart() {
        const ctx = document.getElementById('responseTrendsChart');
        if (!ctx) return;

        const data = JSON.parse(ctx.dataset.chartData || '{}');
        
        this.charts.responseTrends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Responses',
                    data: data.values || [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    initQuestionAnalyticsChart() {
        const ctx = document.getElementById('questionAnalyticsChart');
        if (!ctx) return;

        const data = JSON.parse(ctx.dataset.chartData || '{}');
        
        this.charts.questionAnalytics = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: data.values || [],
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    initCompletionRatesChart() {
        const ctx = document.getElementById('completionRatesChart');
        if (!ctx) return;

        const data = JSON.parse(ctx.dataset.chartData || '{}');
        
        this.charts.completionRates = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Incomplete'],
                datasets: [{
                    data: data.values || [0, 0],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
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

    initDemographicsChart() {
        const ctx = document.getElementById('demographicsChart');
        if (!ctx) return;

        const data = JSON.parse(ctx.dataset.chartData || '{}');
        
        this.charts.demographics = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#C9CBCF', '#4BC0C0', '#FF6384', '#C9CBCF'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    // Filters
    initFilters() {
        this.initDateRangeFilter();
        this.initQuestionFilter();
        this.initResponseFilter();
    }

    initDateRangeFilter() {
        const dateRangeInput = document.getElementById('dateRange');
        if (!dateRangeInput) return;

        dateRangeInput.addEventListener('change', (e) => {
            this.updateChartsWithDateRange(e.target.value);
        });
    }

    initQuestionFilter() {
        const questionFilter = document.getElementById('questionFilter');
        if (!questionFilter) return;

        questionFilter.addEventListener('change', (e) => {
            this.filterByQuestion(e.target.value);
        });
    }

    initResponseFilter() {
        const responseFilter = document.getElementById('responseFilter');
        if (!responseFilter) return;

        responseFilter.addEventListener('change', (e) => {
            this.filterByResponseType(e.target.value);
        });
    }

    updateChartsWithDateRange(dateRange) {
        // Fetch new data based on date range
        this.fetchAnalyticsData({ dateRange })
            .then(data => {
                this.updateCharts(data);
            })
            .catch(error => {
                console.error('Error updating charts:', error);
            });
    }

    filterByQuestion(questionId) {
        // Filter analytics by specific question
        this.fetchAnalyticsData({ questionId })
            .then(data => {
                this.updateCharts(data);
            })
            .catch(error => {
                console.error('Error filtering by question:', error);
            });
    }

    filterByResponseType(responseType) {
        // Filter analytics by response type
        this.fetchAnalyticsData({ responseType })
            .then(data => {
                this.updateCharts(data);
            })
            .catch(error => {
                console.error('Error filtering by response type:', error);
            });
    }

    // Export Functions
    initExport() {
        this.initPDFExport();
        this.initExcelExport();
        this.initCSVExport();
    }

    initPDFExport() {
        const pdfBtn = document.getElementById('exportPDF');
        if (!pdfBtn) return;

        pdfBtn.addEventListener('click', () => {
            this.exportToPDF();
        });
    }

    initExcelExport() {
        const excelBtn = document.getElementById('exportExcel');
        if (!excelBtn) return;

        excelBtn.addEventListener('click', () => {
            this.exportToExcel();
        });
    }

    initCSVExport() {
        const csvBtn = document.getElementById('exportCSV');
        if (!csvBtn) return;

        csvBtn.addEventListener('click', () => {
            this.exportToCSV();
        });
    }

    exportToPDF() {
        this.showLoading();
        
        // Generate PDF using jsPDF or similar library
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Add title
        doc.setFontSize(20);
        doc.text('Survey Analytics Report', 20, 20);
        
        // Add charts as images
        Object.values(this.charts).forEach((chart, index) => {
            const canvas = chart.canvas;
            const imgData = canvas.toDataURL('image/png');
            doc.addImage(imgData, 'PNG', 20, 40 + (index * 80), 160, 60);
        });
        
        // Save the PDF
        doc.save('survey-analytics.pdf');
        
        this.hideLoading();
    }

    exportToExcel() {
        this.showLoading();
        
        // Generate Excel file using SheetJS
        const wb = XLSX.utils.book_new();
        
        // Add overview sheet
        const overviewData = this.getOverviewData();
        const overviewWS = XLSX.utils.json_to_sheet(overviewData);
        XLSX.utils.book_append_sheet(wb, overviewWS, 'Overview');
        
        // Add question analytics sheet
        const questionData = this.getQuestionAnalyticsData();
        const questionWS = XLSX.utils.json_to_sheet(questionData);
        XLSX.utils.book_append_sheet(wb, questionWS, 'Question Analytics');
        
        // Save the Excel file
        XLSX.writeFile(wb, 'survey-analytics.xlsx');
        
        this.hideLoading();
    }

    exportToCSV() {
        this.showLoading();
        
        const csvData = this.getCSVData();
        const csvContent = this.convertToCSV(csvData);
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'survey-analytics.csv';
        a.click();
        
        window.URL.revokeObjectURL(url);
        this.hideLoading();
    }

    // Real-time Updates
    initRealTimeUpdates() {
        if (window.Echo) {
            // Listen for real-time updates using Laravel Echo
            Echo.channel('survey-analytics')
                .listen('ResponseSubmitted', (e) => {
                    this.updateRealTimeData(e.data);
                });
        }
    }

    updateRealTimeData(data) {
        // Update charts with new data
        this.updateCharts(data);
        
        // Show notification
        this.showNotification('New response received!', 'success');
    }

    // Data Fetching
    async fetchAnalyticsData(filters = {}) {
        const surveyId = document.getElementById('analyticsDashboard').dataset.surveyId;
        const url = new URL(`/api/v1/surveys/${surveyId}/analytics/overview`, window.location.origin);
        
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                url.searchParams.append(key, filters[key]);
            }
        });
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            return data.data;
        } catch (error) {
            console.error('Error fetching analytics data:', error);
            throw error;
        }
    }

    // Chart Updates
    updateCharts(data) {
        if (data.responseTrends && this.charts.responseTrends) {
            this.charts.responseTrends.data.labels = data.responseTrends.labels;
            this.charts.responseTrends.data.datasets[0].data = data.responseTrends.values;
            this.charts.responseTrends.update();
        }
        
        if (data.questionAnalytics && this.charts.questionAnalytics) {
            this.charts.questionAnalytics.data.labels = data.questionAnalytics.labels;
            this.charts.questionAnalytics.data.datasets[0].data = data.questionAnalytics.values;
            this.charts.questionAnalytics.update();
        }
        
        if (data.completionRates && this.charts.completionRates) {
            this.charts.completionRates.data.datasets[0].data = data.completionRates.values;
            this.charts.completionRates.update();
        }
        
        if (data.demographics && this.charts.demographics) {
            this.charts.demographics.data.labels = data.demographics.labels;
            this.charts.demographics.data.datasets[0].data = data.demographics.values;
            this.charts.demographics.update();
        }
    }

    // Data Helpers
    getOverviewData() {
        return [
            { Metric: 'Total Responses', Value: document.querySelector('[data-metric="total_responses"]')?.textContent || 0 },
            { Metric: 'Completion Rate', Value: document.querySelector('[data-metric="completion_rate"]')?.textContent || '0%' },
            { Metric: 'Average Completion Time', Value: document.querySelector('[data-metric="avg_completion_time"]')?.textContent || '0 minutes' }
        ];
    }

    getQuestionAnalyticsData() {
        const rows = document.querySelectorAll('#questionAnalyticsTable tbody tr');
        return Array.from(rows).map(row => ({
            Question: row.cells[0].textContent,
            Type: row.cells[1].textContent,
            'Response Count': row.cells[2].textContent,
            'Skip Rate': row.cells[3].textContent
        }));
    }

    getCSVData() {
        const data = [];
        
        // Add overview data
        data.push(['Survey Analytics Overview']);
        data.push(['Metric', 'Value']);
        this.getOverviewData().forEach(row => {
            data.push([row.Metric, row.Value]);
        });
        
        data.push([]); // Empty row
        
        // Add question analytics
        data.push(['Question Analytics']);
        data.push(['Question', 'Type', 'Response Count', 'Skip Rate']);
        this.getQuestionAnalyticsData().forEach(row => {
            data.push([row.Question, row.Type, row['Response Count'], row['Skip Rate']]);
        });
        
        return data;
    }

    convertToCSV(data) {
        return data.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
    }

    // Utility Methods
    showLoading() {
        document.body.classList.add('loading');
    }

    hideLoading() {
        document.body.classList.remove('loading');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('analyticsDashboard')) {
        window.analyticsDashboard = new AnalyticsDashboard();
    }
});

// Export for use in other scripts
window.AnalyticsDashboard = AnalyticsDashboard;
