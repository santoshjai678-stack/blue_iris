<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tenant\Salon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends BaseApiController
{
    /**
     * Get List of Active Stores & Operating Hours
     */
    public function index(Request $request): JsonResponse
    {
        $salons = Salon::with(['timings', 'country', 'stateRel'])
            ->where('status', 1)
            ->get()
            ->map(function ($salon) {
                return [
                    'id' => $salon->id,
                    'salon_name' => $salon->salon_name,
                    'salon_code' => $salon->salon_code ?? ('STR-' . $salon->id),
                    'salon_phone' => $salon->salon_phone,
                    'salon_email' => $salon->salon_email,
                    'address' => $salon->address_1,
                    'city' => $salon->city,
                    'state' => $salon->stateRel->name ?? $salon->state,
                    'zipcode' => $salon->zipcode,
                    'latitude' => $salon->salon_lat,
                    'longitude' => $salon->salon_long,
                    'timings' => $salon->timings->map(fn($t) => [
                        'day' => $t->day_of_week,
                        'open' => $t->open_time,
                        'close' => $t->close_time,
                        'is_closed' => (bool)$t->is_closed,
                    ]),
                ];
            });

        return $this->sendResponse($salons, 'Stores fetched successfully.');
    }

    /**
     * Get Store Details
     */
    public function show(Salon $salon): JsonResponse
    {
        $salon->load(['timings', 'country', 'stateRel']);

        return $this->sendResponse([
            'id' => $salon->id,
            'salon_name' => $salon->salon_name,
            'salon_phone' => $salon->salon_phone,
            'salon_email' => $salon->salon_email,
            'address' => $salon->address_1,
            'city' => $salon->city,
            'state' => $salon->stateRel->name ?? $salon->state,
            'zipcode' => $salon->zipcode,
            'latitude' => $salon->salon_lat,
            'longitude' => $salon->salon_long,
            'timings' => $salon->timings,
        ], 'Store details fetched.');
    }
}