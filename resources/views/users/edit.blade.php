@extends('layouts.app')

@section('title', 'Modifier l\'Utilisateur - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Modifier l'Utilisateur</h1>
                    <p class="text-muted">{{ $user->name }} ({{ $user->email }})</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de l'Utilisateur</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Nouveau mot de passe</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Rôles <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($roles as $role)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="roles[]" value="{{ $role->id }}" 
                                                       id="role_{{ $role->id }}"
                                                       {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="role_{{ $role->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong class="text-primary">{{ $role->display_name }}</strong>
                                                            <br><small class="text-muted">{{ $role->description }}</small>
                                                        </div>
                                                        <span class="badge badge-primary">
                                                            {{ $role->name }}
                                                        </span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à Jour
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Actuelles</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="user-avatar mx-auto mb-2">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6>Rôles Actuels</h6>
                        @if($user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="badge badge-primary me-1">
                                    {{ $role->display_name }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">Aucun rôle assigné</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6>Statut</h6>
                        @if($user->email_verified_at)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Email vérifié
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> Email non vérifié
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6>Membre depuis</h6>
                        <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small>
                    </div>

                    <div class="mb-3">
                        <h6>Dernière connexion</h6>
                        <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning" onclick="resetPassword({{ $user->id }})">
                            <i class="fas fa-key"></i> Réinitialiser le Mot de Passe
                        </button>
                        
                        @if($user->id !== auth()->id())
                        <button class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})">
                            <i class="fas fa-trash"></i> Supprimer l'Utilisateur
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 24px;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
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

function resetPassword(userId) {
    const newPassword = prompt('Entrez le nouveau mot de passe (minimum 8 caractères):');
    if (newPassword && newPassword.length >= 8) {
        // Implémenter la réinitialisation via AJAX
        fetch(`/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ password: newPassword })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mot de passe réinitialisé avec succès !');
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue.');
        });
    } else if (newPassword) {
        alert('Le mot de passe doit contenir au moins 8 caractères.');
    }
}

function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
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
                window.location.href = '/users';
            } else {
                alert('Erreur: ' + data.message);
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
