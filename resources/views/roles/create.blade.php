@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Role</h1>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Role Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Role Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., content_manager" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Use lowercase letters and underscores only</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">Display Name</label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name') }}" 
                                           placeholder="e.g., Content Manager" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Describe what this role can do...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="permission-groups">
                                @foreach($permissions as $group => $groupPermissions)
                                <div class="permission-group mb-4">
                                    <h6 class="font-weight-bold text-primary">{{ $group }}</h6>
                                    <div class="row">
                                        @foreach($groupPermissions as $permission => $description)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="permissions[]" value="{{ $permission }}" 
                                                       id="permission_{{ $permission }}"
                                                       {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission }}">
                                                    <strong>{{ $permission }}</strong>
                                                    <br><small class="text-muted">{{ $description }}</small>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Role
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Permission Summary</h6>
                </div>
                <div class="card-body">
                    <div id="permission-summary">
                        <p class="text-muted">Select permissions to see a summary here.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="selectAllPermissions()">
                            <i class="fas fa-check-square"></i> Select All
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-2" onclick="deselectAllPermissions()">
                            <i class="fas fa-square"></i> Deselect All
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectGroupPermissions('Surveys')">
                            <i class="fas fa-clipboard-list"></i> Survey Permissions
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectGroupPermissions('Users')">
                            <i class="fas fa-users"></i> User Permissions
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectGroupPermissions('System')">
                            <i class="fas fa-cog"></i> System Permissions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionSummary();
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionSummary();
}

function selectGroupPermissions(groupName) {
    // This would select permissions for a specific group
    // Implementation depends on how permissions are grouped in the view
    updatePermissionSummary();
}

function updatePermissionSummary() {
    const selectedPermissions = Array.from(document.querySelectorAll('input[name="permissions[]"]:checked'))
        .map(checkbox => checkbox.value);
    
    const summaryDiv = document.getElementById('permission-summary');
    
    if (selectedPermissions.length === 0) {
        summaryDiv.innerHTML = '<p class="text-muted">Select permissions to see a summary here.</p>';
        return;
    }
    
    const summary = `
        <h6>Selected Permissions (${selectedPermissions.length})</h6>
        <ul class="list-unstyled">
            ${selectedPermissions.map(permission => `<li><i class="fas fa-check text-success"></i> ${permission}</li>`).join('')}
        </ul>
    `;
    
    summaryDiv.innerHTML = summary;
}

// Add event listeners to all permission checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionSummary);
    });
    
    // Initial summary update
    updatePermissionSummary();
});
</script>
@endpush
@endsection
