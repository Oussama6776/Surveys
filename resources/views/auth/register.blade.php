@extends('layouts.app')

@section('title', 'Inscription')
@section('page-title', 'Créer un compte')
@section('page-description', 'Rejoignez notre plateforme de sondages')
@section('guest', true)

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-center border-0 pt-4">
                    <h3 class="mb-0">Créer un compte</h3>
                    <small class="text-muted d-block">Rejoignez notre plateforme de sondages</small>
                    <span class="badge rounded-pill bg-success-subtle text-success mt-2">Inscription 100% gratuite</span>
                </div>
                <div class="card-body pt-3">
                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Nom complet</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                       class="form-control @error('name') is-invalid @enderror" placeholder="Ex. Jean Dupont">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Adresse e-mail</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                       class="form-control @error('email') is-invalid @enderror" placeholder="vous@exemple.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <input id="password" type="password" name="password" required autocomplete="new-password"
                                           class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">8 caractères minimum</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <div class="input-group">
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                           class="form-control" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    Créer un compte
                                </button>
                            </div>

                            <div class="col-12 text-center">
                                <small class="text-muted">
                                    Vous avez déjà un compte ?
                                    <a href="{{ route('login') }}">Connectez-vous</a>
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
document.getElementById('togglePasswordConfirm')?.addEventListener('click', function () {
  const input = document.getElementById('password_confirmation');
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