<?php

namespace App\Mail;

use App\Models\Survey;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Survey $survey;
    public Contact $contact;
    public ?string $plainPassword;
    public ?string $inviteToken;

    public function __construct(Survey $survey, Contact $contact, ?string $plainPassword = null, ?string $inviteToken = null)
    {
        $this->survey = $survey;
        $this->contact = $contact;
        $this->plainPassword = $plainPassword;
        $this->inviteToken = $inviteToken;
    }

    public function build()
    {
        $loginUrl = route('contacts.login.form', $this->survey);
        $surveyUrl = route('surveys.respond', $this->survey);
        if ($this->inviteToken) {
            // Signature HMAC liée au sondage pour éviter une réutilisation croisée
            $sig = hash_hmac('sha256', $this->inviteToken . '|' . $this->survey->id, config('app.key'));
            $inviteUrl = route('surveys.invite.access', $this->inviteToken) . '?sid=' . $this->survey->id . '&sig=' . $sig;
        } else {
            $inviteUrl = $surveyUrl;
        }

        return $this->subject('Invitation à répondre au sondage: ' . $this->survey->title)
            ->view('emails.contacts.invite')
            ->with([
                'survey' => $this->survey,
                'contact' => $this->contact,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $loginUrl,
                'surveyUrl' => $surveyUrl,
                'inviteUrl' => $inviteUrl,
            ]);
    }
}
