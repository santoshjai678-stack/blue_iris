<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Http\Requests\Superadmin\StoreTenantRequest;
use App\Http\Requests\Superadmin\UpdateTenantRequest;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Tenant::with('domains')->select('tenants.*');

            if ($request->filled('status') && $request->get('status') !== '') {
                $query->where('status', (int)$request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('tenant_info', function ($tenant) {
                    $domain = $tenant->domains->first()?->domain ?? ($tenant->id . '.' . config('tenancy.central_domains.0', 'localhost'));
                    return '<div>'
                        . '<div style="font-weight: 700; color: #0f172a;">🏢 ' . e($tenant->name ?? $tenant->id) . '</div>'
                        . '<div style="font-size: 12px; color: #64748b;">ID: <code>' . e($tenant->id) . '</code></div>'
                        . '</div>';
                })
                ->addColumn('domain_url', function ($tenant) {
                    $domain = $tenant->domains->first()?->domain ?? ($tenant->id . '.' . config('tenancy.central_domains.0', 'localhost'));
                    $url = 'http://' . $domain . ':8000';
                    return '<a href="' . $url . '" target="_blank" style="color: #4f46e5; font-weight: 600; text-decoration: none;">🌐 ' . e($domain) . ' ↗</a>';
                })
                ->addColumn('contact', function ($tenant) {
                    $email = $tenant->email ? '<div>✉️ ' . e($tenant->email) . '</div>' : '';
                    $phone = $tenant->phone_number ? '<div style="font-size: 11px; color: #64748b;">📞 ' . e($tenant->phone_number) . '</div>' : '';
                    return $email . $phone ?: '-';
                })
                ->addColumn('status_badge', function ($tenant) {
                    return $tenant->status == 1
                        ? '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Active</span>'
                        : '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Inactive</span>';
                })
                ->addColumn('created_date', function ($tenant) {
                    return $tenant->created_at ? $tenant->created_at->format('M d, Y') : '-';
                })
                ->addColumn('actions', function ($tenant) {
                    $showUrl = route('superadmin.tenants.show', $tenant->id);
                    $editUrl = route('superadmin.tenants.edit', $tenant->id);
                    $deleteUrl = route('superadmin.tenants.destroy', $tenant->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return '<div style="display: flex; gap: 6px; justify-content: flex-end;">'
                        . '<a href="' . $showUrl . '" class="px-2.5 py-1 text-xs font-semibold rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100" style="text-decoration: none;">Manage</a>'
                        . '<a href="' . $editUrl . '" class="px-2.5 py-1 text-xs font-semibold rounded bg-sky-50 text-sky-700 hover:bg-sky-100" style="text-decoration: none;">Edit</a>'
                        . '<form action="' . $deleteUrl . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this tenant and its database?\');">'
                        . $csrf . $method
                        . '<button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded bg-rose-50 text-rose-700 hover:bg-rose-100 border-none cursor-pointer">Delete</button>'
                        . '</form>'
                        . '</div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['tenant_info', 'domain_url', 'contact', 'status_badge', 'created_date', 'actions'])
                ->make(true);
        }

        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 1)->count(),
            'inactive' => Tenant::where('status', 0)->count(),
        ];

        return view('superadmin.tenants.index', compact('stats'));
    }

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();
        
        $tenantId = $validated['tenant_id'] ?? Str::slug($validated['tenant_name'] ?? $validated['company_name'] ?? 'tenant', '_');
        $tenantName = $validated['tenant_name'] ?? $validated['company_name'] ?? $tenantId;
        $subdomain = $validated['subdomain'] ?? $tenantId;
        $phoneNumber = $validated['phone_no'] ?? ($validated['phone_number'] ?? null);
        $postCode = $validated['zipcode'] ?? ($validated['post_code'] ?? null);

        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $tenantName,
            'email' => $validated['email'],
            'phone_number' => $phoneNumber,
            'website_url' => $validated['website_url'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'post_code' => $postCode,
            'status' => $validated['status'] ?? 1,
        ]);

        $centralDomain = config('tenancy.central_domains.0', 'localhost');
        $domain = $subdomain . '.' . $centralDomain;
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        $plainPassword = $validated['admin_password'] ?? Str::random(10);
        
        $nameParts = explode(' ', trim($tenantName), 2);
        $firstName = $nameParts[0] ?? 'Admin';
        $lastName = $nameParts[1] ?? 'User';

        $tenant->run(function () use ($validated, $firstName, $lastName, $phoneNumber, $plainPassword) {
            // Seed base roles and permissions
            (new RoleAndPermissionSeeder())->run();

            // Seed survey periods
            \App\Models\Tenant\SurveyPeriod::firstOrCreate(['id' => 1], ['period' => 'Daily', 'status' => 1]);
            \App\Models\Tenant\SurveyPeriod::firstOrCreate(['id' => 2], ['period' => 'Weekly', 'status' => 1]);
            \App\Models\Tenant\SurveyPeriod::firstOrCreate(['id' => 3], ['period' => 'Monthly', 'status' => 1]);

            // Seed default time slots
            \App\Models\Tenant\SurveySlotTime::firstOrCreate(['id' => 1], ['slot_name' => 'Morning Slot', 'time' => '10:00:00', 'status' => 1]);
            \App\Models\Tenant\SurveySlotTime::firstOrCreate(['id' => 2], ['slot_name' => 'Afternoon Slot', 'time' => '14:00:00', 'status' => 1]);
            \App\Models\Tenant\SurveySlotTime::firstOrCreate(['id' => 3], ['slot_name' => 'Evening Slot', 'time' => '18:00:00', 'status' => 1]);

            $user = \App\Models\User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone_no' => $phoneNumber,
                'password' => Hash::make($plainPassword),
                'status' => 1,
                'email_verified_at' => now(),
            ]);

            $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole($role);
        });

        return redirect()->route('superadmin.tenants.show', $tenant->id)
            ->with('success', "Tenant '{$tenant->name}' created successfully! Subdomain: {$domain} | Admin User: {$validated['email']} / Password: {$plainPassword}");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $validated = $request->validated();

        $tenantName = $validated['tenant_name'] ?? $validated['company_name'] ?? $tenant->name;
        $phoneNumber = $validated['phone_no'] ?? ($validated['phone_number'] ?? $tenant->phone_number);
        $postCode = $validated['zipcode'] ?? ($validated['post_code'] ?? $tenant->post_code);

        $tenant->update([
            'name' => $tenantName,
            'email' => $validated['email'],
            'phone_number' => $phoneNumber,
            'website_url' => $validated['website_url'] ?? $tenant->website_url,
            'address' => $validated['address'] ?? $tenant->address,
            'city' => $validated['city'] ?? $tenant->city,
            'state' => $validated['state'] ?? $tenant->state,
            'post_code' => $postCode,
            'status' => $validated['status'] ?? $tenant->status,
        ]);

        return redirect()->route('superadmin.tenants.show', $tenant->id)
            ->with('success', "Tenant '{$tenant->name}' updated successfully.");
    }

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;
        $tenant->delete();
        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Tenant '{$name}' and its database deleted successfully.");
    }
}
