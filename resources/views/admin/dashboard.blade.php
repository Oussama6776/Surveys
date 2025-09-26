@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête Admin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-primary" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-crown fa-2x me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-0">Bienvenue, {{ auth()->user()->name }} !</h4>
                        <p class="mb-0">Vous êtes connecté en tant que <strong>{{ auth()->user()->roles->first()->display_name ?? 'Administrateur' }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Administrateur -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3">
                <i class="fas fa-tools text-primary"></i> 
                Actions Administrateur
            </h3>
        </div>
    </div>

    <div class="row">
        <!-- Gestion des Utilisateurs -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-primary shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Gestion des Utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Créer, modifier et gérer les utilisateurs du système avec leurs rôles.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Voir tous les Utilisateurs
                        </a>
                        <a href="{{ route('users.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Créer un Nouvel Utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des Sondages -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-success shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-poll"></i> Gestion des Sondages
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Créer, publier et gérer tous les sondages du système.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('surveys.index') }}" class="btn btn-success">
                            <i class="fas fa-list"></i> Voir tous les Sondages
                        </a>
                        <a href="{{ route('surveys.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i> Créer un Nouveau Sondage
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des Rôles -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-warning shadow h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield"></i> Gestion des Rôles
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Gérer les rôles et permissions des utilisateurs.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('roles.index') }}" class="btn btn-warning">
                            <i class="fas fa-list"></i> Voir tous les Rôles
                        </a>
                        <a href="{{ route('roles.create') }}" class="btn btn-outline-warning">
                            <i class="fas fa-plus"></i> Créer un Nouveau Rôle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-info shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Consulter les statistiques et rapports du système.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('analytics.index') }}" class="btn btn-info">
                            <i class="fas fa-chart-line"></i> Voir les Statistiques
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thèmes -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-secondary shadow h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-palette"></i> Thèmes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Personnaliser l'apparence des sondages.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('themes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-palette"></i> Gérer les Thèmes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commandes Rapides -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-dark shadow h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-terminal"></i> Commandes Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Actions rapides pour la gestion du système.</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-dark" onclick="showQuickCommands()">
                            <i class="fas fa-terminal"></i> Voir les Commandes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3">
                <i class="fas fa-chart-pie text-info"></i> 
                Statistiques Rapides
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Utilisateurs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::count() }}
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
                                Total Sondages
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Survey::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-poll fa-2x text-gray-300"></i>
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
                                Sondages Actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Survey::where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                                Total Réponses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Response::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les commandes rapides -->
<div class="modal fade" id="quickCommandsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-terminal"></i> Commandes Rapides
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Gestion des Utilisateurs</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <code>php artisan user:list</code>
                                <small class="text-muted d-block">Lister tous les utilisateurs</small>
                            </div>
                            <div class="list-group-item">
                                <code>php artisan user:create-admin "Nom" email@example.com password123</code>
                                <small class="text-muted d-block">Créer un admin</small>
                            </div>
                            <div class="list-group-item">
                                <code>php artisan user:assign-role email@example.com admin</code>
                                <small class="text-muted d-block">Assigner un rôle</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Gestion du Système</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <code>php artisan migrate</code>
                                <small class="text-muted d-block">Exécuter les migrations</small>
                            </div>
                            <div class="list-group-item">
                                <code>php artisan db:seed</code>
                                <small class="text-muted d-block">Exécuter les seeders</small>
                            </div>
                            <div class="list-group-item">
                                <code>php artisan cache:clear</code>
                                <small class="text-muted d-block">Vider le cache</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
function showQuickCommands() {
    const modal = new bootstrap.Modal(document.getElementById('quickCommandsModal'));
    modal.show();
}
</script>
@endpush
@endsection
