@extends('layouts.tenant')

@section('title', 'Edit Designation')
@section('page-title', 'Edit Designation: ' . $designation->designation)

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tenant.designations.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Designations
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <div>
                <strong>Please fix the following errors:</strong>
                <ul style="margin-top: 6px; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">🏷️ Edit Designation Details</h3>
        </div>

        <form action="{{ route('tenant.designations.update', $designation->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Designation / Role Name <span style="color:red;">*</span></label>
                <input type="text" name="designation" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('designation', $designation->designation) }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Status</label>
                <select name="status" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                    <option value="1" {{ old('status', (string)$designation->status) === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', (string)$designation->status) === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('tenant.designations.index') }}" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border); background: white; color: var(--text-muted); text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);">
                    Update Designation
                </button>
            </div>
        </form>
    </div>
@endsection