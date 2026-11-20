@extends('layouts.tenant')

@section('title', 'Security Roles & Permissions')
@section('page-title', 'Security Roles & Module Permissions')

@section('content')
<div>
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Security Roles & Access Levels</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Configure modular permissions and assign security roles to tenant portal administrators and managers.</p>
        </div>
        <div class="filter-form-wrapper" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('tenant.system-users.index') }}" class="btn-action action-bar-btn" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">
                👤 System Users
            </a>
            <a href="{{ route('tenant.roles.create') }}" class="btn-primary action-bar-btn" style="padding:10px 20px; border-radius:10px; text-decoration:none; font-weight:700; font-size:14px;">
                + Add New Role
            </a>
        </div>
    </div>

    <!-- Roles Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
        @foreach($roles as $role)
            <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px;">
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; word-break: break-word;">
                            🛡️ {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </h3>
                        <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #3730a3; padding: 3px 10px; border-radius: 12px; flex-shrink: 0;">
                            {{ $role->users->count() }} Users
                        </span>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">
                            <span>Granted Permissions</span>
                            <span>{{ $role->permissions->count() }} / {{ $totalPermissions }}</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $totalPermissions > 0 ? ($role->permissions->count() / $totalPermissions) * 100 : 0 }}%; height: 100%; background: linear-gradient(90deg, #4f46e5, #06b6d4);"></div>
                        </div>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 16px;">
                        @foreach($role->permissions->take(4) as $perm)
                            <span style="font-size: 10px; font-weight: 600; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 4px;">
                                {{ $perm->name }}
                            </span>
                        @endforeach
                        @if($role->permissions->count() > 4)
                            <span style="font-size: 10px; font-weight: 700; color: #6366f1; padding: 2px 4px;">
                                +{{ $role->permissions->count() - 4 }} more
                            </span>
                        @endif
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 12px;">
                    <a href="{{ route('tenant.roles.show', $role) }}" style="font-size: 12.5px; font-weight: 700; color: #4338ca; text-decoration: none;">View Matrix →</a>
                    <div style="display: flex; gap: 6px;">
                        <a href="{{ route('tenant.roles.edit', $role) }}" style="background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">Edit</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection