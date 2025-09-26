<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function hasPermission($permission)
    {
        if (in_array('*', $this->permissions)) {
            return true;
        }

        return in_array($permission, $this->permissions);
    }

    public function hasAnyPermission($permissions)
    {
        if (in_array('*', $this->permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions($permissions)
    {
        if (in_array('*', $this->permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!in_array($permission, $this->permissions)) {
                return false;
            }
        }

        return true;
    }
}