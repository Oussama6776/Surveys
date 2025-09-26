@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $survey->title }}</h1>
                    @if($survey->description)
                        <p class="text-muted mb-0">{{ $survey->description }}</p>
                    @endif
                </div>
                <div class="btn-group">
                    @can('surveys.update')
                    <a href="{{ route('surveys.edit', $survey) }}" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    @endcan
                    <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informations du sondage -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du Sondage</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Statut</h6>
                            <span class="badge badge-{{ $survey->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($survey->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6>Visibilité</h6>
                            @if($survey->is_public)
                                <span class="badge badge-success">
                                    <i class="fas fa-globe"></i> Public
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-lock"></i> Privé
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($survey->start_date || $survey->end_date)
                    <hr>
                    <div class="row">
                        @if($survey->start_date)
                        <div class="col-md-6">
                            <h6>Date de début</h6>
                            <p class="text-muted">{{ $survey->start_date->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($survey->end_date)
                        <div class="col-md-6">
                            <h6>Date de fin</h6>
                            <p class="text-muted">{{ $survey->end_date->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($survey->requires_access_code)
                    <hr>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-key"></i> Code d'Accès</h6>
                        @php
                            $accessCode = $survey->accessCodes()->first();
                        @endphp
                        @if($accessCode)
                            <div class="d-flex align-items-center">
                                <code class="bg-light p-2 rounded me-3" style="font-size: 1.2rem;">
                                    {{ $accessCode->access_code }}
                                </code>
                                <button class="btn btn-sm btn-outline-primary" onclick="copyAccessCode('{{ $accessCode->access_code }}')">
                                    <i class="fas fa-copy"></i> Copier
                                </button>
                            </div>
                            <small class="text-muted">
                                Expire le {{ $accessCode->expires_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Questions -->
            <div class="card shadow mb-4" id="questions-section">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Questions</h6>
                    @auth
                    @if(auth()->user()->canModifySurvey($survey))
                    <a href="{{ route('questions.create', $survey) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter une Question
                    </a>
                    @endif
                    @endauth
                </div>
                <div class="card-body">
                    @if($survey->questions->count() > 0)
                        <div class="list-group">
                            @foreach($survey->questions as $question)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $question->question_text }}</h6>
                                        <small class="text-muted">
                                            Type: {{ $question->type->name ?? 'Non défini' }}
                                            @if($question->is_required)
                                                | <span class="text-danger">Requis</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @auth
                                        @if(auth()->user()->canModifySurvey($survey))
                                        <a href="{{ route('questions.edit', [$survey, $question]) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <form action="{{ route('questions.destroy', [$survey, $question]) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                        @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune question</h5>
                            <p class="text-muted">Commencez par ajouter des questions à votre sondage.</p>
                            @can('questions.create')
                            <a href="{{ route('questions.create', $survey) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter la Première Question
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $survey->questions->count() }}</h4>
                                <small class="text-muted">Questions</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $survey->responses->count() }}</h4>
                            <small class="text-muted">Réponses</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @auth
                        @if(auth()->user()->canModifySurvey($survey))
                        <a href="{{ route('questions.create', $survey) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Ajouter des questions
                        </a>
                        <a href="#questions-section" class="btn btn-outline-secondary">
                            <i class="fas fa-pen"></i> Modifier les questions
                        </a>
                        @endif
                        @endauth

                        @if($survey->status === 'draft')
                        <button class="btn btn-success" onclick="publishSurvey({{ $survey->id }})" id="publishBtn">
                            <i class="fas fa-play"></i> Publier le Sondage
                        </button>
                        @else
                        <button class="btn btn-warning" onclick="unpublishSurvey({{ $survey->id }})" id="unpublishBtn">
                            <i class="fas fa-pause"></i> Dépublier
                        </button>
                        @endif

                        <a href="{{ route('surveys.respond', $survey) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Prévisualiser
                        </a>

                        @can('responses.read')
                        <a href="{{ route('responses.index', $survey) }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Voir les Réponses
                        </a>
                        @endcan

                        @can('surveys.duplicate')
                        <button class="btn btn-secondary" onclick="duplicateSurvey({{ $survey->id }})">
                            <i class="fas fa-copy"></i> Dupliquer
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Partage -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Partage</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Lien du sondage</label>
                        <div class="input-group">
                            <input type="text" class="form-control" 
                                   value="{{ route('surveys.respond', $survey) }}" 
                                   id="surveyLink" readonly>
                            <button class="btn btn-outline-secondary" onclick="copySurveyLink()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Importer des Contacts (Privé) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Envoyer le sondage à des contacts</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Importez un fichier Excel (.xlsx) ou CSV contenant les colonnes: <code>nom, prenom, email</code>. Les contacts sont regroupés automatiquement et reçoivent un identifiant (email) et un mot de passe généré pour ce sondage. Aucun compte utilisateur n'est créé.</p>
                    @auth
                    @if(auth()->user()->canModifySurvey($survey))
                    <form action="{{ route('surveys.contacts.import', $survey) }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        <div class="mb-2">
                            <input type="file" name="contacts_file" accept=".csv,.xlsx" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="group_name" class="form-control" placeholder="Nom du groupe (optionnel)" />
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-upload"></i> Importer et Envoyer
                        </button>
                    </form>
                    <div class="d-grid gap-2">
                        <a href="{{ route('surveys.contacts.template', $survey) }}" class="btn btn-light">
                            <i class="fas fa-file-download"></i> Télécharger la trame (CSV)
                        </a>
                        <a href="{{ route('surveys.contacts.index', $survey) }}" class="btn btn-light">
                            <i class="fas fa-address-book"></i> Voir les contacts importés
                        </a>
                        <a href="{{ route('surveys.contacts.export', $survey) }}" class="btn btn-light">
                            <i class="fas fa-file-export"></i> Exporter les contacts (CSV)
                        </a>
                    </div>

                    @if(isset($contactGroups) && $contactGroups->count())
                    <hr>
                    <form action="{{ route('surveys.contacts.send', $survey) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Envoyer à un groupe existant</label>
                            <select name="contact_group_id" class="form-select" required>
                                <option value="">-- Sélectionner un groupe --</option>
                                @foreach($contactGroups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane"></i> Envoyer les invitations</button>
                    </form>
                    @endif
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyAccessCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert('Code d\'accès copié !');
    });
}

