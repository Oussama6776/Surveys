/**
 * Survey Tool Advanced Features JavaScript
 */

class SurveyAdvanced {
    constructor() {
        this.init();
    }

    init() {
        this.initDragAndDrop();
        this.initConditionalLogic();
        this.initRatingScales();
        this.initFileUpload();
        this.initProgressBar();
        this.initMultiPage();
        this.initAnalytics();
    }

    // Drag and Drop Functionality
    initDragAndDrop() {
        const containers = document.querySelectorAll('.drag-drop-container');
        
        containers.forEach(container => {
            container.addEventListener('dragover', (e) => {
                e.preventDefault();
                container.classList.add('dragover');
            });

            container.addEventListener('dragleave', (e) => {
                e.preventDefault();
                container.classList.remove('dragover');
            });

            container.addEventListener('drop', (e) => {
                e.preventDefault();
                container.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                this.handleFileDrop(files, container);
            });
        });
    }

    handleFileDrop(files, container) {
        const fileInput = container.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    }

    // Conditional Logic
    initConditionalLogic() {
        const conditionalQuestions = document.querySelectorAll('.conditional-question');
        
        conditionalQuestions.forEach(question => {
            const conditions = JSON.parse(question.dataset.conditions || '[]');
            this.evaluateConditions(question, conditions);
        });

        // Listen for changes in dependent questions
        document.addEventListener('change', (e) => {
            if (e.target.matches('input, select, textarea')) {
                this.updateConditionalQuestions();
            }
        });
    }

    updateConditionalQuestions() {
        const conditionalQuestions = document.querySelectorAll('.conditional-question');
        
        conditionalQuestions.forEach(question => {
            const conditions = JSON.parse(question.dataset.conditions || '[]');
            this.evaluateConditions(question, conditions);
        });
    }

    evaluateConditions(question, conditions) {
        let shouldShow = true;

        conditions.forEach(condition => {
            const dependentQuestion = document.querySelector(`[name="${condition.depends_on}"]`);
            if (!dependentQuestion) return;

            const value = this.getQuestionValue(dependentQuestion);
            const conditionMet = this.evaluateCondition(value, condition);

            if (conditionMet && condition.action === 'hide') {
                shouldShow = false;
            } else if (!conditionMet && condition.action === 'show') {
                shouldShow = false;
            }
        });

        if (shouldShow) {
            question.classList.add('show');
            question.classList.remove('conditional-question');
        } else {
            question.classList.remove('show');
            question.classList.add('conditional-question');
        }
    }

    getQuestionValue(element) {
        if (element.type === 'checkbox') {
            return element.checked;
        } else if (element.type === 'radio') {
            return element.checked ? element.value : null;
        } else if (element.tagName === 'SELECT') {
            return element.value;
        } else {
            return element.value;
        }
    }

    evaluateCondition(value, condition) {
        switch (condition.type) {
            case 'equals':
                return value == condition.value;
            case 'not_equals':
                return value != condition.value;
            case 'contains':
                return String(value).includes(condition.value);
            case 'greater_than':
                return Number(value) > Number(condition.value);
            case 'less_than':
                return Number(value) < Number(condition.value);
            default:
                return false;
        }
    }

