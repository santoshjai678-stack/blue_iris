@extends('layouts.tenant')

@section('page-title', 'Add New Staff Member')

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
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
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
    .btn-cancel:hover { background: #e2e8f0; }
</style>
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Add New Staff Member</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Create salon staff profile and assign primary store work location.</p>
        </div>
        <a href="{{ route('tenant.employees.index') }}" class="btn-cancel">← Back to Directory</a>
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

    <form method="POST" action="{{ route('tenant.employees.store') }}">
        @csrf

        <!-- 1. Personal Info -->
        <div class="form-card">
            <div class="section-title">
                <span>👤</span> 1. Personal & Contact Information
            </div>

            <div class="form-grid-4">
                <div class="form-group">
                    <label for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. John" class="form-control">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Doe" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="john.doe@salon.com" class="form-control">
                </div>
                <div class="form-group">
                    <label for="phone_no">Phone Number</label>
                    <input type="text" id="phone_no" name="phone_no" value="{{ old('phone_no') }}" placeholder="(555) 000-0000" class="form-control">
                </div>
            </div>

            <div class="form-grid-4" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="joining_date">Hire / Joining Date</label>
                    <input type="date" id="joining_date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="status">Employment Status <span class="req">*</span></label>
                    <select id="status" name="status" required class="form-control">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Store Location Assignment -->
        <div class="form-card">
            <div class="section-title">
                <span>🏪</span> 2. Store Location & Shift Schedule
            </div>

            <div class="form-grid-2" style="margin-bottom: 0;">
                <div class="form-group">
                    <label for="salon_id">Assigned Store / Salon <span class="req">*</span></label>
                    <select id="salon_id" name="salon_id" required class="form-control">
                        <option value="">-- Select Store Location --</option>
                        @foreach($salons as $salon)
                            <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>
                                {{ $salon->salon_name }} ({{ $salon->city ?? 'Location' }})
                            </option>
                        @endforeach
                    </select>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Primary work location for daily shift tasks and mobile checklists.</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label for="start_time">Shift Start Time</label>
                        <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="end_time">Shift End Time</label>
                        <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="form-control">
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
                <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="123 Main St, Apt 4B" class="form-control">
            </div>

            <div class="form-grid-4" style="margin-bottom: 0;">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" placeholder="City" class="form-control">
                </div>
                <div class="form-group">
                    <label for="state">State / Province</label>
                    <input type="text" id="state" name="state" value="{{ old('state') }}" placeholder="State" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode') }}" placeholder="Zipcode" class="form-control">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('tenant.employees.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Save Employee</button>
        </div>
    </form>
</div>
@endsection