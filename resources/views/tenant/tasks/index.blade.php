@extends('layouts.tenant')

@section('title', 'Daily Tasks & Surveys')
@section('page-title', 'Daily Tasks & Survey Questions Master')

@section('content')
    <!-- KPI Header Stats -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ede9fe; color: #6d28d9; display: flex; align-items: center; justify-content: center; font-size: 20px;">📋</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Tasks</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--text-main);">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">✓</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Checklist</div>
                <div style="font-size: 22px; font-weight: 800; color: #166534;">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏸</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Inactive / Paused</div>
                <div style="font-size: 22px; font-weight: 800; color: #991b1b;">{{ $stats['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters & Action Bar -->
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div class="filter-form-wrapper" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <select id="filter_category" class="form-input" style="width: 180px; padding: 10px 14px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->task_category_name }}</option>
                @endforeach
            </select>

            <select id="filter_period" class="form-input" style="width: 140px; padding: 10px 14px;">
                <option value="">All Periods</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->period }}</option>
                @endforeach
            </select>

            <select id="filter_status" class="form-input" style="width: 130px; padding: 10px 14px;">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <button type="button" id="btn_reset_filters" style="background: none; border: none; color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 4px;">↺ Reset</button>
        </div>

        <a href="{{ route('tenant.tasks.create') }}" class="btn-primary action-bar-btn" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none;">
            <span>+</span> Add New Task
        </a>
    </div>

    <!-- DataTables Card -->
    <div class="card">
        <div class="table-responsive">
            <table id="tasksTable" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 35%;">Question / Checklist Item</th>
                        <th style="width: 18%;">Category</th>
                        <th style="width: 12%;">Period</th>
                        <th style="width: 10%;">Input Type</th>
                        <th style="width: 7%;">Status</th>
                        <th style="text-align: right; width: 10%;">Actions</th>
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
    var table = $('#tasksTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('tenant.tasks.index') }}",
            data: function (d) {
                d.category_id = $('#filter_category').val();
                d.period_id = $('#filter_period').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'task_id', name: 'id' },
            { data: 'question_text', name: 'question' },
            { data: 'category_badge', name: 'task_categorys_id' },
            { data: 'period_badge', name: 'survey_periods_id' },
            { data: 'control_type_badge', name: 'control_type' },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: var(--primary);">⏳ Loading tasks...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search question or subtitle...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filter_category, #filter_period, #filter_status').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_category').val('');
        $('#filter_period').val('');
        $('#filter_status').val('');
        table.search('').draw();
    });
});
</script>
@endsection