    // Rating Scales
    initRatingScales() {
        const ratingScales = document.querySelectorAll('.rating-scale');
        
        ratingScales.forEach(scale => {
            const options = scale.querySelectorAll('.rating-option');
            
            options.forEach(option => {
                option.addEventListener('click', () => {
                    // Remove previous selection
                    options.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selection to clicked option
                    option.classList.add('selected');
                    
                    // Update hidden input
                    const input = scale.querySelector('input[type="hidden"]');
                    if (input) {
                        input.value = option.dataset.value;
                    }
                });
            });
        });

        // Star Rating
        const starRatings = document.querySelectorAll('.star-rating');
        
        starRatings.forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    this.setStarRating(rating, index + 1);
                });

                star.addEventListener('mouseenter', () => {
                    this.highlightStars(rating, index + 1);
                });
            });

            rating.addEventListener('mouseleave', () => {
                const currentRating = rating.dataset.rating || 0;
                this.highlightStars(rating, currentRating);
            });
        });
    }

    setStarRating(rating, value) {
        rating.dataset.rating = value;
        this.highlightStars(rating, value);
        
        const input = rating.querySelector('input[type="hidden"]');
        if (input) {
            input.value = value;
        }
    }

    highlightStars(rating, value) {
        const stars = rating.querySelectorAll('.star');
        
        stars.forEach((star, index) => {
            star.classList.remove('active');
            if (index < value) {
                star.classList.add('active');
            }
        });
    }

    // File Upload
    initFileUpload() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e.target.files, input);
            });
        });
    }

    handleFileUpload(files, input) {
        const container = input.closest('.file-upload-area');
        if (!container) return;

        const preview = container.querySelector('.file-preview');
        if (!preview) return;

        preview.innerHTML = '';

        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex align-items-center p-2 border rounded mb-2';
            
            const icon = this.getFileIcon(file.type);
            const size = this.formatFileSize(file.size);
            
            fileItem.innerHTML = `
                <i class="${icon} fa-2x text-primary mr-3"></i>
                <div class="flex-grow-1">
                    <div class="font-weight-bold">${file.name}</div>
                    <small class="text-muted">${size}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            preview.appendChild(fileItem);
        });
    }

    getFileIcon(type) {
        if (type.startsWith('image/')) return 'fas fa-image';
        if (type.startsWith('video/')) return 'fas fa-video';
        if (type.startsWith('audio/')) return 'fas fa-music';
        if (type.includes('pdf')) return 'fas fa-file-pdf';
        if (type.includes('word')) return 'fas fa-file-word';
        if (type.includes('excel') || type.includes('spreadsheet')) return 'fas fa-file-excel';
        return 'fas fa-file';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Progress Bar
    initProgressBar() {
        const progressBars = document.querySelectorAll('.survey-progress-bar');
        
        progressBars.forEach(bar => {
            const targetWidth = bar.dataset.width || 0;
            this.animateProgressBar(bar, targetWidth);
        });
    }

    animateProgressBar(bar, targetWidth) {
        let currentWidth = 0;
        const increment = targetWidth / 100;
        
        const timer = setInterval(() => {
            currentWidth += increment;
            bar.style.width = currentWidth + '%';
            
            if (currentWidth >= targetWidth) {
                clearInterval(timer);
                bar.style.width = targetWidth + '%';
            }
        }, 20);
    }

    // Multi-page Navigation
    initMultiPage() {
        const nextBtn = document.querySelector('.btn-next-page');
        const prevBtn = document.querySelector('.btn-prev-page');
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevPage());
        }
    }

    nextPage() {
        const currentPage = document.querySelector('.survey-page.active');
        const nextPage = currentPage?.nextElementSibling;
        
        if (nextPage && nextPage.classList.contains('survey-page')) {
            currentPage.classList.remove('active');
            nextPage.classList.add('active');
            this.updatePageNavigation();
        }
    }

    prevPage() {
        const currentPage = document.querySelector('.survey-page.active');
        const prevPage = currentPage?.previousElementSibling;
        
        if (prevPage && prevPage.classList.contains('survey-page')) {
            currentPage.classList.remove('active');
            prevPage.classList.add('active');
            this.updatePageNavigation();
        }
    }

    updatePageNavigation() {
        const pages = document.querySelectorAll('.survey-page');
        const currentPage = document.querySelector('.survey-page.active');
        const currentIndex = Array.from(pages).indexOf(currentPage);
        
        const prevBtn = document.querySelector('.btn-prev-page');
        const nextBtn = document.querySelector('.btn-next-page');
        const pageInfo = document.querySelector('.page-info');
        
        if (prevBtn) {
            prevBtn.style.display = currentIndex > 0 ? 'block' : 'none';
        }
        
        if (nextBtn) {
            nextBtn.style.display = currentIndex < pages.length - 1 ? 'block' : 'none';
        }
        
        if (pageInfo) {
            pageInfo.textContent = `Page ${currentIndex + 1} of ${pages.length}`;
        }
    }

    // Analytics
    initAnalytics() {
        this.initCharts();
        this.initWordCloud();
        this.initExportFunctions();
    }

    initCharts() {
        const chartContainers = document.querySelectorAll('.chart-container');
        
        chartContainers.forEach(container => {
            const chartType = container.dataset.chartType;
            const chartData = JSON.parse(container.dataset.chartData || '{}');
            
            this.createChart(container, chartType, chartData);
        });
    }

    createChart(container, type, data) {
        // This would integrate with Chart.js or similar library
        // For now, we'll create a simple placeholder
        container.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <i class="fas fa-chart-${type} fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chart: ${type}</p>
                </div>
            </div>
        `;
    }

    initWordCloud() {
        const wordClouds = document.querySelectorAll('.word-cloud');
        
        wordClouds.forEach(cloud => {
            const words = JSON.parse(cloud.dataset.words || '{}');
            this.createWordCloud(cloud, words);
        });
    }

    createWordCloud(container, words) {
        const wordList = Object.entries(words)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 20);
        
        container.innerHTML = wordList.map(([word, count]) => {
            const size = Math.max(12, Math.min(48, count * 2));
            return `<span class="word-cloud-item" style="font-size: ${size}px; margin: 0.25rem;">${word}</span>`;
        }).join('');
    }

    initExportFunctions() {
        const exportBtns = document.querySelectorAll('.btn-export');
        
        exportBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const format = btn.dataset.format;
                this.exportData(format);
            });
        });
    }

    exportData(format) {
        // This would handle data export
        console.log(`Exporting data as ${format}`);
    }

    // Utility Methods
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

    showLoading(element) {
        element.classList.add('loading');
    }

    hideLoading(element) {
        element.classList.remove('loading');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.surveyAdvanced = new SurveyAdvanced();
});

// Export for use in other scripts
window.SurveyAdvanced = SurveyAdvanced;
