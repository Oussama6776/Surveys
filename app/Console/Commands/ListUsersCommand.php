<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsersCommand extends Command
{
    protected $signature = 'user:list';
    protected $description = 'List all users and their roles';

    public function handle()
    {
        $users = User::with('roles')->get();

        if ($users->isEmpty()) {
            $this->info('Aucun utilisateur trouvé.');
            return 0;
        }

        $this->info('Utilisateurs et leurs rôles:');
        $this->line('');

        foreach ($users as $user) {
            $roles = $user->roles->pluck('display_name')->implode(', ');
            $roles = $roles ?: 'Aucun rôle';
            
            $this->line("📧 {$user->email}");
            $this->line("👤 {$user->name}");
            $this->line("🛡️  Rôles: {$roles}");
            $this->line("📅 Créé: {$user->created_at->format('d/m/Y H:i')}");
            $this->line('─' . str_repeat('─', 50));
        }

        return 0;
    }
}
