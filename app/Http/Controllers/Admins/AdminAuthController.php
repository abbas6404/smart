<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLogin()
    {
        return view('admins.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Can be username or email
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // First, try to find the admin by username or email
        $admin = Admin::where('username', $login)
                     ->orWhere('email', $login)
                     ->first();
        
        if (!$admin) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if admin is active
        if (!$admin->isActive()) {
            throw ValidationException::withMessages([
                'login' => ['Your account has been suspended.'],
            ]);
        }

        // Now try to authenticate with the found admin
        $credentials = ['username' => $admin->username, 'password' => $password];
        
        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Update last login information
        $admin->updateLastLogin();
        
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard.index'));
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
