@extends('layouts.tenant')

@section('title', 'Add Task Category')
@section('page-title', 'Create Task Category')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tenant.task-categories.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Categories
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">📂 Category Details</h3>
        </div>

        <form action="{{ route('tenant.task-categories.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Category Name <span style="color:red;">*</span></label>
                <input type="text" name="task_category_name" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('task_category_name') }}" required placeholder="e.g. Daily Opening Checklist">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Display Order</label>
                    <input type="number" name="order_by_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" value="{{ old('order_by_id', $nextOrder) }}" min="1">
                </div>
                <div>
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Status</label>
                    <select name="status" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('tenant.task-categories.index') }}" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border); background: white; color: var(--text-muted); text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" class="btn-primary" style="padding: 10px 22px; border-radius: 8px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer;">
                    Save Category
                </button>
            </div>
        </form>
    </div>
@endsection