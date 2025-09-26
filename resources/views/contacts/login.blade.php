@extends('layouts.app')

@section('title', 'Connexion Contact - ' . $survey->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Connexion pour répondre au sondage</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('contacts.login.attempt', $survey) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', request('email')) }}" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                            <a href="{{ route('surveys.respond', $survey) }}" class="btn btn-outline-secondary">Retour au sondage</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
