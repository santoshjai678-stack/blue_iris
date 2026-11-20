@extends('layouts.tenant')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Compliance Analytics')

@section('content')
    <!-- KPI Header Stats -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Total Stores Monitored</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--text-main);">{{ $salonsCount }}</div>
            <div style="font-size: 12px; color: var(--success); font-weight: 600; margin-top: 4px;">100% Operational</div>
        </div>

        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Active Daily Tasks</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--text-main);">{{ $tasksCount }}</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Across {{ $categories->count() }} Categories</div>
        </div>

        <div class="card" style="padding: 16px 20px; margin-bottom: 0;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Audits Completed (Month)</div>
            <div style="font-size: 24px; font-weight: 800; color: #4338ca;">{{ $totalAnswersThisMonth }}</div>
            <div style="font-size: 12px; color: var(--success); font-weight: 600; margin-top: 4px;">↑ Active Compliance</div>
        </div>
    </div>

    <!-- Navigation Cards for Reports Sub-modules -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">📊 Reports Suite Navigation</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Access detailed matrix grids, audit history logs, and CSV export tools.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <a href="{{ route('tenant.reports.task-compliance') }}" class="card" style="text-decoration: none; display: flex; align-items: flex-start; gap: 14px; padding: 18px; border: 1.5px solid #e0e7ff; background: #fafafe; margin-bottom: 0; transition: all 0.2s ease;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">🗓️</div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #1e1b4b; margin-bottom: 4px;">Task Compliance Matrix</h4>
                    <p style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">Daily/Weekly/Monthly interactive matrix with green/amber/red status per task and store.</p>
                </div>
            </a>

            <a href="{{ route('tenant.reports.survey-responses') }}" class="card" style="text-decoration: none; display: flex; align-items: flex-start; gap: 14px; padding: 18px; border: 1.5px solid #ede9fe; background: #faf9ff; margin-bottom: 0; transition: all 0.2s ease;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #ede9fe; color: #6d28d9; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">📑</div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #3b0764; margin-bottom: 4px;">Survey Responses Audit Log</h4>
                    <p style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">Granular response logs with DataTables server-side filtering, category filters, and auditor timestamps.</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Store Compliance Ranking -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">🏪 Store Branch Compliance Ranking (This Week)</h3>
            <span style="font-size: 12px; color: var(--text-muted);">Updated in real-time</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Store Name</th>
                        <th>Location</th>
                        <th>Audits Completed (Week)</th>
                        <th>Compliance Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salons as $idx => $s)
                        <tr>
                            <td><strong style="color: var(--primary);">#{{ $idx + 1 }}</strong></td>
                            <td><strong>{{ $s['name'] }}</strong></td>
                            <td>{{ $s['city'] }}</td>
                            <td>{{ $s['answers_week'] }} submissions</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex-grow: 1; max-width: 100px; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                        <div style="width: {{ $s['compliance_rate'] }}%; height: 100%; background: {{ $s['compliance_rate'] >= 75 ? '#10b981' : ($s['compliance_rate'] >= 40 ? '#f59e0b' : '#ef4444') }};"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 700; color: {{ $s['compliance_rate'] >= 75 ? '#166534' : ($s['compliance_rate'] >= 40 ? '#92400e' : '#991b1b') }};">{{ $s['compliance_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">No store audit data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection