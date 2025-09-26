@extends('layouts.app')

@section('title', 'Créer un Sondage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Créer un Nouveau Sondage</h1>
                <a href="{{ route('surveys.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Sondage</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('surveys.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="title">Titre du sondage <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Date de début</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Date de fin</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section Visibilité -->
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-eye"></i> Visibilité du Sondage
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visibility" 
                                                   id="visibility_public" value="public" 
                                                   {{ old('visibility', 'public') == 'public' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="visibility_public">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-globe text-success fa-2x me-3"></i>
                                                    <div>
                                                        <strong class="text-success">Public</strong>
                                                        <br><small class="text-muted">Accessible à tous sans authentification</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="visibility" 
                                                   id="visibility_private" value="private" 
                                                   {{ old('visibility') == 'private' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="visibility_private">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-lock text-warning fa-2x me-3"></i>
                                                    <div>
                                                        <strong class="text-warning">Privé</strong>
                                                        <br><small class="text-muted">Nécessite une authentification</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Options pour sondage privé -->
                                <div id="private_options" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Options pour sondage privé</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="requires_access_code" 
                                                   id="requires_access_code" value="1" 
                                                   {{ old('requires_access_code') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="requires_access_code">
                                                Générer un code d'accès unique
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            Un code d'accès sera généré automatiquement pour ce sondage privé.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Paramètres Avancés -->
                        <div class="card border-secondary mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-cog"></i> Paramètres Avancés
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="allow_multiple_responses" 
                                                   id="allow_multiple_responses" value="1" 
                                                   {{ old('allow_multiple_responses') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allow_multiple_responses">
                                                Autoriser plusieurs réponses par utilisateur
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="max_responses">Nombre maximum de réponses</label>
                                            <input type="number" class="form-control" id="max_responses" 
                                                   name="max_responses" value="{{ old('max_responses') }}" 
                                                   min="1" placeholder="Illimité si vide">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="show_progress_bar" 
                                                   id="show_progress_bar" value="1" 
                                                   {{ old('show_progress_bar', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_progress_bar">
                                                Afficher la barre de progression
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="randomize_questions" 
                                                   id="randomize_questions" value="1" 
                                                   {{ old('randomize_questions') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="randomize_questions">
                                                Mélanger l'ordre des questions
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Thème -->
                        <div class="form-group">
                            <label for="theme_id">Thème du sondage</label>
                            <select class="form-control @error('theme_id') is-invalid @enderror" 
                                    id="theme_id" name="theme_id">
                                <option value="">Thème par défaut</option>
                                @foreach(\App\Models\SurveyTheme::all() as $theme)
                                <option value="{{ $theme->id }}" {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                                    {{ $theme->display_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('theme_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le Sondage
                            </button>
                            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu</h6>
                </div>
                <div class="card-body">
                    <div id="survey_preview">
                        <h5 id="preview_title">Titre du sondage</h5>
                        <p id="preview_description" class="text-muted">Description du sondage</p>
                        <div class="mb-3">
                            <span id="preview_visibility" class="badge badge-success">
                                <i class="fas fa-globe"></i> Public
                            </span>
                        </div>
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-calendar"></i> 
                                <span id="preview_dates">Pas de dates définies</span>
                            </small>
                        </div>
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
                            <i class="fas fa-lightbulb text-warning"></i>
                            <small>Les sondages publics sont accessibles à tous</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <small>Les sondages privés nécessitent une connexion</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <small>Vous pourrez ajouter des questions après la création</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const visibilityRadios = document.querySelectorAll('input[name="visibility"]');
    const privateOptions = document.getElementById('private_options');
    const previewVisibility = document.getElementById('preview_visibility');
    
    // Gestion de la visibilité
    visibilityRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'private') {
                privateOptions.style.display = 'block';
                previewVisibility.innerHTML = '<i class="fas fa-lock"></i> Privé';
                previewVisibility.className = 'badge badge-warning';
            } else {
                privateOptions.style.display = 'none';
                previewVisibility.innerHTML = '<i class="fas fa-globe"></i> Public';
                previewVisibility.className = 'badge badge-success';
            }
        });
    });

    // Aperçu en temps réel
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    titleInput.addEventListener('input', function() {
        document.getElementById('preview_title').textContent = this.value || 'Titre du sondage';
    });
    
    descriptionInput.addEventListener('input', function() {
        document.getElementById('preview_description').textContent = this.value || 'Description du sondage';
    });
    
    function updateDates() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate || endDate) {
            let dateText = '';
            if (startDate) {
                dateText += 'Du ' + new Date(startDate).toLocaleDateString();
            }
            if (endDate) {
                dateText += (startDate ? ' au ' : 'Jusqu\'au ') + new Date(endDate).toLocaleDateString();
            }
            document.getElementById('preview_dates').textContent = dateText;
        } else {
            document.getElementById('preview_dates').textContent = 'Pas de dates définies';
        }
    }
    
    startDateInput.addEventListener('change', updateDates);
    endDateInput.addEventListener('change', updateDates);
    
    // Initialiser l'affichage
    const checkedVisibility = document.querySelector('input[name="visibility"]:checked');
    if (checkedVisibility) {
        checkedVisibility.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection