@extends('layouts.tenant')

@section('title', $salon->salon_name)
@section('page-title', 'Store Overview')

@section('content')
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('tenant.salons.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Stores List
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tenant.salons.edit', $salon->id) }}" style="background: var(--primary); color: white; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                ✏️ Edit Store
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        <!-- Left: Details & Location -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🏪 {{ $salon->salon_name }}</h3>
                <span class="badge {{ $salon->status == 1 ? 'badge-active' : 'badge-inactive' }}">
                    {{ $salon->status == 1 ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                <div>
                    <span style="color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; display: block;">Address</span>
                    <strong>{{ $salon->address_1 ?? '-' }}</strong>
                    @if($salon->address_2)
                        <div>{{ $salon->address_2 }}</div>
                    @endif
                    <div>{{ $salon->city ?? '-' }}, {{ $salon->stateRel->name ?? $salon->state ?? '-' }} {{ $salon->zipcode ?? '' }}</div>
                    <div>{{ $salon->country->name ?? '-' }}</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding-top: 12px; border-top: 1px solid var(--border);">
                    <div>
                        <span style="color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; display: block;">Phone</span>
                        <strong>{{ $salon->salon_phone ?? 'Not provided' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; display: block;">Email</span>
                        <strong>{{ $salon->salon_email ?? 'Not provided' }}</strong>
                    </div>
                </div>

                @if($salon->salon_lat && $salon->salon_long)
                    <div style="padding-top: 12px; border-top: 1px solid var(--border);">
                        <span style="color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; display: block;">GPS Coordinates</span>
                        <code>Lat: {{ $salon->salon_lat }}, Long: {{ $salon->salon_long }}</code>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: 7-Day Timings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">⏰ Operating Schedule</h3>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salon->timings as $timing)
                            <tr>
                                <td><strong>{{ $timing->day_name }}</strong></td>
                                <td>
                                    @if($timing->is_closed)
                                        <span style="color: var(--danger); font-weight: 600;">Closed</span>
                                    @else
                                        {{ date('g:i A', strtotime($timing->open_time)) }} - {{ date('g:i A', strtotime($timing->close_time)) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $timing->is_closed ? 'badge-inactive' : 'badge-active' }}">
                                        {{ $timing->is_closed ? 'Holiday' : 'Open' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                    No custom timings configured. Default: 9:00 AM - 9:00 PM.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection