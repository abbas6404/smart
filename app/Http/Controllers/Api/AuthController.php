<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AccountNumberHelper;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?: null,
            'password' => Hash::make($validated['password']),
            'referral_code' => AccountNumberHelper::generateReferralCode(),
        ]);

        if (isset($validated['referral_code'])) {
            // Try to find sponsor by referral code first
            $sponsor = User::where('referral_code', $validated['referral_code'])->first();
            
            // If not found by referral code, try by account number
            if (!$sponsor) {
                $sponsor = User::where('account_number', $validated['referral_code'])->first();
            }
            
            if ($sponsor) {
                $user->update(['sponsor_id' => $sponsor->id]);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'User registered successfully'
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string', // Can be email or phone
            'password' => 'required|string',
        ]);

        $login = $validated['login'];
        $password = $validated['password'];

        // Determine if login is email or phone
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // Check if user exists with this email
            $user = User::where('email', $login)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'login' => ['No account found with this email address.'],
                ]);
            }
            // Login with email
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            // Login with phone
            $credentials = ['phone' => $login, 'password' => $password];
        }

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Logged in successfully'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        // Send password reset email logic here
        // This would typically use Laravel's password reset functionality

        return response()->json([
            'message' => 'Password reset link sent to your email'
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Reset password logic here
        // This would typically use Laravel's password reset functionality

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }


}
