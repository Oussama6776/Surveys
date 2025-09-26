<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {to} {--subject=Test SurveyApp} {--body=Hello from SurveyApp}';
    protected $description = 'Send a quick test email using current mail configuration';

    public function handle()
    {
        $to = $this->argument('to');
        $subject = $this->option('subject');
        $body = $this->option('body');

        $this->info('Using mailer: ' . config('mail.default'));
        $this->line('Host: ' . config('mail.mailers.smtp.host'));
        $this->line('Port: ' . config('mail.mailers.smtp.port'));
        $this->line('Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->line('From: ' . config('mail.from.address'));

        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            $this->info('✅ Test email dispatched to ' . $to);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Failed to send: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

