@extends('layouts.tenant')

@section('title', 'Add Survey Task')
@section('page-title', 'Create Survey Task / Checklist')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tenant.tasks.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Tasks List
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

    <form action="{{ route('tenant.tasks.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 24px;">
            
            <!-- Left Column: Question Details & Assignments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📝 Task Details & Target</h3>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Task / Question Text <span style="color:red;">*</span></label>
                    <textarea name="question" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; height: 90px;" required placeholder="e.g. Are all stations clean and sanitizers refilled before shift?">{{ old('question') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Category <span style="color:red;">*</span></label>
                        <select name="task_categorys_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('task_categorys_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->task_category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Target Designation <span style="color:red;">*</span></label>
                        <select name="designations_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" required>
                            <option value="">-- Select Role --</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ old('designations_id') == $desig->id ? 'selected' : '' }}>
                                    {{ $desig->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Target Store</label>
                        <select name="salon_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                            <option value="">🌐 Universal (All Stores)</option>
                            @foreach($salons as $salon)
                                <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>
                                    {{ $salon->salon_name }} ({{ $salon->city ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Survey Period <span style="color:red;">*</span></label>
                        <select name="survey_periods_id" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" required>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ old('survey_periods_id', 1) == $period->id ? 'selected' : '' }}>
                                    {{ $period->period }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Task Type</label>
                        <select name="task_type" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                            <option value="general" {{ old('task_type') == 'general' ? 'selected' : '' }}>General Checklist</option>
                            <option value="expiry" {{ old('task_type') == 'expiry' ? 'selected' : '' }}>Product Expiry Tracking</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Tag / Slug</label>
                        <select name="task_tag_slug" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                            <option value="general" {{ old('task_tag_slug') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="cash" {{ old('task_tag_slug') == 'cash' ? 'selected' : '' }}>Cash Audit</option>
                            <option value="attrition" {{ old('task_tag_slug') == 'attrition' ? 'selected' : '' }}>Attrition</option>
                            <option value="expiry" {{ old('task_tag_slug') == 'expiry' ? 'selected' : '' }}>Expiry</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                        <input type="checkbox" name="is_question_mandatory" id="is_mandatory" value="1" {{ old('is_question_mandatory', 1) ? 'checked' : '' }}>
                        <label for="is_mandatory" style="font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer;">
                            Mandatory Question
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                        <input type="checkbox" name="is_other_option_add" id="is_other" value="1" {{ old('is_other_option_add') ? 'checked' : '' }}>
                        <label for="is_other" style="font-size: 13px; font-weight: 600; color: var(--text-main); cursor: pointer;">
                            Allow "Other" input
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Status</label>
                    <select name="status" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;">
                        <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Right Column: Control Types, Options & Slot Times -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">⚙️ Response Options & Slots</h3>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Response Control Type <span style="color:red;">*</span></label>
                    <select name="control_type" id="control_type" class="form-input" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;" onchange="toggleOptionsSection()">
                        <option value="1" {{ old('control_type', 1) == 1 ? 'selected' : '' }}>🔘 Radio Button (Yes/No or Single Choice)</option>
                        <option value="2" {{ old('control_type') == 2 ? 'selected' : '' }}>☑️ Checkbox (Multiple Choice Options)</option>
                        <option value="3" {{ old('control_type') == 3 ? 'selected' : '' }}>✍️ Text / Numeric Input</option>
                    </select>
                </div>

                <!-- Custom Options Builder (Shown for Radio and Checkbox) -->
                <div id="options_section" style="margin-bottom: 24px; padding: 16px; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: var(--text-main);">Custom Answer Options</label>
                        <button type="button" onclick="addOptionField()" style="background: var(--primary); color: white; border: none; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                            ➕ Add Option
                        </button>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                        Leave empty to use default <strong>Yes</strong> / <strong>No</strong> choices for Radio questions.
                    </p>
                    <div id="options_container" style="display: flex; flex-direction: column; gap: 8px;">
                        <div class="option-row" style="display: flex; gap: 8px;">
                            <input type="text" name="options[]" class="form-input" style="flex-grow: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;" placeholder="Option text (e.g. Satisfactory)">
                            <button type="button" onclick="removeOptionField(this)" style="background: #fee2e2; color: #991b1b; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;">✕</button>
                        </div>
                    </div>
                </div>

                <!-- Slot Timings Section -->
                <div style="margin-bottom: 24px;">
                    <label class="form-label" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px;">
                        ⏰ Daily Slot Timings
                    </label>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                        Select which time intervals this task must be performed in store:
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($slotTimes as $slot)
                            <label style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="slot_times[]" value="{{ $slot->id }}" {{ in_array($slot->id, old('slot_times', [])) ? 'checked' : '' }}>
                                <strong>{{ $slot->slot_name }}</strong>
                                <span style="color: var(--text-muted); font-size: 12px;">
                                    ({{ date('g:i A', strtotime($slot->slot_start_time)) }} - {{ date('g:i A', strtotime($slot->slot_end_time)) }})
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('tenant.tasks.index') }}" style="padding: 12px 20px; border-radius: 8px; border: 1px solid var(--border); background: white; color: var(--text-muted); text-decoration: none; font-weight: 600;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding: 12px 24px; border-radius: 8px; background: var(--primary); color: white; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);">
                        Save Survey Task
                    </button>
                </div>
            </div>

        </div>
    </form>
@endsection

@section('scripts')
<script>
    function toggleOptionsSection() {
        const val = document.getElementById('control_type').value;
        const optSec = document.getElementById('options_section');
        if (val == 3) {
            optSec.style.display = 'none';
        } else {
            optSec.style.display = 'block';
        }
    }

    function addOptionField() {
        const container = document.getElementById('options_container');
        const div = document.createElement('div');
        div.className = 'option-row';
        div.style.display = 'flex';
        div.style.gap = '8px';
        div.innerHTML = `
            <input type="text" name="options[]" class="form-input" style="flex-grow: 1; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;" placeholder="Option text">
            <button type="button" onclick="removeOptionField(this)" style="background: #fee2e2; color: #991b1b; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;">✕</button>
        `;
        container.appendChild(div);
    }

    function removeOptionField(btn) {
        btn.closest('.option-row').remove();
    }

    toggleOptionsSection();
</script>
@endsection