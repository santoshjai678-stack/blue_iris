@extends('layouts.superadmin')

@section('title', 'Manage Tenant: ' . $tenant->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $tenant->name }}</h1>
            <p class="text-sm text-slate-500 mt-1">Tenant Database ID: <code class="bg-slate-100 px-2 py-0.5 rounded text-xs text-indigo-700 font-bold">{{ $tenant->id }}</code></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm">Edit Tenant</a>
            <a href="{{ route('superadmin.tenants.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">← Directory</a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Tenant Info -->
        <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Tenant Profile & Contact</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Company / Brand Name</span>
                    <span class="font-semibold text-slate-800">{{ $tenant->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Account Status</span>
                    @if($tenant->status == 1)
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Active</span>
                    @else
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full bg-rose-100 text-rose-800">Inactive</span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Admin Email</span>
                    <span class="text-slate-800">✉ {{ $tenant->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Phone Number</span>
                    <span class="text-slate-800">📞 {{ $tenant->phone_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Website</span>
                    @if($tenant->website_url)
                        <a href="{{ $tenant->website_url }}" target="_blank" class="text-blue-600 hover:underline">{{ $tenant->website_url }}</a>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold block">Created On</span>
                    <span class="text-slate-800">{{ $tenant->created_at ? $tenant->created_at->format('M d, Y H:i A') : '-' }}</span>
                </div>
            </div>

            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pt-4 pb-3">Location & Address</h3>
            <div class="text-sm text-slate-700 space-y-1">
                <div>{{ $tenant->address ?: 'No street address provided' }}</div>
                <div>{{ implode(', ', array_filter([$tenant->city, $tenant->state, $tenant->post_code])) }}</div>
            </div>
        </div>

        <!-- Tenant Domain & Access Card -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-xl p-6 shadow-md">
                <h3 class="text-sm uppercase font-bold text-indigo-200 tracking-wider mb-3">Portal URL</h3>
                @php
                    $primaryDomain = $tenant->domains->first()?->domain ?? ($tenant->id . '.' . config('tenancy.central_domains.0', 'localhost'));
                    $loginUrl = 'http://' . $primaryDomain . ':8000/login';
                @endphp
                <div class="bg-white/10 rounded-lg p-3 backdrop-blur-sm border border-white/20 mb-4">
                    <div class="text-xs text-indigo-200">Subdomain Access</div>
                    <div class="text-base font-mono font-bold truncate">{{ $primaryDomain }}</div>
                </div>
                <a href="{{ $loginUrl }}" target="_blank" class="block w-full text-center py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-bold text-sm shadow transition">
                    Open Tenant Portal ↗
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-2">Dedicated Database</h3>
                <p class="text-xs text-slate-500 mb-3">Isolated tenant database schema:</p>
                <div class="bg-slate-50 p-2.5 rounded font-mono text-xs font-semibold text-slate-700 border border-slate-200">
                    tenant{{ $tenant->id }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
