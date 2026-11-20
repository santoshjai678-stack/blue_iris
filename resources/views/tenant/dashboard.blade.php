@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page-title', 'Overview Dashboard')

@section('content')
    <!-- Metric Cards -->
    <div class="kpi-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0;">
            <div class="stat-info">
                <h3 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $totalSalons }}</h3>
                <p style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Salons / Stores</p>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px;">🏪</div>
        </div>

        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0;">
            <div class="stat-info">
                <h3 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $totalEmployees }}</h3>
                <p style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Staff</p>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #dcfce7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 20px;">👥</div>
        </div>

        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0;">
            <div class="stat-info">
                <h3 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $totalQuestions }}</h3>
                <p style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Active Checklist</p>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 20px;">📋</div>
        </div>

        <div class="card" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0;">
            <div class="stat-info">
                <h3 style="font-size: 26px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $todayAttendance }}</h3>
                <p style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Today's Submissions</p>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ede9fe; color: #6d28d9; display: flex; align-items: center; justify-content: center; font-size: 20px;">📊</div>
        </div>
    </div>

    <!-- Overview Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <!-- Recent Stores -->
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">🏪 Registered Stores / Salons</h3>
                <a href="{{ route('tenant.salons.index') }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none;">View All →</a>
            </div>
            @if(isset($recentSalons) && $recentSalons->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Salon Name</th>
                                <th>City</th>
                                <th>Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSalons as $salon)
                                <tr>
                                    <td><strong>{{ $salon->salon_name }}</strong></td>
                                    <td>{{ $salon->city ?? '-' }}</td>
                                    <td>{{ $salon->salon_phone ?? '-' }}</td>
                                    <td>
                                        @if($salon->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: var(--text-muted); font-size: 13px;">No stores registered yet.</p>
            @endif
        </div>

        <!-- Recent Tasks -->
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main);">📋 Active Survey Checklist</h3>
                <a href="{{ route('tenant.tasks.index') }}" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none;">View All →</a>
            </div>
            @if(isset($recentTasks) && $recentTasks->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Task Question</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTasks as $task)
                                <tr>
                                    <td><strong>{{ Str::limit($task->question, 40) }}</strong></td>
                                    <td><span class="badge" style="background:#ede9fe; color:#6d28d9;">{{ $task->category->task_category_name ?? '-' }}</span></td>
                                    <td>
                                        @if($task->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: var(--text-muted); font-size: 13px;">No tasks created yet.</p>
            @endif
        </div>
    </div>
@endsection