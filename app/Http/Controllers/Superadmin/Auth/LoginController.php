<?php

namespace App\Http\Controllers\Superadmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Check active status
        $credentials['status'] = 1;

        if (Auth::guard('superadmin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('superadmin.dashboard'))
                ->with('success', 'Welcome back, ' . Auth::guard('superadmin')->user()->name . '!');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records or the account is inactive.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')
            ->with('success', 'You have been successfully logged out.');
    }
}