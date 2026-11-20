@extends('layouts.tenant')

@section('page-title', 'Create Security Role')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Create New Security Role</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Define role title and select specific module permissions.</p>
        </div>
        <a href="{{ route('tenant.roles.index') }}" style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px;">← Back to Roles</a>
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

    <form method="POST" action="{{ route('tenant.roles.store') }}">
        @csrf

        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px;">
            <div style="max-width: 400px;">
                <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Role Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Area Supervisor, Auditor" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px;">
            </div>
        </div>

        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0;">Modular Permissions Matrix <span style="color:#dc2626;">*</span></h3>
                <button type="button" onclick="selectAll(true)" style="background: #e0e7ff; color: #4338ca; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; margin-right: 6px;">Select All</button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                @foreach($permissionsByModule as $module => $perms)
                    <div style="border: 1px solid var(--border); border-radius: 10px; padding: 16px; background: #f8fafc;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">
                            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">{{ $module }}</h4>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @foreach($perms as $permKey => $label)
                                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; user-select: none;">
                                    <input type="checkbox" name="permissions[]" value="{{ $permKey }}" {{ is_array(old('permissions')) && in_array($permKey, old('permissions')) ? 'checked' : '' }} class="perm-cb" style="margin-top: 3px; accent-color: var(--primary);">
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">{{ $label }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">{{ $permKey }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('tenant.roles.index') }}" style="background:#f1f5f9; color:#475569; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px;">Cancel</a>
            <button type="submit" style="background: linear-gradient(135deg, #4f46e5, #4338ca); color:#fff; border:none; padding:12px 28px; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
                Save Role
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
function selectAll(state) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = state);
}
</script>
@endsection
@endsection