<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation Sondage</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .btn { display: inline-block; padding: 10px 16px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 6px; }
        .muted { color: #666; font-size: 12px; }
    </style>
    </head>
<body>
    <h2>Invitation à répondre au sondage</h2>
    <p>Bonjour {{ $contact->prenom }} {{ $contact->nom }},</p>
    <p>Vous avez été invité à répondre au sondage: <strong>{{ $survey->title }}</strong>.</p>

    <p>
        Lien sécurisé d'accès:
        <br>
        <a class="btn" href="{{ $inviteUrl }}">Accéder au sondage</a>
    </p>

    <h3>Vos identifiants</h3>
    <ul>
        <li>Identifiant (email): <strong>{{ $contact->email }}</strong></li>
        @if($plainPassword)
        <li>Mot de passe: <strong>{{ $plainPassword }}</strong></li>
        @endif
    </ul>
    <p>
        Connexion: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>.
        @if($survey->is_public)
            Ce sondage est public: vous pouvez aussi répondre sans vous connecter.
        @else
            Ce sondage est privé: connectez-vous avant de répondre.
        @endif
    </p>

    <p class="muted">Si vous n'attendiez pas cet email, vous pouvez l'ignorer.</p>
</body>
</html>
