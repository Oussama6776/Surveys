@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Gestion des Utilisateurs</h1>
                    <p class="text-muted">Créer et gérer les utilisateurs du système</p>
                </div>
                @if(auth()->user()->hasAnyRole(['super_admin', 'admin']) || auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nouvel Utilisateur
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Utilisateurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $users->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Administrateurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $users->where('roles.0.name', 'admin')->count() + $users->where('roles.0.name', 'super_admin')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Créateurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $users->where('roles.0.name', 'survey_creator')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Clients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $users->where('roles.0.name', 'client')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des utilisateurs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> Liste des Utilisateurs
            </h6>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôles</th>
                                <th>Statut</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->id === auth()->id())
                                                <span class="badge badge-info">Vous</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </td>
                                <td>
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-primary me-1">
                                                {{ $role->display_name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Aucun rôle</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Vérifié
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> En attente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @can('users.read')
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('users.update')
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('users.delete')
                                        @if($user->id !== auth()->id())
                                        <button class="btn btn-outline-danger" 
                                                onclick="deleteUser({{ $user->id }})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier utilisateur.</p>
                    @can('users.create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Créer un Utilisateur
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush

@push('scripts')
<script>
function getRoleBadgeColor(roleName) {
    const colors = {
        'super_admin': 'danger',
        'admin': 'warning',
        'survey_creator': 'primary',
        'survey_viewer': 'info',
        'client': 'success',
        'moderator': 'secondary'
    };
    return colors[roleName] || 'secondary';
}

function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        // Implémenter la suppression via AJAX
        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression.');
        });
    }
}
</script>
@endpush
@endsection
