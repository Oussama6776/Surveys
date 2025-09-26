<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignRoleCommand extends Command
{
    protected $signature = 'user:assign-role {email} {role}';
    protected $description = 'Assign a role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        // Trouver l'utilisateur
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Utilisateur avec l'email '{$email}' non trouvé.");
            return 1;
        }

        // Trouver le rôle
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Rôle '{$roleName}' non trouvé.");
            $this->info("Rôles disponibles: " . Role::pluck('name')->implode(', '));
            return 1;
        }

        // Vérifier si l'utilisateur a déjà ce rôle
        if ($user->hasRole($roleName)) {
            $this->info("L'utilisateur '{$user->name}' a déjà le rôle '{$role->display_name}'.");
            return 0;
        }

        // Assigner le rôle
        $user->assignRole($role);
        $this->info("Rôle '{$role->display_name}' assigné avec succès à '{$user->name}'.");

        return 0;
    }
}
