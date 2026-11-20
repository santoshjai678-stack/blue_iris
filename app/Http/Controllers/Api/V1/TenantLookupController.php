<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TenantLookupController extends Controller
{
    /**
     * Resolve Tenant Workspace URL by Subdomain, Company Name, or Employee Phone
     */
    public function lookup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'nullable|string',
            'subdomain'  => 'nullable|string',
            'phone_no'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim((string)($request->input('identifier') ?? $request->input('subdomain') ?? ''));
        $phoneNo = trim((string)($request->input('phone_no') ?? ''));

        $tenant = null;

        // 1. Lookup by subdomain / tenant id / domain name
        if (!empty($identifier)) {
            $cleanSlug = strtolower(str_replace(['http://', 'https://', '/', '.localhost', '.127.0.0.1'], '', $identifier));

            $tenant = Tenant::with('domains')
                ->where('id', $cleanSlug)
                ->orWhere('name', 'like', "%{$identifier}%")
                ->orWhereHas('domains', function ($q) use ($identifier, $cleanSlug) {
                    $q->where('domain', 'like', "%{$cleanSlug}%")
                      ->orWhere('domain', 'like', "%{$identifier}%");
                })
                ->first();
        }

        // 2. Lookup by phone number across tenant databases if phone provided
        if (!$tenant && !empty($phoneNo)) {
            $cleanPhone = str_replace(['-', ' ', '+'], '', $phoneNo);
            $allTenants = Tenant::with('domains')->where('status', 1)->get();

            foreach ($allTenants as $t) {
                try {
                    $hasUser = $t->run(function () use ($cleanPhone) {
                        return DB::table('users')->whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->exists()
                            || DB::table('employees')->whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->exists();
                    });

                    if ($hasUser) {
                        $tenant = $t;
                        break;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Workspace not found. Please check your company subdomain or registered phone number.',
            ], 404);
        }

        if ($tenant->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'This workspace account is inactive. Please contact support.',
            ], 403);
        }

        // Pick clean localhost domain or custom domain
        $domainRecord = $tenant->domains->first(function ($d) {
            return !str_ends_with($d->domain, '127.0.0.1');
        });
        $domain = $domainRecord ? $domainRecord->domain : ($tenant->id . '.localhost');

        $port = request()->getPort();
        $portSuffix = ($port && !in_array($port, [80, 443])) ? ":{$port}" : '';
        $scheme = request()->getScheme();
        $tenantBaseUrl = "{$scheme}://{$domain}{$portSuffix}";

        return response()->json([
            'success' => true,
            'message' => 'Tenant workspace resolved successfully.',
            'data' => [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'subdomain' => str_replace('.localhost', '', $domain),
                'domain' => $domain,
                'base_url' => $tenantBaseUrl,
                'api_base_url' => "{$tenantBaseUrl}/api/v1",
                'login_url' => "{$tenantBaseUrl}/login",
            ]
        ]);
    }
}
