@extends('layouts.tenant')

@section('page-title', 'Role: ' . $role->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">🛡 Role: {{ ucwords(str_replace('_', ' ', $role->name)) }}</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Permissions matrix and assigned portal users.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tenant.roles.edit', $role) }}" style="background:#f59e0b; color:#78350f; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">✏ Edit Permissions</a>
            <a href="{{ route('tenant.roles.index') }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">← All Roles</a>
        </div>
    </div>

    <!-- Summary Box -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Assigned Portal Users</div>
            <div style="font-size: 20px; font-weight: 800; color: var(--text-main); margin-top: 2px;">{{ $role->users->count() }} Users</div>
        </div>
        <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Permissions</div>
            <div style="font-size: 20px; font-weight: 800; color: #4338ca; margin-top: 2px;">{{ $role->permissions->count() }} Granted</div>
        </div>
    </div>

    <!-- Permissions Grid -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px;">Detailed Permission Matrix</h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            @foreach($permissionsByModule as $module => $perms)
                <div style="border: 1px solid var(--border); border-radius: 10px; padding: 16px; background: #f8fafc;">
                    <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 12px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">{{ $module }}</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($perms as $permKey => $label)
                            @php
                                $isGranted = $role->hasPermissionTo($permKey);
                            @endphp
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: {{ $isGranted ? '#047857' : '#94a3b8' }};">
                                <span style="font-weight: 800;">{{ $isGranted ? '✓' : '✕' }}</span>
                                <span style="font-weight: {{ $isGranted ? '700' : '400' }};">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection