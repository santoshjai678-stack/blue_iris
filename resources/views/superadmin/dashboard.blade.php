@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Central Overview</h1>
        <a href="{{ route('superadmin.tenants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            + New Tenant
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500">Total Tenants</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $totalTenants }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500">Active Tenants</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $activeTenants }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-slate-500">Inactive Tenants</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $inactiveTenants }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tenants Table -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-900">Recently Created Tenants</h2>
            <a href="{{ route('superadmin.tenants.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View All &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tenant Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tenant ID / DB</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Domain</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white text-sm">
                    @forelse($recentTenants as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $t->name }}</td>
                        <td class="px-6 py-4 text-slate-600">
                            <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">{{ $t->id }}</span>
                        </td>
                        <td class="px-6 py-4 text-blue-600">
                            {{ optional($t->domains->first())->domain ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($t->status == 1)
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $t->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No tenants created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection