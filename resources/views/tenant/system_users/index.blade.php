@extends('layouts.tenant')

@section('title', 'System Users')
@section('page-title', 'System Portal Users')

@section('content')
    <!-- KPI Header Stats -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ede9fe; color: #6d28d9; display: flex; align-items: center; justify-content: center; font-size: 20px;">🛡️</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Portal Users</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--text-main);">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">✓</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Users</div>
                <div style="font-size: 22px; font-weight: 800; color: #166534;">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏸</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Inactive Users</div>
                <div style="font-size: 22px; font-weight: 800; color: #991b1b;">{{ $stats['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Action Bar -->
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div class="filter-form-wrapper" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <select id="filter_role" class="form-input" style="width: 170px; padding: 10px 14px;">
                <option value="">All Security Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>

            <select id="filter_status" class="form-input" style="width: 140px; padding: 10px 14px;">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <button type="button" id="btn_reset_filters" style="background: none; border: none; color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 4px;">↺ Reset</button>
        </div>

        <div class="filter-form-wrapper" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('tenant.roles.index') }}" class="btn-action action-bar-btn" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; background: #ede9fe; color: #6d28d9;">
                🔑 Manage Security Roles
            </a>
            <a href="{{ route('tenant.system-users.create') }}" class="btn-primary action-bar-btn" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none;">
                <span>+</span> Add Portal User
            </a>
        </div>
    </div>

    <!-- DataTables Card -->
    <div class="card">
        <div class="table-responsive">
            <table id="usersTable" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 32%;">User Account</th>
                        <th style="width: 20%;">Phone Number</th>
                        <th style="width: 25%;">Assigned Roles</th>
                        <th style="width: 10%;">Status</th>
                        <th style="text-align: right; width: 13%;">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('tenant.system-users.index') }}",
            data: function (d) {
                d.role = $('#filter_role').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'user_info', name: 'first_name' },
            { data: 'phone_no', name: 'phone_no' },
            { data: 'roles_list', name: 'roles.name', orderable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: var(--primary);">⏳ Loading users...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search user name, email, phone...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filter_role, #filter_status').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_role').val('');
        $('#filter_status').val('');
        table.search('').draw();
    });
});
</script>
@endsection