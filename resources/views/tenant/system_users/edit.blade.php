@extends('layouts.tenant')

@section('page-title', 'Edit System Portal User: ' . $user->first_name . ' ' . $user->last_name)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Edit System User: {{ $user->first_name }} {{ $user->last_name }}</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Update portal login details, role permissions, or reset password.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tenant.system-users.show', $user) }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">View Permissions</a>
            <a href="{{ route('tenant.system-users.index') }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">Directory</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.system-users.update', $user) }}">
        @csrf
        @method('PUT')

        @php
            $currentRole = old('role', $user->roles->first()?->name ?? '');
        @endphp

        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">First Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Last Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Email Address (Login ID) <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Phone Number</label>
                    <input type="text" name="phone_no" value="{{ old('phone_no', $user->phone_no) }}" placeholder="(555) 000-0000" style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Portal Role & Permissions <span style="color:#dc2626;">*</span></label>
                    <select name="role" required style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; background:#fff;">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $currentRole == $role->name ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role->name)) }} ({{ $role->permissions->count() }} permissions)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Reset Password (Optional)</label>
                    <input type="text" name="password" placeholder="Leave blank to keep existing password" style="width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 18px;">
                <a href="{{ route('tenant.system-users.index') }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">Cancel</a>
                <button type="submit" style="background: linear-gradient(135deg, #d97706, #b45309); color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer;">
                    Update User
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
