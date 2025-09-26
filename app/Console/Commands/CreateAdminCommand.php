<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'user:create-admin {name} {email} {password}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Vérifier si l'utilisateur existe déjà
        if (User::where('email', $email)->exists()) {
            $this->error("Un utilisateur avec l'email '{$email}' existe déjà.");
            return 1;
        }

        // Valider le mot de passe
        if (strlen($password) < 8) {
            $this->error("Le mot de passe doit contenir au moins 8 caractères.");
            return 1;
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Assigner le rôle admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole);
            $this->info("Utilisateur admin créé avec succès !");
            $this->line("📧 Email: {$email}");
            $this->line("🔑 Mot de passe: {$password}");
            $this->line("🛡️  Rôle: Administrateur");
        } else {
            $this->error("Rôle 'admin' non trouvé. Veuillez exécuter les seeders.");
            return 1;
        }

        return 0;
    }
}
