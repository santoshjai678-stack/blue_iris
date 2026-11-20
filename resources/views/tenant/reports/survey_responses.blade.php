@extends('layouts.tenant')

@section('title', 'Survey Responses Audit Log')
@section('page-title', 'Detailed Survey Responses & Audit Log')

@section('content')
    <div class="filter-toolbar-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 13.5px; color: var(--text-muted);">Real-time granular audit of all store checklist submissions and staff surveys.</div>
        </div>
        <div class="filter-form-wrapper" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('tenant.reports.export-compliance') }}" class="btn-primary action-bar-btn" style="background: #16a34a; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                📥 Export CSV Audit
            </a>
            <a href="{{ route('tenant.reports.index') }}" class="btn-action action-bar-btn" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; background: #f1f5f9; color: #475569;">
                ← Back to Overview
            </a>
        </div>
    </div>

    <!-- Dynamic Filter Bar -->
    <div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="filter-form-wrapper" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div style="display: flex; flex-direction: column; gap: 4px; flex: 1 1 180px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Store</label>
                <select id="filter_salon" class="form-input" style="padding: 8px 12px;">
                    <option value="">All Stores</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}">{{ $salon->salon_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px; flex: 1 1 180px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Category</label>
                <select id="filter_category" class="form-input" style="padding: 8px 12px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->task_category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px; flex: 1 1 140px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Start Date</label>
                <input type="date" id="filter_start_date" value="{{ $startDate }}" class="form-input" style="padding: 8px 12px;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px; flex: 1 1 140px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">End Date</label>
                <input type="date" id="filter_end_date" value="{{ $endDate }}" class="form-input" style="padding: 8px 12px;">
            </div>

            <div style="display: flex; align-items: center; gap: 8px; flex: 1 1 180px;">
                <button type="button" id="btn_apply_filters" class="btn-primary" style="padding: 9px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; flex: 1;">Apply</button>
                <button type="button" id="btn_reset_filters" style="background: none; border: none; color: var(--text-muted); font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 4px;">↺ Reset</button>
            </div>
        </div>
    </div>

    <!-- DataTables Card -->
    <div class="card">
        <div class="table-responsive">
            <table id="surveyResponsesTable" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date & Time</th>
                        <th style="width: 18%;">Store / Salon</th>
                        <th style="width: 15%;">Auditor / Staff</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 25%;">Survey Task Question</th>
                        <th style="width: 12%;">Response</th>
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
    var table = $('#surveyResponsesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('tenant.reports.survey-responses') }}",
            data: function (d) {
                d.salon_id = $('#filter_salon').val();
                d.category_id = $('#filter_category').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
            }
        },
        columns: [
            { data: 'date_slot', name: 'ans_date' },
            { data: 'salon_name', name: 'salon.salon_name' },
            { data: 'auditor', name: 'user.first_name' },
            { data: 'category_badge', name: 'question.category.task_category_name' },
            { data: 'question_text', name: 'question.question' },
            { data: 'answer_badge', name: 'ans_text' }
        ],
        order: [[0, 'desc']],
        language: {
            processing: '<div style="padding: 8px 16px; font-weight: 600; color: var(--primary);">⏳ Loading audit records...</div>',
            search: "_INPUT_",
            searchPlaceholder: "🔍 Search question, response, store...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#btn_apply_filters').on('click', function() {
        table.draw();
    });

    $('#filter_salon, #filter_category').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_salon').val('');
        $('#filter_category').val('');
        $('#filter_start_date').val('{{ $startDate }}');
        $('#filter_end_date').val('{{ $endDate }}');
        table.search('').draw();
    });
});
</script>
@endsection