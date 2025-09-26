/**
 * Theme Management JavaScript
 */

class ThemeManager {
    constructor() {
        this.currentTheme = null;
        this.init();
    }

    init() {
        this.initThemeSelector();
        this.initThemePreview();
        this.initThemeEditor();
        this.initThemeImport();
        this.initThemeExport();
    }

    // Theme Selector
    initThemeSelector() {
        const themeSelectors = document.querySelectorAll('.theme-selector');
        
        themeSelectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                this.applyTheme(e.target.value);
            });
        });
    }

    applyTheme(themeId) {
        if (!themeId) return;
        
        this.fetchTheme(themeId)
            .then(theme => {
                this.currentTheme = theme;
                this.updateCSSVariables(theme);
                this.updatePreview(theme);
                this.saveThemePreference(themeId);
            })
            .catch(error => {
                console.error('Error applying theme:', error);
            });
    }

    async fetchTheme(themeId) {
        try {
            const response = await fetch(`/api/themes/${themeId}`);
            const data = await response.json();
            return data.data;
        } catch (error) {
            console.error('Error fetching theme:', error);
            throw error;
        }
    }

    updateCSSVariables(theme) {
        const root = document.documentElement;
        
        root.style.setProperty('--primary-color', theme.primary_color);
        root.style.setProperty('--secondary-color', theme.secondary_color);
        root.style.setProperty('--background-color', theme.background_color);
        root.style.setProperty('--text-color', theme.text_color);
        root.style.setProperty('--font-family', theme.font_family);
        root.style.setProperty('--font-size', theme.font_size + 'px');
        
        // Apply custom CSS if available
        if (theme.custom_css && theme.custom_css.css) {
            this.applyCustomCSS(theme.custom_css.css);
        }
    }

    applyCustomCSS(css) {
        let customStyle = document.getElementById('custom-theme-css');
        if (!customStyle) {
            customStyle = document.createElement('style');
            customStyle.id = 'custom-theme-css';
            document.head.appendChild(customStyle);
        }
        customStyle.textContent = css;
    }

    updatePreview(theme) {
        const preview = document.getElementById('theme-preview');
        if (!preview) return;
        
        preview.style.cssText = `
            background-color: ${theme.background_color};
            color: ${theme.text_color};
            font-family: ${theme.font_family};
            font-size: ${theme.font_size}px;
        `;
        
        // Update preview elements
        const header = preview.querySelector('.preview-header');
        const actions = preview.querySelector('.preview-actions');
        const button = preview.querySelector('.btn');
        
        if (header) {
            header.style.borderBottomColor = theme.primary_color;
        }
        
        if (actions) {
            actions.style.borderTopColor = theme.secondary_color;
        }
        
        if (button) {
            button.style.backgroundColor = theme.primary_color;
            button.style.borderColor = theme.primary_color;
        }
    }

    saveThemePreference(themeId) {
        localStorage.setItem('selectedTheme', themeId);
    }

    loadThemePreference() {
        const savedTheme = localStorage.getItem('selectedTheme');
        if (savedTheme) {
            this.applyTheme(savedTheme);
        }
    }

    // Theme Preview
    initThemePreview() {
        this.initColorPickers();
        this.initFontSelectors();
        this.initCustomCSSEditor();
    }

    initColorPickers() {
        const colorInputs = document.querySelectorAll('input[type="color"]');
        
        colorInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.updateLivePreview();
            });
        });
    }

    initFontSelectors() {
        const fontSelects = document.querySelectorAll('select[name="font_family"], input[name="font_size"]');
        
        fontSelects.forEach(select => {
            select.addEventListener('change', (e) => {
                this.updateLivePreview();
            });
        });
    }

    initCustomCSSEditor() {
        const cssEditor = document.getElementById('custom_css');
        if (!cssEditor) return;
        
        cssEditor.addEventListener('input', (e) => {
            this.updateLivePreview();
        });
    }

    updateLivePreview() {
        const form = document.querySelector('form');
        if (!form) return;
        
        const formData = new FormData(form);
        const theme = {
            primary_color: formData.get('primary_color') || '#007bff',
            secondary_color: formData.get('secondary_color') || '#6c757d',
            background_color: formData.get('background_color') || '#ffffff',
            text_color: formData.get('text_color') || '#333333',
            font_family: formData.get('font_family') || 'Arial, sans-serif',
            font_size: formData.get('font_size') || 16,
            custom_css: { css: formData.get('custom_css') || '' }
        };
        
        this.updatePreview(theme);
    }

    // Theme Editor
    initThemeEditor() {
        this.initThemeValidation();
        this.initThemeDuplication();
        this.initThemeDeletion();
    }

    initThemeValidation() {
        const forms = document.querySelectorAll('form[action*="themes"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateTheme(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    validateTheme(form) {
        const name = form.querySelector('input[name="name"]');
        const displayName = form.querySelector('input[name="display_name"]');
        
        if (!name || !displayName) return true;
        
        // Validate name format
        const nameRegex = /^[a-z0-9_]+$/;
        if (!nameRegex.test(name.value)) {
            this.showError('Theme name must contain only lowercase letters, numbers, and underscores.');
            return false;
        }
        
        // Validate display name
        if (displayName.value.trim().length < 2) {
            this.showError('Display name must be at least 2 characters long.');
            return false;
        }
        
        return true;
    }

    initThemeDuplication() {
        const duplicateBtns = document.querySelectorAll('.btn-duplicate-theme');
        
        duplicateBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const themeId = btn.dataset.themeId;
                this.duplicateTheme(themeId);
            });
        });
    }

    async duplicateTheme(themeId) {
        try {
            const response = await fetch(`/themes/${themeId}/duplicate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                this.showSuccess('Theme duplicated successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to duplicate theme');
            }
        } catch (error) {
            this.showError('Error duplicating theme: ' + error.message);
        }
    }

    initThemeDeletion() {
        const deleteBtns = document.querySelectorAll('.btn-delete-theme');
        
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const themeId = btn.dataset.themeId;
                this.deleteTheme(themeId);
            });
        });
    }

    async deleteTheme(themeId) {
        if (!confirm('Are you sure you want to delete this theme?')) {
            return;
        }
        
        try {
            const response = await fetch(`/themes/${themeId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                this.showSuccess('Theme deleted successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to delete theme');
            }
        } catch (error) {
            this.showError('Error deleting theme: ' + error.message);
        }
    }

    // Theme Import/Export
    initThemeImport() {
        const importForm = document.getElementById('importThemeForm');
        if (!importForm) return;
        
        importForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.importTheme(importForm);
        });
    }

    importTheme(form) {
        const fileInput = form.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        
        if (!file) {
            this.showError('Please select a theme file to import.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const themeData = JSON.parse(e.target.result);
                this.validateImportedTheme(themeData);
                this.submitImportedTheme(themeData);
            } catch (error) {
                this.showError('Invalid theme file format.');
            }
        };
        reader.readAsText(file);
    }

    validateImportedTheme(themeData) {
        const requiredFields = ['name', 'display_name', 'primary_color', 'secondary_color', 'background_color', 'text_color', 'font_family', 'font_size'];
        
        requiredFields.forEach(field => {
            if (!themeData[field]) {
                throw new Error(`Missing required field: ${field}`);
            }
        });
    }

    async submitImportedTheme(themeData) {
        try {
            const response = await fetch('/themes/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(themeData)
            });
            
            if (response.ok) {
                this.showSuccess('Theme imported successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error('Failed to import theme');
            }
        } catch (error) {
            this.showError('Error importing theme: ' + error.message);
        }
    }

    initThemeExport() {
        const exportBtns = document.querySelectorAll('.btn-export-theme');
        
        exportBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const themeId = btn.dataset.themeId;
                this.exportTheme(themeId);
            });
        });
    }

    async exportTheme(themeId) {
        try {
            const response = await fetch(`/themes/${themeId}/export`);
            const themeData = await response.json();
            
            const blob = new Blob([JSON.stringify(themeData, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `theme_${themeData.name}.json`;
            a.click();
            
            window.URL.revokeObjectURL(url);
            this.showSuccess('Theme exported successfully!');
        } catch (error) {
            this.showError('Error exporting theme: ' + error.message);
        }
    }

    // Utility Methods
    showError(message) {
        this.showNotification(message, 'danger');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
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
    window.themeManager = new ThemeManager();
});

// Export for use in other scripts
window.ThemeManager = ThemeManager;
