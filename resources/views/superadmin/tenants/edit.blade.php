@extends('layouts.superadmin')

@section('title', 'Edit Tenant')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Tenant: {{ $tenant->name }}</h1>
            <p class="text-sm text-slate-500 mt-1">Update tenant metadata, contact information, and active status</p>
        </div>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to List</a>
    </div>

    <form method="POST" action="{{ route('superadmin.tenants.update', $tenant->id) }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-200 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Tenant Identity</h2>
            <p class="text-xs text-slate-500">Tenant ID: <span class="font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $tenant->id }}</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="tenant_name" class="block text-sm font-medium text-slate-700">Tenant / Admin Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="tenant_name" id="tenant_name" value="{{ old('tenant_name', $tenant->name) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Admin Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="phone_no" class="block text-sm font-medium text-slate-700">Phone Number <span class="text-rose-500">*</span></label>
                <input type="text" name="phone_no" id="phone_no" value="{{ old('phone_no', $tenant->phone_number) }}" required class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="website_url" class="block text-sm font-medium text-slate-700">Website URL</label>
                <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $tenant->website_url) }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status <span class="text-rose-500">*</span></label>
                <select name="status" id="status" required class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1" {{ old('status', $tenant->status) == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $tenant->status) == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="border-b border-slate-200 pt-4 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Address Information</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-slate-700">Street Address</label>
                <input type="text" name="address" id="address" value="{{ old('address', $tenant->address) }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-slate-700">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $tenant->city) }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="state" class="block text-sm font-medium text-slate-700">State / Province</label>
                <input type="text" name="state" id="state" value="{{ old('state', $tenant->state) }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="zipcode" class="block text-sm font-medium text-slate-700">Zip / Postal Code</label>
                <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $tenant->post_code) }}" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3 border-t border-slate-200">
            <a href="{{ route('superadmin.tenants.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm">Save Changes</button>
        </div>
    </form>
</div>
@endsection