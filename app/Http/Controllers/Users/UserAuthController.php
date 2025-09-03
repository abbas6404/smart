<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SubAccount;
use App\Helpers\AccountNumberHelper;
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
            'login' => 'required|string', // Can be email or phone
            'password' => 'required|string',
            'remember' => 'nullable', // Simplified validation
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        
        // Handle remember checkbox properly
        $remember = $request->has('remember') && $request->input('remember') == '1';

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

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Update last login information
            $user->updateLastLogin();
            
            $request->session()->regenerate();

            return redirect()->intended(route('user.dashboard.index'));
        }

        throw ValidationException::withMessages([
            'login' => ['The provided credentials are incorrect.'],
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
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?: null, // Handle empty email as null
            'password' => Hash::make($request->password),
        ]);

        // Create sub account with referral code
        try {
            $referralCode = AccountNumberHelper::generateReferralCode();
            $user->subAccounts()->create([
                'name' => $user->name,
                'user_id' => $user->id, // Ensure user_id is set
                'account_number' => AccountNumberHelper::generateAccountNumber(),
                'referral_code' => $referralCode,
                'is_primary' => true, // Set as primary account
            ]);
        } catch (\Exception $e) {
            // If sub account creation fails, delete the user and show error
            $user->delete();
            throw ValidationException::withMessages([
                'general' => ['Failed to create account. Please try again.'],
            ]);
        }

        // Set sponsor if referral code or account number provided
        if ($request->referral_code) {
            try {
                // Try to find sponsor by referral code first
                $sponsor = SubAccount::where('referral_code', $request->referral_code)->first();
                
                // If not found by referral code, try by account number
                if (!$sponsor) {
                    $sponsor = SubAccount::where('account_number', $request->referral_code)->first();
                }
                
                if ($sponsor) {
                    $user->subAccount->update(['referral_by_id' => $sponsor->id]);
                    
                    // Update sponsor's direct referral count
                    $sponsor->increment('direct_referral_count');
                }
            } catch (\Exception $e) {
                // Log the error but don't fail registration
                \Log::warning('Failed to set sponsor relationship', [
                    'user_id' => $user->id,
                    'referral_input' => $request->referral_code,
                    'error' => $e->getMessage()
                ]);
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

        return redirect()->route('login');
    }


}
