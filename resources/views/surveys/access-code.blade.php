@extends('layouts.app')

@section('title', 'Code d\'Accès - ' . $survey->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <i class="fas fa-lock fa-3x mb-3"></i>
                    <h4 class="mb-0">Sondage Privé</h4>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h5 class="text-primary">{{ $survey->title }}</h5>
                        @if($survey->description)
                            <p class="text-muted">{{ $survey->description }}</p>
                        @endif
                    </div>

                    <form action="{{ route('surveys.verify-access-code', $survey) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="access_code" class="form-label">
                                <i class="fas fa-key"></i> Code d'accès
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center @error('access_code') is-invalid @enderror" 
                                   id="access_code" 
                                   name="access_code" 
                                   placeholder="Entrez le code d'accès"
                                   style="font-size: 1.5rem; letter-spacing: 0.2em;"
                                   required>
                            @error('access_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-unlock"></i> Accéder au Sondage
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Ce sondage nécessite un code d'accès pour être consulté.
                        </small>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux Sondages
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    border-radius: 15px;
}

.form-control-lg {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-lg {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accessCodeInput = document.getElementById('access_code');
    
    // Convertir en majuscules automatiquement
    accessCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Focus sur le champ
    accessCodeInput.focus();
});
</script>
@endpush
@endsection
