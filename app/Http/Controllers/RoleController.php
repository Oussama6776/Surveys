<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasPermission('users.manage')) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->getAvailablePermissions();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAvailablePermissions())),
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $validated['permissions'],
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('users');
        $permissions = $this->getAvailablePermissions();
        return view('roles.show', compact('role', 'permissions'));
    }

    public function edit(Role $role)
    {
        $permissions = $this->getAvailablePermissions();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAvailablePermissions())),
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'permissions' => $validated['permissions'],
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['super_admin', 'admin'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system roles.');
        }

        // Check if role is assigned to users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function assignUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);
        
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
            
            return redirect()->route('roles.show', $role)
                ->with('success', 'Role assigned to user successfully.');
        }

        return redirect()->route('roles.show', $role)
            ->with('error', 'User already has this role.');
    }

    public function removeUser(Role $role, User $user)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            
            return redirect()->route('roles.show', $role)
                ->with('success', 'Role removed from user successfully.');
        }

        return redirect()->route('roles.show', $role)
            ->with('error', 'User does not have this role.');
    }

    private function getAvailablePermissions()
    {
        return [
            // Survey permissions
            'surveys.create' => 'Create Surveys',
            'surveys.read' => 'View Surveys',
            'surveys.update' => 'Edit Surveys',
            'surveys.delete' => 'Delete Surveys',
            'surveys.publish' => 'Publish Surveys',
            
            // Question permissions
            'questions.create' => 'Create Questions',
            'questions.read' => 'View Questions',
            'questions.update' => 'Edit Questions',
            'questions.delete' => 'Delete Questions',
            
            // Response permissions
            'responses.read' => 'View Responses',
            'responses.delete' => 'Delete Responses',
            'responses.export' => 'Export Responses',
            
            // User permissions
            'users.create' => 'Create Users',
            'users.read' => 'View Users',
            'users.update' => 'Edit Users',
            'users.delete' => 'Delete Users',
            'users.manage' => 'Manage User Roles',
            
            // Analytics permissions
            'analytics.read' => 'View Analytics',
            'analytics.export' => 'Export Analytics',
            
            // Theme permissions
            'themes.create' => 'Create Themes',
            'themes.read' => 'View Themes',
            'themes.update' => 'Edit Themes',
            'themes.delete' => 'Delete Themes',
            
            // System permissions
            'system.settings' => 'Manage System Settings',
            'system.backup' => 'Backup System',
            'system.logs' => 'View System Logs',
        ];
    }
}