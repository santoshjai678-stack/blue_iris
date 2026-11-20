@extends('layouts.superadmin')

@section('title', 'All Tenants')
@section('page-title', 'Tenant Companies Master')

@section('content')
    <!-- KPI Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">🏢</div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tenants</div>
                <div class="text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">✓</div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Tenants</div>
                <div class="text-2xl font-extrabold text-emerald-600">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">⏸</div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactive Tenants</div>
                <div class="text-2xl font-extrabold text-rose-600">{{ $stats['inactive'] }}</div>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
            <select id="filter_status" class="bg-white border border-slate-300 text-slate-700 text-sm rounded-lg px-3 py-2">
                <option value="">All Statuses</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <button type="button" id="btn_reset_filters" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 cursor-pointer">↺ Reset</button>
        </div>

        <a href="{{ route('superadmin.tenants.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors gap-2">
            <span>+</span> Create New Tenant
        </a>
    </div>

    <!-- DataTables Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table id="tenantsTable" class="w-full text-left" style="width: 100%;">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="pb-3" style="width: 25%;">Tenant / Company</th>
                        <th class="pb-3" style="width: 25%;">Domain / Subdomain</th>
                        <th class="pb-3" style="width: 20%;">Contact Info</th>
                        <th class="pb-3" style="width: 10%;">Status</th>
                        <th class="pb-3" style="width: 10%;">Created</th>
                        <th class="pb-3 text-right" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#tenantsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('superadmin.tenants.index') }}",
            data: function (d) {
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'tenant_info', name: 'name' },
            { data: 'domain_url', name: 'domains.domain', orderable: false },
            { data: 'contact', name: 'email' },
            { data: 'status_badge', name: 'status' },
            { data: 'created_date', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: #4f46e5;">⏳ Loading tenants...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search tenant name, email, ID...",
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