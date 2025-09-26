@extends('layouts.app')

@section('title', 'Connexion')
@section('page-title', 'Bienvenue')
@section('page-description', 'Connectez-vous à votre compte')
@section('guest', true)

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-center border-0 pt-4">
                    <h3 class="mb-0">Connexion</h3>
                    <small class="text-muted d-block">Accédez à votre tableau de bord</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf
                        <input type="hidden" name="survey" value="{{ request('survey') }}" />
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Adresse e-mail</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                       class="form-control @error('email') is-invalid @enderror" placeholder="vous@exemple.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <input id="password" type="password" name="password" required autocomplete="current-password"
                                           class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @if (Route::has('password.request'))
                                <div class="form-text">
                                    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                                </div>
                                @endif
                            </div>

                            <div class="col-12 d-flex align-items-center justify-content-between pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                </div>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                            </div>

                            <div class="col-12 text-center">
                                <small class="text-muted">
                                    Vous n'avez pas de compte ?
                                    <a href="{{ route('register') }}">Inscrivez-vous gratuitement</a>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('togglePassword')?.addEventListener('click', function () {
  const input = document.getElementById('password');
  const icon = this.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye','fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash','fa-eye');
  }
});
</script>
@endpush
@endsection 
