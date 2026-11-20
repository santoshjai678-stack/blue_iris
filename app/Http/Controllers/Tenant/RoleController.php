<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreRoleRequest;
use App\Http\Requests\Tenant\UpdateRoleRequest;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of system roles.
     */
    public function index(): View
    {
        $roles = Role::with(['permissions', 'users'])->where('guard_name', 'web')->get();
        $totalPermissions = Permission::where('guard_name', 'web')->count();

        return view('tenant.roles.index', compact('roles', 'totalPermissions'));
    }

    /**
     * Show form for creating a new role.
     */
    public function create(): View
    {
        $permissionsByModule = RoleAndPermissionSeeder::$permissionsByModule;
        return view('tenant.roles.create', compact('permissionsByModule'));
    }

    /**
     * Store newly created role.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => strtolower(trim(str_replace(' ', '_', $validated['name']))),
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', "Role '{$role->name}' created successfully with " . count($validated['permissions']) . ' permissions.');
    }

    /**
     * Display specified role details and permission matrix.
     */
    public function show(Role $role): View
    {
        $role->load(['permissions', 'users']);
        $permissionsByModule = RoleAndPermissionSeeder::$permissionsByModule;

        return view('tenant.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show form for editing role.
     */
    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissionsByModule = RoleAndPermissionSeeder::$permissionsByModule;
        $assignedPermissions = $role->permissions->pluck('name')->toArray();

        return view('tenant.roles.edit', compact('role', 'permissionsByModule', 'assignedPermissions'));
    }

    /**
     * Update specified role and its permissions.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        // Prevent renaming core admin role
        if ($role->name === 'admin' && $validated['name'] !== 'admin') {
            return redirect()
                ->back()
                ->with('error', 'The core admin role name cannot be modified.');
        }

        if ($role->name !== 'admin') {
            $role->update([
                'name' => strtolower(trim(str_replace(' ', '_', $validated['name']))),
            ]);
        }

        $role->syncPermissions($validated['permissions']);

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'store_manager'])) {
            return redirect()
                ->route('tenant.roles.index')
                ->with('error', "Core system role '{$role->name}' cannot be deleted.");
        }

        if ($role->users()->count() > 0) {
            return redirect()
                ->route('tenant.roles.index')
                ->with('error', "Cannot delete role '{$role->name}' because {$role->users()->count()} user(s) are assigned to it.");
        }

        $name = $role->name;
        $role->syncPermissions([]);
        $role->delete();

        return redirect()
            ->route('tenant.roles.index')
            ->with('success', "Role '{$name}' deleted successfully.");
    }
}