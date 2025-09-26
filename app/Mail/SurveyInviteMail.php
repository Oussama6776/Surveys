<?php

namespace App\Mail;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Survey $survey;
    public User $user;
    public ?string $plainPassword;

    public function __construct(Survey $survey, User $user, ?string $plainPassword = null)
    {
        $this->survey = $survey;
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        $loginUrl = route('login');
        // Lien direct vers la page de réponse; l'utilisateur non connecté sera redirigé vers le login puis renvoyé ici
        $surveyUrl = route('surveys.respond', $this->survey);

        return $this->subject('Invitation à répondre au sondage: ' . $this->survey->title)
            ->view('emails.surveys.invite')
            ->with([
                'survey' => $this->survey,
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $loginUrl,
                'surveyUrl' => $surveyUrl,
            ]);
    }
}
