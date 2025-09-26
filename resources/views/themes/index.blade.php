@extends('layouts.app')

@section('title', 'Survey Themes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Survey Themes</h1>
                <div class="btn-group">
                    <a href="{{ route('themes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Theme
                    </a>
                    <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#importModal">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Themes Grid -->
    <div class="row">
        @foreach($themes as $theme)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header" style="background-color: {{ $theme->primary_color }}; color: white;">
                    <h6 class="m-0 font-weight-bold">{{ $theme->display_name }}</h6>
                </div>
                <div class="card-body" style="background-color: {{ $theme->background_color }}; color: {{ $theme->text_color }};">
                    <p class="card-text">{{ $theme->description }}</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Primary</small>
                            <div class="color-preview" style="background-color: {{ $theme->primary_color }}; width: 100%; height: 20px; border-radius: 3px;"></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Secondary</small>
                            <div class="color-preview" style="background-color: {{ $theme->secondary_color }}; width: 100%; height: 20px; border-radius: 3px;"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-font"></i> {{ $theme->font_family }} ({{ $theme->font_size }}px)
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="btn-group w-100">
                        <a href="{{ route('themes.preview', $theme) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ route('themes.edit', $theme) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('themes.duplicate', $theme) }}">
                                    <i class="fas fa-copy"></i> Duplicate
                                </a>
                                <a class="dropdown-item" href="{{ route('themes.export', $theme) }}">
                                    <i class="fas fa-download"></i> Export
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteTheme({{ $theme->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($themes->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No themes found</h4>
                <p class="text-muted">Create your first theme to get started.</p>
                <a href="{{ route('themes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Theme
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Theme</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('themes.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="theme_file">Theme File (JSON)</label>
                        <input type="file" class="form-control-file" id="theme_file" name="theme_file" accept=".json" required>
                        <small class="form-text text-muted">Select a JSON file exported from another theme.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Theme</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteTheme(themeId) {
    if (confirm('Are you sure you want to delete this theme?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/themes/${themeId}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
