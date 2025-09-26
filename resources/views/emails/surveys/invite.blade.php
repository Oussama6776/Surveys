<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation Sondage</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111;">
    <h2>Invitation à répondre au sondage</h2>
    <p>Bonjour {{ $user->name ?? ($user->email) }},</p>
    <p>Vous avez été invité à répondre au sondage privé: <strong>{{ $survey->title }}</strong>.</p>

    <p>
        Lien direct vers le sondage:
        <br>
        <a href="{{ $surveyUrl }}">{{ $surveyUrl }}</a>
    </p>

    <h3>Informations de connexion</h3>
    <ul>
        <li>Email: <strong>{{ $user->email }}</strong></li>
        @if($plainPassword)
        <li>Mot de passe temporaire: <strong>{{ $plainPassword }}</strong></li>
        @endif
    </ul>

    <p>
        Votre rôle: <strong>Survey Viewer</strong> (lecture et réponse aux sondages). 
        Connectez-vous ici: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>.
    </p>

    <p style="color:#666; font-size: 12px;">Si vous n'attendiez pas cet email, vous pouvez l'ignorer.</p>
</body>
</html>

