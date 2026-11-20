<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSystemUserRequest;
use App\Http\Requests\Tenant\UpdateSystemUserRequest;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class SystemUserController extends Controller
{
    /**
     * Display a listing of system portal users.
     * Excludes the primary Tenant Administrator account (tenant owner).
     */
    public function index(Request $request)
    {
        $tenantEmail = tenant('email');

        if ($request->ajax() || $request->wantsJson()) {
            $query = User::with('roles')->select('users.*');

            // Exclude root tenant owner/admin account
            if ($tenantEmail) {
                $query->where('email', '!=', $tenantEmail);
            }

            if ($request->filled('role')) {
                $roleFilter = $request->get('role');
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('roles.name', $roleFilter);
                });
            }

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('user_info', function ($user) {
                    $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
                    $avatar = '<div style="width: 36px; height: 36px; border-radius: 8px; background: #ede9fe; color: #6d28d9; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; margin-right: 12px;">' . e($initials) . '</div>';
                    $name = '<div style="display: inline-block; vertical-align: middle;">'
                        . '<div style="font-weight: 600; color: var(--text-main);">' . e($user->first_name . ' ' . $user->last_name) . '</div>'
                        . '<div style="font-size: 12px; color: var(--text-muted);">' . e($user->email) . '</div>'
                        . '</div>';
                    return '<div style="display: flex; align-items: center;">' . $avatar . $name . '</div>';
                })
                ->addColumn('phone_no', function ($user) {
                    return $user->phone_no ? '📞 ' . e($user->phone_no) : '<span style="color: var(--text-muted);">-</span>';
                })
                ->addColumn('roles_list', function ($user) {
                    if ($user->roles->isEmpty()) {
                        return '<span class="badge" style="background:#f1f5f9; color:#64748b;">No Role</span>';
                    }
                    $html = '<div style="display: flex; gap: 4px; flex-wrap: wrap;">';
                    foreach ($user->roles as $role) {
                        $bg = $role->name === 'admin' ? '#fee2e2' : '#e0e7ff';
                        $color = $role->name === 'admin' ? '#991b1b' : '#3730a3';
                        $html .= '<span class="badge" style="background:' . $bg . '; color:' . $color . '; font-weight:600; text-transform:capitalize;">' . e($role->name) . '</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status_badge', function ($user) {
                    return $user->status == 1
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($user) {
                    $showUrl = route('tenant.system-users.show', $user->id);
                    $editUrl = route('tenant.system-users.edit', $user->id);
                    $deleteUrl = route('tenant.system-users.destroy', $user->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $isSelf = Auth::id() === $user->id;
                    $deleteBtn = $isSelf
                        ? '<button type="button" class="btn-action" disabled title="Cannot delete self" style="color: #cbd5e1; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: not-allowed;">Delete</button>'
                        : '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this portal user?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="btn-action" title="Delete" style="color: #ef4444; background: #fee2e2; padding: 6px 10px; border-radius: 6px; border: none; font-size: 12px; font-weight: 600; cursor: pointer;">Delete</button>'
                        . '</form>';

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $showUrl . '" class="btn-action" title="View Permissions" style="color: #4f46e5; background: #eef2ff; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">View</a>'
                        . '<a href="' . $editUrl . '" class="btn-action" title="Edit User" style="color: #0284c7; background: #e0f2fe; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Edit</a>'
                        . $deleteBtn
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone_no', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['user_info', 'phone_no', 'roles_list', 'status_badge', 'actions'])
                ->make(true);
        }

        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        
        $baseQuery = User::query();
        if ($tenantEmail) {
            $baseQuery->where('email', '!=', $tenantEmail);
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 1)->count(),
            'inactive' => (clone $baseQuery)->where('status', 0)->count(),
        ];

        return view('tenant.system_users.index', compact('roles', 'stats'));
    }

    /**
     * Show form for creating a new system user.
     */
    public function create(): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        return view('tenant.system_users.create', compact('roles'));
    }

    /**
     * Store a newly created system user.
     */
    public function store(StoreSystemUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $roleInput = $validated['role'] ?? ($validated['roles'] ?? null);
        $roleName = is_array($roleInput) ? ($roleInput[0] ?? null) : $roleInput;
        $roleId = null;
        if ($roleName) {
            $roleObj = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            $roleId = $roleObj?->id;
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_no' => $validated['phone_no'] ?? null,
            'password' => Hash::make($validated['password']),
            'roles_id' => $roleId,
            'status' => $validated['status'] ?? 1,
            'email_verified_at' => now(),
        ]);

        if ($roleName) {
            $user->syncRoles([$roleName]);
        }

        return redirect()->route('tenant.system-users.index')
            ->with('success', "System user {$user->first_name} created successfully.");
    }

    /**
     * Display the specified system user and their permissions.
     */
    public function show(User $systemUser): View|RedirectResponse
    {
        if ($systemUser->email === tenant('email')) {
            return redirect()->route('tenant.system-users.index')
                ->with('error', 'The primary Tenant Administrator account cannot be managed from this section.');
        }

        $systemUser->load('roles.permissions');
        $allPermissions = [];
        foreach (RoleAndPermissionSeeder::$permissionsByModule as $module => $perms) {
            $permObjects = [];
            foreach ($perms as $permName => $label) {
                $permObjects[] = (object)['name' => $permName, 'label' => $label];
            }
            $allPermissions[$module] = $permObjects;
        }

        return view('tenant.system_users.show', [
            'user' => $systemUser,
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Show form for editing system user.
     */
    public function edit(User $systemUser): View|RedirectResponse
    {
        if ($systemUser->email === tenant('email')) {
            return redirect()->route('tenant.system-users.index')
                ->with('error', 'The primary Tenant Administrator account cannot be edited from this section.');
        }

        $systemUser->load('roles');
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        return view('tenant.system_users.edit', [
            'user' => $systemUser,
            'roles' => $roles,
        ]);
    }

    /**
     * Update existing system user.
     */
    public function update(UpdateSystemUserRequest $request, User $systemUser): RedirectResponse
    {
        if ($systemUser->email === tenant('email')) {
            return redirect()->route('tenant.system-users.index')
                ->with('error', 'The primary Tenant Administrator account cannot be updated from this section.');
        }

        $validated = $request->validated();

        $roleInput = $validated['role'] ?? ($validated['roles'] ?? null);
        $roleName = is_array($roleInput) ? ($roleInput[0] ?? null) : $roleInput;
        $roleId = null;
        if ($roleName) {
            $roleObj = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            $roleId = $roleObj?->id;
        }

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_no' => $validated['phone_no'] ?? null,
            'status' => $validated['status'] ?? 1,
        ];

        if ($roleId !== null) {
            $updateData['roles_id'] = $roleId;
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $systemUser->update($updateData);

        if ($roleName) {
            $systemUser->syncRoles([$roleName]);
        }

        return redirect()->route('tenant.system-users.index')
            ->with('success', "System user {$systemUser->first_name} updated successfully.");
    }

    /**
     * Delete system user.
     */
    public function destroy(User $systemUser): RedirectResponse
    {
        if ($systemUser->email === tenant('email')) {
            return back()->with('error', 'The primary Tenant Administrator account cannot be deleted.');
        }

        if (Auth::id() === $systemUser->id) {
            return back()->with('error', 'You cannot delete your own active account.');
        }

        $systemUser->delete();
        return redirect()->route('tenant.system-users.index')
            ->with('success', 'System user removed successfully.');
    }
}
