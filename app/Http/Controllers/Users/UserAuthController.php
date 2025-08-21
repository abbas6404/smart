<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SubAccount;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Show user login form
     */
    public function showLogin()
    {
        return view('users.auth.login');
    }

    /**
     * Show user registration form
     */
    public function showRegister()
    {
        return view('users.auth.register');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Update last login information
            $user->updateLastLogin();
            
            $request->session()->regenerate();

            return redirect()->intended(route('user.dashboard.index'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:sub_accounts,referral_code',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create sub account with referral code
        $referralCode = $this->generateReferralCode();
        $user->subAccount()->create([
            'name' => $user->name,
            'account_number' => $this->generateAccountNumber(),
            'referral_code' => $referralCode,
        ]);

        // Set sponsor if referral code provided
        if ($request->referral_code) {
            $sponsor = SubAccount::where('referral_code', $request->referral_code)->first();
            if ($sponsor) {
                $user->subAccount->update(['referral_by_id' => $sponsor->id]);
                
                // Update sponsor's direct referral count
                $sponsor->increment('direct_referral_count');
            }
        }

        // Log in the user
        Auth::login($user);
        
        // Update last login
        $user->updateLastLogin();

        return redirect()->route('user.dashboard.index')->with('success', 'Account created successfully!');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }

    /**
     * Generate unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (SubAccount::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Generate unique account number
     */
    private function generateAccountNumber(): string
    {
        do {
            $number = 'ACC' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (SubAccount::where('account_number', $number)->exists());

        return $number;
    }
}
