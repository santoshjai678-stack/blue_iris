<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends BaseApiController
{
    /**
     * Register Device Push Token
     */
    public function subscribe(Request $request): JsonResponse
    {
        $deviceToken = $request->input('player_id') ?? $request->input('device_token') ?? $request->input('fcm_token');
        if (empty($deviceToken)) {
            return $this->sendError('Device token or player_id is required.', [], 422);
        }

        // Store or update device token if needed
        return $this->sendResponse([
            'device_token' => $deviceToken,
            'status' => 'subscribed',
        ], 'Push notification subscription registered successfully.');
    }
}