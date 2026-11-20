@extends('layouts.tenant')

@section('title', 'Task Overview')
@section('page-title', 'Survey Task Details')

@section('content')
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('tenant.tasks.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
            ← Back to Tasks List
        </a>
        <a href="{{ route('tenant.tasks.edit', $task->id) }}" style="background: var(--primary); color: white; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
            ✏️ Edit Task
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        <!-- Left Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📝 Task Overview</h3>
                <span class="badge {{ $task->status == 1 ? 'badge-active' : 'badge-inactive' }}">
                    {{ $task->status == 1 ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 16px; line-height: 1.5;">
                {{ $task->question }}
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px; border-top: 1px solid var(--border); padding-top: 16px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Category:</span>
                    <strong>📂 {{ $task->category->task_category_name ?? '-' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Target Store:</span>
                    <strong>🏪 {{ $task->salon->salon_name ?? 'Universal (All Stores)' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Target Designation:</span>
                    <strong>👥 {{ $task->designation->designation ?? '-' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Survey Frequency:</span>
                    <strong>📅 {{ $task->period->period ?? 'Daily' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Tag Type:</span>
                    <strong style="text-transform: capitalize;">{{ $task->task_tag_slug }} ({{ $task->task_type }})</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Mandatory:</span>
                    <strong>{{ $task->is_question_mandatory ? 'Yes (Required)' : 'Optional' }}</strong>
                </div>
            </div>
        </div>

        <!-- Right Card: Options & Slots -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">⚙️ Response Format & Timing</h3>
            </div>

            <div style="margin-bottom: 20px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 6px;">Control Type</span>
                @if($task->control_type == 1)
                    <span style="font-weight: 700; color: #047857;">🔘 Radio Button (Single Choice)</span>
                @elseif($task->control_type == 2)
                    <span style="font-weight: 700; color: #0369a1;">☑️ Checkbox (Multiple Selection)</span>
                @else
                    <span style="font-weight: 700; color: #6d28d9;">✍️ Text Input</span>
                @endif
            </div>

            @if($task->options->count() > 0)
                <div style="margin-bottom: 20px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 8px;">Configured Choice Options</span>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 6px;">
                        @foreach($task->options as $opt)
                            <li style="background: #f8fafc; padding: 8px 12px; border-radius: 6px; font-size: 13px; border: 1px solid var(--border);">
                                • {{ $opt->option }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 8px;">Configured Slot Times</span>
                @forelse($task->slots as $slot)
                    <div style="background: #eff6ff; color: #1e40af; padding: 8px 12px; border-radius: 6px; font-size: 13px; margin-bottom: 6px; display: flex; justify-content: space-between;">
                        <strong>⏰ {{ $slot->slot_name }}</strong>
                        <span>{{ date('g:i A', strtotime($slot->slot_start_time)) }} - {{ date('g:i A', strtotime($slot->slot_end_time)) }}</span>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 13px;">No specific slot timings assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection