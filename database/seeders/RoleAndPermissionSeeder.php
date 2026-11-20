<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public static array $permissionsByModule = [
        'Stores / Salons' => [
            'salons.view' => 'View Store Directory & Details',
            'salons.create' => 'Create New Stores',
            'salons.edit' => 'Edit Store Information & Timings',
            'salons.delete' => 'Delete Stores',
        ],
        'Staff & Employees' => [
            'employees.view' => 'View Staff Directory & Profiles',
            'employees.create' => 'Add New Staff Members',
            'employees.edit' => 'Edit Staff & Schedules',
            'employees.delete' => 'Delete Staff Profiles',
        ],
        'Designations / Job Titles' => [
            'designations.view' => 'View Designations',
            'designations.create' => 'Create Designations',
            'designations.edit' => 'Edit Designations',
            'designations.delete' => 'Delete Designations',
        ],
        'Task Categories' => [
            'task_categories.view' => 'View Task Categories',
            'task_categories.create' => 'Create Task Categories',
            'task_categories.edit' => 'Edit Task Categories',
            'task_categories.delete' => 'Delete Task Categories',
        ],
        'Daily Survey Tasks' => [
            'tasks.view' => 'View Survey Tasks & Checklists',
            'tasks.create' => 'Create Survey Tasks',
            'tasks.edit' => 'Edit Survey Tasks & Options',
            'tasks.delete' => 'Delete Survey Tasks',
        ],
        'Attendance & Time Tracking' => [
            'attendance.view' => 'View Attendance Logs & Punches',
            'attendance.manage' => 'Manual Attendance Entry & Edits',
        ],
        'PTO & Leaves' => [
            'pto.view' => 'View Leave Requests & PTO Balances',
            'pto.manage' => 'Approve or Reject Leave Requests',
        ],
        'Reports & Analytics' => [
            'reports.view' => 'View Compliance & Operational Reports',
            'reports.export' => 'Export Reports to Excel/PDF',
        ],
        'System Users' => [
            'system_users.view' => 'View Tenant Portal Users',
            'system_users.create' => 'Add Tenant Portal Users',
            'system_users.edit' => 'Edit Portal Users & Status',
            'system_users.delete' => 'Delete Portal Users',
        ],
        'Roles & Permissions' => [
            'roles.view' => 'View Roles & Access Levels',
            'roles.create' => 'Create Custom Roles',
            'roles.edit' => 'Edit Role Permissions',
            'roles.delete' => 'Delete Roles',
        ],
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Seed all permissions
        foreach (self::$permissionsByModule as $module => $perms) {
            foreach ($perms as $permName => $label) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
            }
        }

        // 2. Seed Admin Role with full permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());

        // 3. Seed Store Manager Role
        $managerRole = Role::firstOrCreate([
            'name' => 'store_manager',
            'guard_name' => 'web',
        ]);
        $managerRole->syncPermissions([
            'salons.view',
            'employees.view',
            'employees.create',
            'employees.edit',
            'designations.view',
            'task_categories.view',
            'tasks.view',
            'attendance.view',
            'attendance.manage',
            'pto.view',
            'pto.manage',
            'reports.view',
        ]);

        // 4. Seed Supervisor Role
        $supervisorRole = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web',
        ]);
        $supervisorRole->syncPermissions([
            'salons.view',
            'employees.view',
            'tasks.view',
            'attendance.view',
            'reports.view',
        ]);

        // 5. Seed Auditor (Read-Only) Role
        $auditorRole = Role::firstOrCreate([
            'name' => 'auditor',
            'guard_name' => 'web',
        ]);
        $auditorRole->syncPermissions([
            'salons.view',
            'employees.view',
            'designations.view',
            'task_categories.view',
            'tasks.view',
            'attendance.view',
            'pto.view',
            'reports.view',
            'system_users.view',
            'roles.view',
        ]);

        // 6. Ensure initial tenant admin user has 'admin' role
        $tenantUser = User::first();
        if ($tenantUser && !$tenantUser->hasRole('admin')) {
            $tenantUser->assignRole('admin');
        }
    }
}