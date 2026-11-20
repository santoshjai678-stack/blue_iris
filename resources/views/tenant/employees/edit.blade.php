@extends('layouts.tenant')

@section('page-title', 'Edit Staff: ' . $employee->full_name)

@section('styles')
<style>
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
    }
    .section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-main);
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-grid-4 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }
    .req { color: #dc2626; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        color: var(--text-main);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .btn-submit {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        transition: all 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Edit Profile: {{ $employee->full_name }}</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Update staff details, store work location, and employment status.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tenant.employees.show', $employee) }}" class="btn-cancel">View Profile</a>
            <a href="{{ route('tenant.employees.index') }}" class="btn-cancel">Directory</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tenant.employees.update', $employee) }}">
        @csrf
        @method('PUT')

        <!-- 1. Personal Info -->
        <div class="form-card">
            <div class="section-title">
                <span>👤</span> 1. Personal & Contact Information
            </div>

            <div class="form-grid-4">
                <div class="form-group">
                    <label for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="phone_no">Phone Number</label>
                    <input type="text" id="phone_no" name="phone_no" value="{{ old('phone_no', $employee->phone_no) }}" class="form-control">
                </div>
            </div>

            <div class="form-grid-4" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob', $employee->dob ? $employee->dob->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="joining_date">Joining Date</label>
                    <input type="date" id="joining_date" name="joining_date" value="{{ old('joining_date', $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="rejoin_date">Rejoin Date (Optional)</label>
                    <input type="date" id="rejoin_date" name="rejoin_date" value="{{ old('rejoin_date', $employee->rejoin_date ? $employee->rejoin_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="status">Employment Status <span class="req">*</span></label>
                    <select id="status" name="status" required onchange="toggleTerminationField(this.value)" class="form-control">
                        <option value="1" {{ old('status', $employee->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $employee->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        <option value="2" {{ old('status', $employee->status) == 2 ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>

            <div id="termination_reason_div" style="margin-top: 16px; display: {{ old('status', $employee->status) == 2 ? 'block' : 'none' }};">
                <div class="form-group">
                    <label for="termination_reason" style="color: #b91c1c;">Termination Reason</label>
                    <input type="text" id="termination_reason" name="termination_reason" value="{{ old('termination_reason') }}" placeholder="Reason for termination..." class="form-control" style="border-color: #fca5a5;">
                </div>
            </div>
        </div>

        <!-- 2. Store Location Assignment -->
        <div class="form-card">
            <div class="section-title">
                <span>🏪</span> 2. Store Location & Shift Schedule
            </div>

            @php
                $activeSalonId = old('salon_id', $employee->activeSchedule ? $employee->activeSchedule->salon_id : '');
                $startTime = old('start_time', $employee->activeSchedule ? $employee->activeSchedule->start_time : '');
                $endTime = old('end_time', $employee->activeSchedule ? $employee->activeSchedule->end_time : '');
            @endphp

            <div class="form-grid-2" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="salon_id">Assigned Store / Salon <span class="req">*</span></label>
                    <select id="salon_id" name="salon_id" required class="form-control">
                        <option value="">-- Select Store Location --</option>
                        @foreach($salons as $salon)
                            <option value="{{ $salon->id }}" {{ (string)$activeSalonId === (string)$salon->id ? 'selected' : '' }}>
                                {{ $salon->salon_name }} ({{ $salon->city ?? 'Location' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label for="start_time">Shift Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="{{ $startTime }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="end_time">Shift End Time</label>
                        <input type="time" id="end_time" name="end_time" value="{{ $endTime }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Address -->
        <div class="form-card">
            <div class="section-title">
                <span>📍</span> 3. Residential Address (Optional)
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address" value="{{ old('address', $employee->address) }}" placeholder="123 Main St" class="form-control">
            </div>

            <div class="form-grid-4" style="margin-bottom: 0;">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $employee->city) }}" placeholder="City" class="form-control">
                </div>
                <div class="form-group">
                    <label for="state">State / Province</label>
                    <input type="text" id="state" name="state" value="{{ old('state', $employee->state) }}" placeholder="State" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $employee->zipcode) }}" placeholder="Zipcode" class="form-control">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('tenant.employees.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Update Employee Profile</button>
        </div>
    </form>
</div>

@section('scripts')
<script>
function toggleTerminationField(statusVal) {
    const div = document.getElementById('termination_reason_div');
    if(statusVal === '2') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
</script>
@endsection
@endsection