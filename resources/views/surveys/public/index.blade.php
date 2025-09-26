@extends('layouts.app')

@section('title', 'Sondages Publics')
@section('guest', true)

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Sondages publics</h1>
            <small class="text-muted">Répondez sans vous connecter</small>
            <span class="badge rounded-pill bg-success-subtle text-success ms-2">100% gratuit</span>
        </div>
        <div class="mt-2 mt-md-0">
            @auth
                <a href="{{ route('surveys.create') }}" class="btn btn-outline-secondary"><i class="fas fa-plus me-1"></i> Nouveau sondage</a>
                <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary">Gérer mes sondages</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary me-2">Se connecter</a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary me-2">Créer un compte</a>
            @endauth
        </div>
    </div>

    @if($surveys->count())
        <div class="row g-3 g-md-4">
            @foreach($surveys as $survey)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        @php
                            $isStarted = !$survey->start_date || $survey->start_date->isPast();
                            $isNotEnded = !$survey->end_date || $survey->end_date->isFuture();
                            $isActive = $isStarted && $isNotEnded;
                        @endphp
                        <div class="d-flex align-items-start justify-content-between">
                            <h5 class="card-title d-flex align-items-start mb-0 me-2">
                                <i class="fas fa-poll-h text-primary me-2 mt-1"></i>
                                <span class="text-truncate" title="{{ $survey->title }}">{{ $survey->title }}</span>
                            </h5>
                            <div class="ms-2">
                                @if($isActive)
                                    <span class="badge bg-success"><i class="fas fa-play-circle"></i> Actif</span>
                                @elseif($isStarted && !$isNotEnded)
                                    <span class="badge bg-secondary"><i class="fas fa-flag-checkered"></i> Terminé</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Programmé</span>
                                @endif
                            </div>
                        </div>
                        @if($survey->description)
                        <p class="card-text text-muted mt-2">{{ Str::limit($survey->description, 120) }}</p>
                        @endif
                        <div class="d-flex align-items-center gap-2 text-muted small mt-1">
                            <span class="badge bg-info text-dark">{{ $survey->responses_count ?? 0 }} réponses</span>
                        </div>
                        <div class="mt-auto">
                            @if($isActive)
                                <a href="{{ route('survey.public.show', $survey) }}" class="btn btn-primary w-100">Répondre</a>
                            @elseif(!$isStarted)
                                <button class="btn btn-outline-secondary w-100" disabled>Disponible bientôt</button>
                            @else
                                <button class="btn btn-outline-secondary w-100" disabled>Terminé</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white text-muted small d-flex justify-content-between">
                        <span><i class="far fa-calendar me-1"></i> {{ optional($survey->created_at)->format('d/m/Y') }}</span>
                        @if($survey->end_date)
                            <span><i class="far fa-calendar-check me-1"></i> fin {{ $survey->end_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $surveys->links() }}
        </div>
    @else
        <div class="alert alert-info">
            Aucun sondage public n'est disponible pour le moment.
        </div>
    @endif
</div>
@endsection

