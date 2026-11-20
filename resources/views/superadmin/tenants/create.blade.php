@extends('layouts.superadmin')

@section('title', 'Create New Tenant')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Onboard New Tenant</h1>
            <p class="text-sm text-slate-500 mt-1">Provision a new multi-database tenant, assign subdomain, and configure admin access</p>
        </div>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to List</a>
    </div>

    <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        <div class="border-b border-slate-200 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Tenant Identity & Domain</h2>
            <p class="text-xs text-slate-500">Unique identifier and routing domain</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="tenant_id" class="block text-sm font-medium text-slate-700">Tenant ID (Slug) <span class="text-rose-500">*</span></label>
                <input type="text" name="tenant_id" id="tenant_id" value="{{ old('tenant_id') }}" required placeholder="e.g. loreal_india" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-slate-400 mt-1">Unique slug used for database name & references</p>
            </div>

            <div>
                <label for="subdomain" class="block text-sm font-medium text-slate-700">Subdomain <span class="text-rose-500">*</span></label>
                <div class="mt-1 flex rounded-lg shadow-sm">
                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}" required placeholder="e.g. loreal" class="block w-full rounded-l-lg border border-r-0 border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">.localhost</span>
                </div>
            </div>
        </div>

        <div class="border-b border-slate-200 pt-4 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Company & Admin Details</h2>
            <p class="text-xs text-slate-500">Primary tenant admin user credentials</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="tenant_name" class="block text-sm font-medium text-slate-700">Tenant / Admin Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="tenant_name" id="tenant_name" value="{{ old('tenant_name') }}" required placeholder="e.g. Santosh Jaiswal" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-slate-400 mt-1">Auto-divided into First & Last name for the tenant admin account</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Admin Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="admin@brand.com" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="phone_no" class="block text-sm font-medium text-slate-700">Phone Number <span class="text-rose-500">*</span></label>
                <input type="text" name="phone_no" id="phone_no" value="{{ old('phone_no') }}" required placeholder="e.g. 9876543210" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="website_url" class="block text-sm font-medium text-slate-700">Website URL</label>
                <input type="url" name="website_url" id="website_url" value="{{ old('website_url') }}" placeholder="https://example.com" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="border-b border-slate-200 pt-4 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Address Information</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-slate-700">Street Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="123 Main Street" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-slate-700">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="City" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="state" class="block text-sm font-medium text-slate-700">State / Province</label>
                <input type="text" name="state" id="state" value="{{ old('state') }}" placeholder="State" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="zipcode" class="block text-sm font-medium text-slate-700">Zip / Postal Code</label>
                <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode') }}" placeholder="Postal Code" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3 border-t border-slate-200">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm">Provision Tenant</button>
        </div>
    </form>
</div>
@endsection