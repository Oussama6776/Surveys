<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
    protected $signature = 'user:reset-password {email} {password}';
    protected $description = 'Reset a user password';

    public function handle()
    {
        $email = $this->argument('email');
        $newPassword = $this->argument('password');

        // Trouver l'utilisateur
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Utilisateur avec l'email '{$email}' non trouvé.");
            return 1;
        }

        // Valider le mot de passe
        if (strlen($newPassword) < 8) {
            $this->error("Le mot de passe doit contenir au moins 8 caractères.");
            return 1;
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        $this->info("Mot de passe réinitialisé avec succès pour '{$user->name}' ({$user->email}).");
        $this->line("Nouveau mot de passe: {$newPassword}");
        $this->warn("⚠️  Veuillez noter ce mot de passe et le communiquer de manière sécurisée à l'utilisateur.");

        return 0;
    }
}
