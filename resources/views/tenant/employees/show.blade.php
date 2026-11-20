@extends('layouts.tenant')

@section('page-title', 'Staff Profile: ' . $employee->full_name)

@section('styles')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #1e1b4b, #312e81);
        color: #fff;
        border-radius: var(--radius);
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(30, 27, 75, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .profile-avatar-lg {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 800;
        color: #fff;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    }
    .details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    @media (max-width: 900px) {
        .details-grid { grid-template-columns: 1fr; }
    }
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
    }
    .info-card h3 {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-item {
        margin-bottom: 14px;
    }
    .info-item .label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .info-item .val {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }
    .timeline {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid var(--border);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 18px;
    }
    .timeline-dot {
        position: absolute;
        left: -27px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary);
    }
    .btn-edit-prof {
        background: #f59e0b;
        color: #78350f;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-del {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        width: 100%;
    }
</style>
@endsection

@section('content')
<!-- Profile Hero -->
<div class="profile-hero">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="profile-avatar-lg">
            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <h1 style="font-size: 24px; font-weight: 800; margin: 0;">{{ $employee->full_name }}</h1>
                <span class="badge badge-status-{{ $employee->status }}" style="font-size: 11px;">
                    {{ $employee->status_label }}
                </span>
            </div>
            <div style="display: flex; gap: 16px; margin-top: 8px; font-size: 13px; opacity: 0.9; flex-wrap: wrap;">
                <span>✉ {{ $employee->email }}</span>
                @if($employee->phone_no)
                    <span>📞 {{ $employee->phone_no }}</span>
                @endif
                @if($employee->joining_date)
                    <span>🗓 Joined: {{ $employee->joining_date->format('M d, Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="{{ route('tenant.employees.edit', $employee) }}" class="btn-edit-prof">✏ Edit Staff</a>
        <a href="{{ route('tenant.employees.index') }}" class="btn-cancel" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.2);">← Directory</a>
    </div>
</div>

<!-- Details Grid -->
<div class="details-grid">
    <!-- Left Column -->
    <div>
        <!-- Store Assignment -->
        <div class="info-card">
            <h3>🏪 Current Store Location & Schedule</h3>
            @if($employee->activeSchedule && $employee->activeSchedule->salon)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #eef2ff; border-radius: 12px; border: 1px solid #c7d2fe;">
                    <div>
                        <div style="font-size: 16px; font-weight: 800; color: #312e81;">{{ $employee->activeSchedule->salon->salon_name }}</div>
                        <div style="font-size: 13px; color: #4338ca; margin-top: 2px;">
                            {{ $employee->activeSchedule->salon->address_1 ?? '' }} {{ $employee->activeSchedule->salon->city ? ', ' . $employee->activeSchedule->salon->city : '' }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; font-weight: 700; color: #4338ca; text-transform: uppercase;">Working Hours</div>
                        <div style="font-size: 13px; font-weight: 700; color: #312e81;">
                            {{ $employee->activeSchedule->start_time ? date('h:i A', strtotime($employee->activeSchedule->start_time)) : 'Standard' }}
                            -
                            {{ $employee->activeSchedule->end_time ? date('h:i A', strtotime($employee->activeSchedule->end_time)) : 'Standard' }}
                        </div>
                    </div>
                </div>
            @else
                <p style="font-size: 13px; color: var(--text-muted); font-style: italic;">No active store location assigned.</p>
            @endif
        </div>

        <!-- Personal & Location -->
        <div class="info-card">
            <h3>📍 Contact & Residential Details</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="info-item">
                    <div class="label">Date of Birth</div>
                    <div class="val">{{ $employee->dob ? $employee->dob->format('M d, Y') : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Phone Number</div>
                    <div class="val">{{ $employee->phone_no ?? '—' }}</div>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <div class="label">Residential Address</div>
                    <div class="val">
                        {{ $employee->address ?? 'No address provided' }}
                        @if($employee->city || $employee->state || $employee->zipcode)
                            <br><span style="font-weight: 400; color: var(--text-muted);">{{ implode(', ', array_filter([$employee->city, $employee->state, $employee->zipcode])) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Timeline & Actions -->
    <div>
        <div class="info-card">
            <h3>🗓 Employment History</h3>
            <div class="timeline">
                @forelse($employee->hireRecords as $record)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: {{ $record->termination_date ? '#ef4444' : '#10b981' }};"></div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                            {{ $record->hire_date ? 'Hired: ' . $record->hire_date->format('M d, Y') : 'Hiring Record' }}
                        </div>
                        @if($record->termination_date)
                            <div style="font-size: 12px; font-weight: 600; color: #b91c1c; margin-top: 2px;">
                                Terminated: {{ $record->termination_date->format('M d, Y') }}
                            </div>
                        @endif
                        @if($record->reason)
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px; background: #f8fafc; padding: 6px 10px; border-radius: 6px;">
                                {{ $record->reason }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p style="font-size: 13px; color: var(--text-muted); font-style: italic;">No hiring records recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="info-card" style="border-color: #fecaca; background: #fff5f5;">
            <h3 style="color: #991b1b;">⚠️ Danger Zone</h3>
            <p style="font-size: 12px; color: #7f1d1d; margin-bottom: 12px;">Permanently delete this staff member's profile and account credentials.</p>
            <form method="POST" action="{{ route('tenant.employees.destroy', $employee) }}" onsubmit="return confirm('Are you sure you want to permanently delete {{ $employee->full_name }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-del">Delete Employee</button>
            </form>
        </div>
    </div>
</div>
@endsection