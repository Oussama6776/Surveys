@extends('layouts.app')

@section('title', 'Sondages')
@section('page-title', 'Sondages')
@section('page-description', 'Gérez vos sondages: créer, modifier, publier et analyser')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="h4 mb-1">Mes sondages</h2>
            <small class="text-muted">Liste et actions rapides</small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('surveys.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouveau sondage
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Réponses</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surveys as $survey)
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-poll text-primary me-2 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $survey->title }}</div>
                                        @if($survey->description)
                                            <small class="text-muted d-block">{{ Str::limit($survey->description, 80) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $survey->responses_count ?? 0 }}</span>
                            </td>
                            <td>
                                @php
                                    $isStarted = !$survey->start_date || $survey->start_date->isPast();
                                    $isNotEnded = !$survey->end_date || $survey->end_date->isFuture();
                                @endphp
                                @if($isStarted && $isNotEnded)
                                    <span class="badge bg-success"><i class="fas fa-play-circle"></i> Actif</span>
                                @elseif($isStarted && !$isNotEnded)
                                    <span class="badge bg-secondary"><i class="fas fa-flag-checkered"></i> Terminé</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Programmé</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $survey->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('surveys.show', $survey) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('surveys.edit', $survey) }}" class="btn btn-outline-secondary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('surveys.destroy', $survey) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce sondage ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucun sondage trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-center">
                {{ $surveys->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 