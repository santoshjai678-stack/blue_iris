@extends('layouts.tenant')

@section('page-title', 'System User Profile: ' . $user->first_name . ' ' . $user->last_name)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $user->first_name }} {{ $user->last_name }}</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Portal user profile and granted module access permissions.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tenant.system-users.edit', $user) }}" style="background:#f59e0b; color:#78350f; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">✏ Edit User</a>
            <a href="{{ route('tenant.system-users.index') }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">← Directory</a>
        </div>
    </div>

    <!-- User Header Info -->
    <div style="background: linear-gradient(135deg, #1e1b4b, #312e81); color:#fff; padding:24px; border-radius:var(--radius); margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: #fff;">
                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: 800; margin: 0;">{{ $user->first_name }} {{ $user->last_name }}</h3>
                <div style="display: flex; gap: 12px; font-size: 13px; opacity: 0.9; margin-top: 4px;">
                    <span>✉ {{ $user->email }}</span>
                    @if($user->phone_no)
                        <span>📞 {{ $user->phone_no }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @php
                $role = $user->roles->first();
            @endphp
            <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); padding: 8px 16px; border-radius: 12px; text-align: right;">
                <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">Active Portal Role</div>
                <div style="font-size: 15px; font-weight: 800; margin-top: 2px;">🛡 {{ $role ? ucwords(str_replace('_', ' ', $role->name)) : 'None' }}</div>
            </div>
        </div>
    </div>

    <!-- Permission Matrix -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow);">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px;">
            Granted Module Permissions ({{ $role ? $role->permissions->count() : 0 }} active)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            @foreach($allPermissions as $moduleName => $perms)
                <div style="border: 1px solid var(--border); border-radius: 10px; padding: 14px; background: #f8fafc;">
                    <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 6px;">
                        {{ $moduleName }}
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @foreach($perms as $p)
                            @php
                                $hasPerm = $user->hasPermissionTo($p->name);
                            @endphp
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: {{ $hasPerm ? '#047857' : '#94a3b8' }};">
                                <span>{{ $hasPerm ? '✓' : '✕' }}</span>
                                <span style="font-weight: {{ $hasPerm ? '700' : '400' }};">{{ $p->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection