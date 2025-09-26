@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Role Management</h1>
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Role
                </a>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Roles</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rolesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Display Name</th>
                                    <th>Description</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $role->name === 'super_admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : 'info') }}">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td>{{ $role->display_name }}</td>
                                    <td>{{ $role->description }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $role->users_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ count($role->permissions) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('roles.show', $role) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('roles.edit', $role) }}" 
                                               class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!in_array($role->name, ['super_admin', 'admin']))
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="deleteRole({{ $role->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Permissions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="font-weight-bold">Survey Permissions</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> surveys.create</li>
                                <li><i class="fas fa-check text-success"></i> surveys.read</li>
                                <li><i class="fas fa-check text-success"></i> surveys.update</li>
                                <li><i class="fas fa-check text-success"></i> surveys.delete</li>
                                <li><i class="fas fa-check text-success"></i> surveys.publish</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h6 class="font-weight-bold">Question Permissions</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> questions.create</li>
                                <li><i class="fas fa-check text-success"></i> questions.read</li>
                                <li><i class="fas fa-check text-success"></i> questions.update</li>
                                <li><i class="fas fa-check text-success"></i> questions.delete</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h6 class="font-weight-bold">Response Permissions</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> responses.read</li>
                                <li><i class="fas fa-check text-success"></i> responses.delete</li>
                                <li><i class="fas fa-check text-success"></i> responses.export</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <h6 class="font-weight-bold">User Permissions</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> users.create</li>
                                <li><i class="fas fa-check text-success"></i> users.read</li>
                                <li><i class="fas fa-check text-success"></i> users.update</li>
                                <li><i class="fas fa-check text-success"></i> users.delete</li>
                                <li><i class="fas fa-check text-success"></i> users.manage</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteRole(roleId) {
    if (confirm('Are you sure you want to delete this role?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/roles/${roleId}`;
        
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

$(document).ready(function() {
    $('#rolesTable').DataTable({
        "pageLength": 25,
        "order": [[ 1, "asc" ]]
    });
});
</script>
@endpush
@endsection
