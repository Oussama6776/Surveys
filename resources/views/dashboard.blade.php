@extends('layouts.app')

@section('title', 'Tableau de Bord')
@section('page-title', 'Tableau de bord')
@section('page-description', 'Bienvenue, ' . auth()->user()->name . ' !')

@section('content')
<div class="container-fluid">
    <!-- En-tête géré par le layout via les sections page-title et page-description -->
    <div class="row mb-2">
        <div class="col-12 text-end">
            <small class="text-muted">Dernière connexion : {{ auth()->user()->updated_at->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="row gy-3 gx-3 mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-uppercase text-primary small fw-semibold mb-1">
                                Total Sondages
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                {{ $surveys->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-poll fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-uppercase text-success small fw-semibold mb-1">
                                Réponses Total
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                {{ $surveys->sum('responses_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-uppercase text-info small fw-semibold mb-1">
                                Sondages Actifs
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                {{ $surveys->where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2 border-0">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-uppercase text-warning small fw-semibold mb-1">
                                Brouillons
                            </div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                {{ $surveys->where('status', 'draft')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bolt"></i> Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 g-md-4">
                        <!-- Créer un Sondage -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-plus-circle fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Nouveau Sondage</h5>
                                    <p class="card-text text-muted">Créer un sondage public ou privé</p>
                                    <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Créer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Gestion des Utilisateurs -->
                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin']) || auth()->user()->hasPermission('users.read'))
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-users fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Gestion Utilisateurs</h5>
                                    <p class="card-text text-muted">Créer et gérer les utilisateurs</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('users.index') }}" class="btn btn-success">
                                            <i class="fas fa-users"></i> Voir la Liste
                                        </a>
                                        @if(auth()->user()->hasAnyRole(['super_admin', 'admin']) || auth()->user()->hasPermission('users.create'))
                                        <a href="{{ route('users.create') }}" class="btn btn-outline-success">
                                            <i class="fas fa-user-plus"></i> Créer Utilisateur
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Analytics -->
                        @can('analytics.read')
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-chart-bar fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">Analytics</h5>
                                    <p class="card-text text-muted">Statistiques et rapports</p>
                                    <a href="{{ route('analytics.index') }}" class="btn btn-info">
                                        <i class="fas fa-chart-bar"></i> Voir
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endcan

                        <!-- Thèmes -->
                        @can('themes.read')
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-palette fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">Thèmes</h5>
                                    <p class="card-text text-muted">Personnaliser l'apparence</p>
                                    <a href="{{ route('themes.index') }}" class="btn btn-warning">
                                        <i class="fas fa-palette"></i> Gérer
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sondages Récents -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-poll"></i> Sondages Récents
                    </h6>
                    <a href="{{ route('surveys.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tous
                    </a>
                </div>
                <div class="card-body">
                    @if($surveys->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-poll fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun sondage créé</h5>
                            <p class="text-muted">Commencez par créer votre premier sondage.</p>
                            <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer un Sondage
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Statut</th>
                                        <th>Visibilité</th>
                                        <th>Réponses</th>
                                        <th>Créé le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($surveys->take(5) as $survey)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $survey->title }}</strong>
                                                @if($survey->description)
                                                    <br><small class="text-muted">{{ Str::limit($survey->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($survey->status === 'active')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-play-circle"></i> Actif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-edit"></i> Brouillon
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($survey->is_public)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-globe"></i> Public
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-lock"></i> Privé
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $survey->responses_count }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $survey->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('surveys.show', $survey) }}" 
                                                   class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('surveys.update')
                                                <a href="{{ route('surveys.edit', $survey) }}" 
                                                   class="btn btn-outline-secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('responses.read')
                                                <a href="{{ route('responses.index', $survey) }}" 
                                                   class="btn btn-outline-info" title="Réponses">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Accent borders aligned with brand */
.border-left-primary { border-left: 0.25rem solid var(--brand-primary) !important; }
.border-left-success { border-left: 0.25rem solid #198754 !important; }  /* Bootstrap success */
.border-left-info { border-left: 0.25rem solid #0dcaf0 !important; }     /* Bootstrap info */
.border-left-warning { border-left: 0.25rem solid #ffc107 !important; }  /* Bootstrap warning */

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush
@endsection 
