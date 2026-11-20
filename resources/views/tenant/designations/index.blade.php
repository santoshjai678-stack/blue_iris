@extends('layouts.tenant')

@section('title', 'Designations Master')
@section('page-title', 'Staff Designations Master')

@section('content')
    <!-- KPI Header Stats -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px;">💼</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Designations</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--text-main);">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">✓</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Designations</div>
                <div style="font-size: 22px; font-weight: 800; color: #166534;">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏸</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Inactive Designations</div>
                <div style="font-size: 22px; font-weight: 800; color: #991b1b;">{{ $stats['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Controls -->
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div class="filter-form-wrapper" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <select id="filter_status" class="form-input" style="width: 140px; padding: 10px 14px;">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <button type="button" id="btn_reset_filters" style="background: none; border: none; color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 4px;">↺ Reset</button>
        </div>

        <a href="{{ route('tenant.designations.create') }}" class="btn-primary action-bar-btn" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none;">
            <span>+</span> Add Designation
        </a>
    </div>

    <!-- DataTables Card -->
    <div class="card">
        <div class="table-responsive">
            <table id="designationsTable" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40%;">Designation & Description</th>
                        <th style="width: 20%;">Assigned Staff</th>
                        <th style="width: 15%;">Survey Tasks</th>
                        <th style="width: 10%;">Status</th>
                        <th style="text-align: right; width: 15%;">Actions</th>
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
    var table = $('#designationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('tenant.designations.index') }}",
            data: function (d) {
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'designation_info', name: 'designation' },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'tasks_count', name: 'questions_count', searchable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: var(--primary);">⏳ Loading designations...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search designation name...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filter_status').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_status').val('');
        table.search('').draw();
    });
});
</script>
@endsection