function copySurveyLink() {
    const linkInput = document.getElementById('surveyLink');
    linkInput.select();
    document.execCommand('copy');
    alert('Lien copié !');
}

function publishSurvey(surveyId) {
    if (confirm('Êtes-vous sûr de vouloir publier ce sondage ?')) {
        const button = document.getElementById('publishBtn');
        const originalText = button.innerHTML;
        
        // Désactiver le bouton et afficher un indicateur de chargement
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication...';
        
        // Envoyer la requête AJAX
        fetch(`/surveys/${surveyId}/publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de succès
                showAlert('success', data.message);
                
                // Mettre à jour le bouton
                button.className = 'btn btn-warning';
                button.innerHTML = '<i class="fas fa-pause"></i> Dépublier';
                button.onclick = () => unpublishSurvey(surveyId);
                button.id = 'unpublishBtn';
                
                // Mettre à jour le statut dans la page
                const statusBadge = document.querySelector('.badge');
                if (statusBadge) {
                    statusBadge.className = 'badge badge-success';
                    statusBadge.textContent = 'Actif';
                }
            } else {
                showAlert('error', data.message);
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Une erreur est survenue lors de la publication.');
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
}

function unpublishSurvey(surveyId) {
    if (confirm('Êtes-vous sûr de vouloir dépublier ce sondage ?')) {
        const button = document.getElementById('unpublishBtn');
        const originalText = button.innerHTML;
        
        // Désactiver le bouton et afficher un indicateur de chargement
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Dépublication...';
        
        // Envoyer la requête AJAX
        fetch(`/surveys/${surveyId}/unpublish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de succès
                showAlert('success', data.message);
                
                // Mettre à jour le bouton
                button.className = 'btn btn-success';
                button.innerHTML = '<i class="fas fa-play"></i> Publier le Sondage';
                button.onclick = () => publishSurvey(surveyId);
                button.id = 'publishBtn';
                
                // Mettre à jour le statut dans la page
                const statusBadge = document.querySelector('.badge');
                if (statusBadge) {
                    statusBadge.className = 'badge badge-secondary';
                    statusBadge.textContent = 'Brouillon';
                }
            } else {
                showAlert('error', data.message);
                button.disabled = false;
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Une erreur est survenue lors de la dépublication.');
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }
}

function showAlert(type, message) {
    // Créer l'alerte
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insérer l'alerte en haut de la page
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Supprimer automatiquement l'alerte après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function duplicateSurvey(surveyId) {
    if (confirm('Êtes-vous sûr de vouloir dupliquer ce sondage ?')) {
        // Implémenter la logique de duplication
        console.log('Duplicating survey:', surveyId);
    }
}

</script>
@endpush
@endsection
