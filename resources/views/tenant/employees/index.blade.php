@extends('layouts.tenant')

@section('title', 'Salon Employees')
@section('page-title', 'Salon Staff & Employee Directory')

@section('content')
    <!-- KPI Header Stats -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px;">👥</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Employees</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--text-main);">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">✓</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Roster</div>
                <div style="font-size: 22px; font-weight: 800; color: #166534;">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏸</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Inactive / On Hold</div>
                <div style="font-size: 22px; font-weight: 800; color: #991b1b;">{{ $stats['inactive'] }}</div>
            </div>
        </div>
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 0;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 20px;">⏹</div>
            <div>
                <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Terminated</div>
                <div style="font-size: 22px; font-weight: 800; color: #64748b;">{{ $stats['terminated'] }}</div>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Controls -->
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div class="filter-form-wrapper" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <select id="filter_salon" class="form-input" style="width: 190px; padding: 10px 14px;">
                <option value="">All Salons / Stores</option>
                @foreach($salons as $salon)
                    <option value="{{ $salon->id }}">{{ $salon->salon_name }}</option>
                @endforeach
            </select>

            <select id="filter_status" class="form-input" style="width: 140px; padding: 10px 14px;">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
                <option value="2">Terminated</option>
            </select>

            <button type="button" id="btn_reset_filters" style="background: none; border: none; color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 4px;">↺ Reset</button>
        </div>

        <a href="{{ route('tenant.employees.create') }}" class="btn-primary action-bar-btn" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none;">
            <span>+</span> Onboard Employee
        </a>
    </div>

    <!-- DataTables Server-Side Card -->
    <div class="card">
        <div class="table-responsive">
            <table id="employeesTable" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 25%;">Employee Name</th>
                        <th style="width: 18%;">Contact & City</th>
                        <th style="width: 20%;">Assigned Store</th>
                        <th style="width: 15%;">Shift Time</th>
                        <th style="width: 10%;">Status</th>
                        <th style="text-align: right; width: 12%;">Actions</th>
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
    var table = $('#employeesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('tenant.employees.index') }}",
            data: function (d) {
                d.salon_id = $('#filter_salon').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'employee_info', name: 'first_name' },
            { data: 'contact', name: 'phone_no' },
            { data: 'salon', name: 'salon', orderable: false },
            { data: 'shift', name: 'shift', orderable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: var(--primary);">⏳ Loading employee data...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search employee name, email, phone...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filter_salon, #filter_status').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_salon').val('');
        $('#filter_status').val('');
        table.search('').draw();
    });
});
</script>
@endsection