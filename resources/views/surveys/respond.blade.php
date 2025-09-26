@extends('layouts.app')

@section('title', 'Répondre au Sondage - ' . $survey->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête du sondage -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">{{ $survey->title }}</h3>
                </div>
                <div class="card-body">
                    @if($survey->description)
                        <p class="lead">{{ $survey->description }}</p>
                    @endif
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small class="text-muted">
                                <i class="fas fa-question-circle"></i> 
                                {{ $survey->questions->count() }} questions
                            </small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                Temps estimé : {{ $survey->questions->count() * 2 }} min
                            </small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">
                                @if($survey->is_public)
                                    <i class="fas fa-globe text-success"></i> Public
                                @else
                                    <i class="fas fa-lock text-warning"></i> Privé
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de réponse -->
            <form action="{{ route('responses.public.store', $survey) }}" method="POST" id="surveyForm">
                @csrf
                
                @if($survey->show_progress_bar)
                <!-- Barre de progression -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar"></div>
                        </div>
                        <small class="text-muted">
                            <span id="currentQuestion">1</span> sur {{ $survey->questions->count() }} questions
                        </small>
                    </div>
                </div>
                @endif

                <!-- Questions -->
                @foreach($survey->questions as $index => $question)
                <div class="card shadow mb-4 question-card" data-question="{{ $index + 1 }}">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Question {{ $index + 1 }}
                            @if($question->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="question-text mb-3">{{ $question->question_text }}</p>
                        
                        @if($question->type)
                            @switch(strtolower($question->type->name))
                                @case('text')
                                    <input type="text" 
                                           class="form-control" 
                                           name="answers[{{ $question->id }}]" 
                                           @if($question->is_required) required @endif
                                           placeholder="Votre réponse..."
                                           value="{{ $existingAnswers[$question->id] ?? '' }}">
                                    @break
                                    
                                @case('textarea')
                                    <textarea class="form-control" 
                                              name="answers[{{ $question->id }}]" 
                                              rows="4"
                                              @if($question->is_required) required @endif
                                              placeholder="Votre réponse...">{{ $existingAnswers[$question->id] ?? '' }}</textarea>
                                    @break
                                    
                                @case('multiple choice')
                                    @foreach($question->options as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option->id }}"
                                               id="option_{{ $option->id }}"
                                               @if($question->is_required) required @endif
                                               @if(isset($existingAnswers[$question->id]) && (string)$existingAnswers[$question->id] === (string)$option->id) checked @endif>
                                        <label class="form-check-label" for="option_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @break
                                    
                                @case('checkbox')
                                    @foreach($question->options as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="answers[{{ $question->id }}][]" 
                                               value="{{ $option->id }}"
                                               id="option_{{ $option->id }}"
                                               @php $vals = isset($existingAnswers[$question->id]) ? (array)json_decode($existingAnswers[$question->id], true) ?: (array)$existingAnswers[$question->id] : []; @endphp
                                               @if(in_array((string)$option->id, array_map('strval', $vals), true)) checked @endif>
                                        <label class="form-check-label" for="option_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @break
                                    
                                @case('select')
                                    <select class="form-select" 
                                            name="answers[{{ $question->id }}]"
                                            @if($question->is_required) required @endif>
                                        <option value="">Sélectionnez une option...</option>
                                        @foreach($question->options as $option)
                                        <option value="{{ $option->id }}" @if(isset($existingAnswers[$question->id]) && (string)$existingAnswers[$question->id] === (string)$option->id) selected @endif>{{ $option->option_text }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                    
                                @case('rating')
                                    <div class="rating-container">
                                        <div class="d-flex justify-content-center">
                                            @for($i = 1; $i <= 5; $i++)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ $i }}"
                                                       id="rating_{{ $question->id }}_{{ $i }}"
                                                       @if($question->is_required) required @endif
                                                       @if(isset($existingAnswers[$question->id]) && (string)$existingAnswers[$question->id] === (string)$i) checked @endif>
                                                <label class="form-check-label" for="rating_{{ $question->id }}_{{ $i }}">
                                                    <i class="fas fa-star text-warning"></i>
                                                </label>
                                            </div>
                                            @endfor
                                        </div>
                                        <div class="text-center mt-2">
                                            <small class="text-muted">1 = Très insatisfait, 5 = Très satisfait</small>
                                        </div>
                                    </div>
                                    @break
                                    
                                @default
                                    <input type="text" 
                                           class="form-control" 
                                           name="answers[{{ $question->id }}]" 
                                           @if($question->is_required) required @endif
                                           placeholder="Votre réponse...">
                            @endswitch
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Boutons d'action -->
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i>
                            {{ isset($isEditing) && $isEditing ? 'Mettre à jour mes réponses' : 'Soumettre les Réponses' }}
                        </button>
                        <a href="{{ route('surveys.show', $survey) }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.question-card {
    transition: all 0.3s ease;
}

.question-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.rating-container .form-check-input:checked + .form-check-label i {
    color: #ffc107 !important;
}

.progress {
    height: 8px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('surveyForm');
    const progressBar = document.getElementById('progressBar');
    const currentQuestionSpan = document.getElementById('currentQuestion');
    const questionCards = document.querySelectorAll('.question-card');
    const totalQuestions = questionCards.length;
    
    // Mise à jour de la barre de progression
    function updateProgress() {
        if (!progressBar) return;
        
        let answeredQuestions = 0;
        questionCards.forEach(card => {
            const inputs = card.querySelectorAll('input, select, textarea');
            let hasAnswer = false;
            
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    if (input.checked) hasAnswer = true;
                } else if (input.type === 'radio') {
                    if (input.checked) hasAnswer = true;
                } else if (input.value.trim() !== '') {
                    hasAnswer = true;
                }
            });
            
            if (hasAnswer) answeredQuestions++;
        });
        
        const progress = (answeredQuestions / totalQuestions) * 100;
        progressBar.style.width = progress + '%';
        
        if (currentQuestionSpan) {
            currentQuestionSpan.textContent = answeredQuestions;
        }
    }
    
    // Écouter les changements sur tous les inputs
    form.addEventListener('input', updateProgress);
    form.addEventListener('change', updateProgress);
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredQuestions = document.querySelectorAll('[required]');
        
        requiredQuestions.forEach(input => {
            if (!input.value || (input.type === 'checkbox' && !input.checked)) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez répondre à toutes les questions obligatoires.');
        }
    });
    
    // Initialiser la progression
    updateProgress();
});
</script>
@endpush
@endsection
