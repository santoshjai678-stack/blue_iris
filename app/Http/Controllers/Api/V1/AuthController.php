<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tenant\Employee;
use App\Models\Tenant\Salon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    /**
     * Email / Phone Password Login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', // email or phone
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed', $validator->errors()->toArray(), 422);
        }

        $login = $request->input('login');
        $password = $request->input('password');

        $user = User::where('email', $login)
            ->orWhere('phone_no', $login)
            ->orWhereRaw('REPLACE(phone_no, "-", "") = ?', [str_replace('-', '', $login)])
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->sendError('Invalid login credentials provided.', [], 401);
        }

        if ($user->status == 0) {
            return $this->sendError('Your account has been deactivated. Please contact your manager.', [], 403);
        }

        // Issue Sanctum Token
        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        return $this->sendResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'roles' => $roles,
                'permissions' => $permissions,
            ],
            'tenant' => [
                'id' => tenant('id'),
                'name' => tenant('name'),
            ]
        ], 'Login successful.');
    }

    /**
     * Generate OTP for Phone Number Login
     */
    public function generateOtp(Request $request): JsonResponse
    {
        $phone = $request->input('phone_no') ?? $request->input('phone_number.number') ?? $request->input('phone_number');
        if (empty($phone)) {
            return $this->sendError('Phone number is required.', [], 422);
        }

        $cleanPhone = str_replace(['-', ' ', '+'], '', (string)$phone);

        // Check if user or employee exists with this phone
        $user = User::whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->first();
        $employee = Employee::whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->first();

        if (!$user && !$employee) {
            return $this->sendError('This phone number is not registered in the system.', [], 404);
        }

        // Generate 6 digit OTP (for testing/demo: 123456 or random)
        $otp = (string)rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('mobile_otps')->insert([
            'phone_no' => $cleanPhone,
            'otp' => $otp,
            'status' => 0,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->sendResponse([
            'phone_no' => $cleanPhone,
            'expires_in_minutes' => 10,
            'dev_otp' => config('app.debug') ? $otp : null, // expose OTP in debug mode for seamless API testing
        ], 'OTP generated successfully. Please verify to continue.');
    }

    /**
     * Verify OTP & Authenticate
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $phone = $request->input('phone_no') ?? $request->input('phone_number.number') ?? $request->input('phone_number');
        $otp = $request->input('otp') ?? $request->input('phone_number.otp');

        if (empty($phone) || empty($otp)) {
            return $this->sendError('Phone number and OTP are required.', [], 422);
        }

        $cleanPhone = str_replace(['-', ' ', '+'], '', (string)$phone);

        $otpRecord = DB::table('mobile_otps')
            ->where('phone_no', $cleanPhone)
            ->where('otp', $otp)
            ->where('status', 0)
            ->where('expires_at', '>=', now())
            ->latest('id')
            ->first();

        if (!$otpRecord) {
            return $this->sendError('Invalid or expired OTP. Please try again.', [], 400);
        }

        // Mark OTP as used
        DB::table('mobile_otps')->where('id', $otpRecord->id)->update(['status' => 1]);

        // Find or link User
        $user = User::whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->first();

        if (!$user) {
            $employee = Employee::whereRaw('REPLACE(phone_no, "-", "") = ?', [$cleanPhone])->first();
            if ($employee) {
                $user = User::create([
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'email' => $employee->email ?? ($cleanPhone . '@mobile.store'),
                    'phone_no' => $cleanPhone,
                    'password' => Hash::make(Str::random(16)),
                    'status' => 1,
                    'email_verified_at' => now(),
                ]);
            }
        }

        if (!$user) {
            return $this->sendError('User account not found.', [], 404);
        }

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'roles' => $user->getRoleNames(),
            ],
            'tenant' => [
                'id' => tenant('id'),
                'name' => tenant('name'),
            ]
        ], 'OTP verified and logged in successfully.');
    }

    /**
     * Authenticated User Profile & Assignments
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');

        // Check if user has an associated salon staff profile
        $employee = Employee::with('activeSchedule.salon')->where('email', $user->email)->orWhere('phone_no', $user->phone_no)->first();

        return $this->sendResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'roles' => $roles,
                'permissions' => $permissions,
            ],
            'assigned_store' => $employee && $employee->activeSchedule && $employee->activeSchedule->salon ? [
                'id' => $employee->activeSchedule->salon->id,
                'name' => $employee->activeSchedule->salon->salon_name,
                'city' => $employee->activeSchedule->salon->city,
                'shift_start' => $employee->activeSchedule->shift_start_time,
                'shift_end' => $employee->activeSchedule->shift_end_time,
            ] : null,
            'tenant' => [
                'id' => tenant('id'),
                'name' => tenant('name'),
            ]
        ]);
    }

    /**
     * Logout & Revoke Current Token
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return $this->sendResponse(null, 'Logged out successfully.');
    }
}