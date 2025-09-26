<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Assign default roles to existing users
        $users = User::all();
        $defaultRole = Role::where('name', 'survey_creator')->first();
        
        if ($defaultRole) {
            foreach ($users as $user) {
                if (!$user->hasAnyRole(['super_admin', 'admin'])) {
                    $user->assignRole($defaultRole);
                }
            }
        }

        // Create a super admin user if none exists
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole && !User::whereHas('roles', function($query) {
            $query->where('name', 'super_admin');
        })->exists()) {
            
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@surveytool.com',
                'password' => bcrypt('password'),
            ]);
            
            $superAdmin->assignRole($superAdminRole);
        }
    }
}