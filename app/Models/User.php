<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return $this->roles->contains($role);
    }

    public function hasAnyRole($roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission($permissions)
    {
        foreach ($this->roles as $role) {
            if ($role->hasAnyPermission($permissions)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role && !$this->hasRole($role)) {
            $this->roles()->attach($role);
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role);
        }

        return $this;
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function hasAllPermissions($permissions)
    {
        foreach ($this->roles as $role) {
            if ($role->hasAllPermissions($permissions)) {
                return true;
            }
        }
        return false;
    }

    public function canAccessSurvey($survey)
    {
        // Super admin et admin : accès global
        if ($this->hasRole('super_admin') || $this->hasRole('admin')) {
            return true;
        }

        // Le propriétaire a toujours accès à son sondage
        if ($survey->user_id === $this->id) {
            return true;
        }

        // Les autres peuvent voir les enquêtes publiques
        if ($survey->is_public) {
            return true;
        }

        return false;
    }

    public function canModifySurvey($survey)
    {
        // Super admin ou admin : modification globale
        if ($this->hasRole('super_admin') || $this->hasRole('admin')) {
            return true;
        }

        // Le propriétaire peut modifier son propre sondage
        return $survey->user_id === $this->id;
    }
}
