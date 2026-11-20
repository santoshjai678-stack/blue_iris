@extends('layouts.tenant')

@section('title', 'Store Task Compliance Matrix')
@section('page-title', 'Store Task Compliance Matrix')

@section('styles')
<style>
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
    }
    .filter-grid {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1 1 140px;
    }
    .filter-group label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #fff;
        height: 38px;
        outline: none;
    }
    .filter-control:focus {
        border-color: var(--primary);
    }
    .cell-pill {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        min-width: 50px;
        line-height: 1.2;
    }
    .cell-100 { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .cell-partial { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .cell-zero { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .btn-export {
        background: #047857;
        color: #fff;
        padding: 9px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 38px;
    }
    .btn-export:hover { background: #065f46; }
</style>
@endsection

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">
                <a href="{{ route('tenant.reports.index') }}" style="color: var(--primary); text-decoration: none;">Reports</a>
                <span>/</span>
                <span>Task Compliance</span>
            </div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0;">Store Task Compliance Report</h2>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tenant.reports.export-compliance', request()->all()) }}" class="btn-export">
                📥 Export CSV
            </a>
            <a href="{{ route('tenant.reports.index') }}" style="background:#f1f5f9; color:#475569; padding:9px 16px; border-radius:8px; text-decoration:none; font-weight:700; font-size:13px; display:inline-flex; align-items:center;">
                ← Overview
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-card">
        <form method="GET" action="{{ route('tenant.reports.task-compliance') }}" class="filter-grid">
            <!-- Period Type -->
            <div class="filter-group">
                <label>Frequency</label>
                <select name="period_type" class="filter-control">
                    <option value="" {{ empty($periodType) ? 'selected' : '' }}>All Frequencies</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ (string)$periodType === (string)$p->id ? 'selected' : '' }}>{{ ucfirst($p->period ?? $p->period_name ?? $p->name ?? "Frequency #" . $p->id) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Store -->
            <div class="filter-group">
                <label>Store Location</label>
                <select name="salon_id" class="filter-control">
                    <option value="">All Salons</option>
                    @foreach($allSalons as $s)
                        <option value="{{ $s->id }}" {{ (string)$salonId === (string)$s->id ? 'selected' : '' }}>{{ $s->salon_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div class="filter-group">
                <label>Task Category</label>
                <select name="category_id" class="filter-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ (string)$categoryId === (string)$c->id ? 'selected' : '' }}>{{ $c->task_category_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div class="filter-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $startDateStr }}" class="filter-control">
            </div>

            <!-- End Date -->
            <div class="filter-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $endDateStr }}" class="filter-control">
            </div>

            <!-- Action -->
            <div style="display: flex; gap: 6px;">
                <button type="submit" style="height: 38px; padding: 0 18px; background: #334155; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Apply</button>
            </div>
        </form>
    </div>

    <!-- Compliance Matrix Table -->
    <div class="card">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                Active Checklist Questions in Scope: <span style="color: #4338ca;">{{ $totalQuestions }} Questions</span>
            </div>
            <div style="display: flex; gap: 12px; font-size: 11px; font-weight: 700;">
                <span style="color: #15803d;">● 100% Completed</span>
                <span style="color: #b45309;">● Partial</span>
                <span style="color: #b91c1c;">● 0% / Missed</span>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="min-width: 200px;">Store / Salon</th>
                        @foreach($dateHeaders as $header)
                            <th style="text-align: center; min-width: 80px;">
                                <div>{{ $header['formatted'] }}</div>
                                <div style="font-size: 10px; font-weight: 600; text-transform: none; color: var(--text-muted);">{{ $header['day'] }}</div>
                            </th>
                        @endforeach
                        <th style="text-align: right; min-width: 100px;">Overall %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrix as $row)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ $row['salon_name'] }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $row['city'] }}</div>
                            </td>
                            @foreach($dateHeaders as $header)
                                @php
                                    $cell = $row['columns'][$header['date_str']];
                                    $cellClass = match(true) {
                                        $cell['answered'] === 0 => 'cell-zero',
                                        $cell['answered'] >= $totalQuestions && $totalQuestions > 0 => 'cell-100',
                                        default => 'cell-partial'
                                    };
                                @endphp
                                <td style="text-align: center;">
                                    <span class="cell-pill {{ $cellClass }}">
                                        <span>{{ $cell['answered'] }}/{{ $totalQuestions }}</span>
                                        <span style="font-size: 9px; opacity: 0.85;">{{ $cell['percentage'] }}%</span>
                                    </span>
                                </td>
                            @endforeach
                            <td style="text-align: right;">
                                <div style="font-weight: 800; font-size: 14px; color: {{ $row['overall_percentage'] >= 80 ? '#047857' : ($row['overall_percentage'] >= 50 ? '#b45309' : '#b91c1c') }};">
                                    {{ $row['overall_percentage'] }}%
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $row['total_answered'] }} / {{ $row['total_expected'] }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($dateHeaders) + 2 }}" style="text-align: center; padding: 32px;">
                                <p style="font-size: 14px; color: var(--text-muted);">No salon compliance data found for selected criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
