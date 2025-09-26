@extends('layouts.app')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Créer un Nouvel Utilisateur</h1>
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
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 caractères</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
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
                                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
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
                                <i class="fas fa-save"></i> Créer l'Utilisateur
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
                    <h6 class="m-0 font-weight-bold text-primary">Guide des Rôles</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-danger">Super Admin</h6>
                        <small class="text-muted">Accès complet au système</small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-warning">Admin</h6>
                        <small class="text-muted">Gestion des utilisateurs et système</small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Créateur d'Enquêtes</h6>
                        <small class="text-muted">Crée et gère ses enquêtes</small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-info">Lecteur d'Enquêtes</h6>
                        <small class="text-muted">Consulte les enquêtes</small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-success">Client</h6>
                        <small class="text-muted">Répond aux enquêtes publiques</small>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-secondary">Modérateur</h6>
                        <small class="text-muted">Modère le contenu</small>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conseils</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <small>Un utilisateur peut avoir plusieurs rôles</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <small>Le mot de passe sera envoyé par email</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <small>Les rôles peuvent être modifiés plus tard</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Validation côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const roles = document.querySelectorAll('input[name="roles[]"]:checked');
    if (roles.length === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un rôle pour l\'utilisateur.');
        return false;
    }
});
</script>
@endpush
@endsection
