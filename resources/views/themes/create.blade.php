@extends('layouts.app')

@section('title', 'Create Theme')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Theme</h1>
                <a href="{{ route('themes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Themes
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Theme Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('themes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Theme Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">Display Name</label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_color">Primary Color</label>
                                    <input type="color" class="form-control @error('primary_color') is-invalid @enderror" 
                                           id="primary_color" name="primary_color" value="{{ old('primary_color', '#007bff') }}" required>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="secondary_color">Secondary Color</label>
                                    <input type="color" class="form-control @error('secondary_color') is-invalid @enderror" 
                                           id="secondary_color" name="secondary_color" value="{{ old('secondary_color', '#6c757d') }}" required>
                                    @error('secondary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="background_color">Background Color</label>
                                    <input type="color" class="form-control @error('background_color') is-invalid @enderror" 
                                           id="background_color" name="background_color" value="{{ old('background_color', '#ffffff') }}" required>
                                    @error('background_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_color">Text Color</label>
                                    <input type="color" class="form-control @error('text_color') is-invalid @enderror" 
                                           id="text_color" name="text_color" value="{{ old('text_color', '#333333') }}" required>
                                    @error('text_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="font_family">Font Family</label>
                                    <select class="form-control @error('font_family') is-invalid @enderror" 
                                            id="font_family" name="font_family" required>
                                        <option value="Arial, sans-serif" {{ old('font_family') == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                                        <option value="Helvetica, sans-serif" {{ old('font_family') == 'Helvetica, sans-serif' ? 'selected' : '' }}>Helvetica</option>
                                        <option value="Georgia, serif" {{ old('font_family') == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                                        <option value="Times New Roman, serif" {{ old('font_family') == 'Times New Roman, serif' ? 'selected' : '' }}>Times New Roman</option>
                                        <option value="Courier New, monospace" {{ old('font_family') == 'Courier New, monospace' ? 'selected' : '' }}>Courier New</option>
                                    </select>
                                    @error('font_family')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="font_size">Font Size (px)</label>
                                    <input type="number" class="form-control @error('font_size') is-invalid @enderror" 
                                           id="font_size" name="font_size" value="{{ old('font_size', 16) }}" 
                                           min="10" max="24" required>
                                    @error('font_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="custom_css">Custom CSS</label>
                            <textarea class="form-control @error('custom_css') is-invalid @enderror" 
                                      id="custom_css" name="custom_css" rows="6" 
                                      placeholder="/* Add your custom CSS here */">{{ old('custom_css') }}</textarea>
                            @error('custom_css')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Theme
                            </button>
                            <a href="{{ route('themes.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Preview</h6>
                </div>
                <div class="card-body">
                    <div id="theme-preview" class="theme-preview">
                        <div class="preview-header">
                            <h4>Sample Survey</h4>
                            <p>This is a preview of your theme</p>
                        </div>
                        <div class="preview-question">
                            <label>What is your favorite color?</label>
                            <div class="preview-options">
                                <div class="preview-option">
                                    <input type="radio" name="preview" id="preview1">
                                    <label for="preview1">Red</label>
                                </div>
                                <div class="preview-option">
                                    <input type="radio" name="preview" id="preview2">
                                    <label for="preview2">Blue</label>
                                </div>
                            </div>
                        </div>
                        <div class="preview-actions">
                            <button type="button" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.theme-preview {
    padding: 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.preview-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid;
}

.preview-question {
    margin-bottom: 20px;
}

.preview-options {
    margin-top: 10px;
}

.preview-option {
    margin-bottom: 8px;
}

.preview-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('theme-preview');
    const inputs = {
        primary_color: document.getElementById('primary_color'),
        secondary_color: document.getElementById('secondary_color'),
        background_color: document.getElementById('background_color'),
        text_color: document.getElementById('text_color'),
        font_family: document.getElementById('font_family'),
        font_size: document.getElementById('font_size'),
        custom_css: document.getElementById('custom_css')
    };

    function updatePreview() {
        const primaryColor = inputs.primary_color.value;
        const secondaryColor = inputs.secondary_color.value;
        const backgroundColor = inputs.background_color.value;
        const textColor = inputs.text_color.value;
        const fontFamily = inputs.font_family.value;
        const fontSize = inputs.font_size.value + 'px';
        const customCss = inputs.custom_css.value;

        preview.style.cssText = `
            background-color: ${backgroundColor};
            color: ${textColor};
            font-family: ${fontFamily};
            font-size: ${fontSize};
            --primary-color: ${primaryColor};
            --secondary-color: ${secondaryColor};
        `;

        // Apply custom CSS
        let customStyle = document.getElementById('custom-preview-style');
        if (!customStyle) {
            customStyle = document.createElement('style');
            customStyle.id = 'custom-preview-style';
            document.head.appendChild(customStyle);
        }
        customStyle.textContent = customCss;

        // Update preview elements
        const header = preview.querySelector('.preview-header');
        const actions = preview.querySelector('.preview-actions');
        
        header.style.borderBottomColor = primaryColor;
        actions.style.borderTopColor = secondaryColor;
        
        const button = preview.querySelector('.btn');
        button.style.backgroundColor = primaryColor;
        button.style.borderColor = primaryColor;
    }

    // Add event listeners
    Object.values(inputs).forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Initial preview update
    updatePreview();
});
</script>
@endpush
@endsection
