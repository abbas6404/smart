<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SubAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Helpers\AccountNumberHelper;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        \Log::info('📝 API REGISTRATION REQUEST STARTED', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'referral_code' => 'nullable|string',
            ]);
            
            // Handle phone number format variations
            $phone = $validated['phone'];
            
            // If phone starts with 0, convert to +88 format
            if (str_starts_with($phone, '0')) {
                $phone = '+88' . $phone;
            }
            // If phone doesn't start with +, add +88
            elseif (!str_starts_with($phone, '+')) {
                $phone = '+88' . $phone;
            }
            
            // Check if phone already exists after format conversion
            if (User::where('phone', $phone)->exists()) {
                throw ValidationException::withMessages([
                    'phone' => ['This phone number is already registered.'],
                ]);
            }
            
            $validated['phone'] = $phone;
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('❌ API REGISTRATION VALIDATION FAILED', [
                'errors' => $e->errors(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            throw $e;
        }

        // Start database transaction to ensure both user and sub-account are created
        DB::beginTransaction();
        
        try {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?: null,
                'password' => Hash::make($validated['password']),
            ]);

            // Find sponsor if referral code is provided
            $sponsorSubAccount = null;
            if (isset($validated['referral_code'])) {
                // Try to find sponsor by referral code in sub_accounts table
                $sponsorSubAccount = SubAccount::where('referral_code', $validated['referral_code'])->first();
                
                // If not found by referral code, try by account number
                if (!$sponsorSubAccount) {
                    $sponsorSubAccount = SubAccount::where('account_number', $validated['referral_code'])->first();
                }
            }

            // Create primary sub-account for the user
            $subAccount = SubAccount::create([
                'name' => $validated['name'] . ' - Primary Account',
                'user_id' => $user->id,
                'account_number' => AccountNumberHelper::generateAccountNumber(),
                'referral_code' => AccountNumberHelper::generateReferralCode(),
                'referral_by_id' => $sponsorSubAccount ? $sponsorSubAccount->id : null,
                'direct_referral_count' => 0,
                'purchase_referral_count' => 0,
                'generation_count' => 0,
                'total_balance' => 0.00,
                'remaining_withdrawal_limit' => 0.00,
                'total_withdrawal' => 0.00,
                'total_package_purchase' => 0.00,
                'total_deposit' => 0.00,
                'total_sponsor_commission' => 0.00,
                'total_generation_commission' => 0.00,
                'total_auto_income' => 0.00,
                'status' => 'active',
                'is_primary' => true,
                'last_balance_update_at' => now(),
            ]);

            // Update sponsor's direct referral count if sponsor exists
            if ($sponsorSubAccount) {
                $sponsorSubAccount->increment('direct_referral_count');
            }

            // Commit the transaction
            DB::commit();

            \Log::info('✅ API REGISTRATION SUCCESS - User and SubAccount created', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                'sub_account_id' => $subAccount->id,
                'account_number' => $subAccount->account_number,
                'referral_code' => $subAccount->referral_code,
                'sponsor_id' => $sponsorSubAccount ? $sponsorSubAccount->id : null,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            
            \Log::error('❌ API REGISTRATION FAILED - Database error', [
                'error' => $e->getMessage(),
                'user_data' => $validated,
                'timestamp' => now()
            ]);
            
            throw ValidationException::withMessages([
                'registration' => ['Registration failed. Please try again.'],
            ]);
        }

        $token = base64_encode($user->id . '|' . time() . '|' . \Illuminate\Support\Str::random(32));

        \Log::info('✅ API REGISTRATION SUCCESS - Final response', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_phone' => $user->phone,
            'sub_account_id' => $subAccount->id,
            'account_number' => $subAccount->account_number,
            'referral_code' => $subAccount->referral_code,
            'token_preview' => substr($token, 0, 20) . '...',
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully with primary sub-account',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                ],
                'sub_account' => [
                    'id' => $subAccount->id,
                    'name' => $subAccount->name,
                    'account_number' => $subAccount->account_number,
                    'referral_code' => $subAccount->referral_code,
                    'status' => $subAccount->status,
                    'is_primary' => $subAccount->is_primary,
                    'total_balance' => $subAccount->total_balance,
                    'sponsor_id' => $subAccount->referral_by_id,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        \Log::info('🔐 API LOGIN REQUEST STARTED', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'login_field' => $request->input('login'),
            'login_type' => filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone'
        ]);

        try {
            $validated = $request->validate([
                'login' => 'required|string', // Can be email or phone
                'password' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('❌ API LOGIN VALIDATION FAILED', [
                'errors' => $e->errors(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            throw $e;
        }

        $login = $validated['login'];
        $password = $validated['password'];

        // Determine if login is email or phone
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // Check if user exists with this email
            $user = User::where('email', $login)->first();
            if (!$user) {
                \Log::warning('❌ API LOGIN FAILED - Email not found', [
                    'email' => $login,
                    'ip' => $request->ip(),
                    'timestamp' => now()
                ]);
                throw ValidationException::withMessages([
                    'login' => ['No account found with this email address.'],
                ]);
            }
            // Login with email
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            // Handle phone number format variations
            $phone = $login;
            
            // If phone starts with 0, convert to +88 format
            if (str_starts_with($phone, '0')) {
                $phone = '+88' . $phone;
            }
            // If phone doesn't start with +, add +88
            elseif (!str_starts_with($phone, '+')) {
                $phone = '+88' . $phone;
            }
            
            \Log::info('📱 Phone number format conversion', [
                'original' => $login,
                'converted' => $phone,
                'timestamp' => now()
            ]);
            
            // Login with phone
            $credentials = ['phone' => $phone, 'password' => $password];
        }

        if (!Auth::attempt($credentials)) {
            \Log::warning('❌ API LOGIN FAILED - Invalid credentials', [
                'login_field' => $login,
                'login_type' => $isEmail ? 'email' : 'phone',
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        
        // Load user's primary sub-account
        $subAccount = $user->subAccount;
        
        if (!$subAccount) {
            \Log::warning('⚠️ API LOGIN - No primary sub-account found', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now()
            ]);
            
            // Create a primary sub-account if none exists (for existing users)
            $subAccount = SubAccount::create([
                'name' => $user->name . ' - Primary Account',
                'user_id' => $user->id,
                'account_number' => AccountNumberHelper::generateAccountNumber(),
                'referral_code' => AccountNumberHelper::generateReferralCode(),
                'direct_referral_count' => 0,
                'purchase_referral_count' => 0,
                'generation_count' => 0,
                'total_balance' => 0.00,
                'remaining_withdrawal_limit' => 0.00,
                'total_withdrawal' => 0.00,
                'total_package_purchase' => 0.00,
                'total_deposit' => 0.00,
                'total_sponsor_commission' => 0.00,
                'total_generation_commission' => 0.00,
                'total_auto_income' => 0.00,
                'status' => 'active',
                'is_primary' => true,
                'last_balance_update_at' => now(),
            ]);
            
            \Log::info('✅ API LOGIN - Created missing primary sub-account', [
                'user_id' => $user->id,
                'sub_account_id' => $subAccount->id,
                'account_number' => $subAccount->account_number,
                'timestamp' => now()
            ]);
        }

        $token = base64_encode($user->id . '|' . time() . '|' . \Illuminate\Support\Str::random(32));

        \Log::info('✅ API LOGIN SUCCESS', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_phone' => $user->phone,
            'sub_account_id' => $subAccount->id,
            'account_number' => $subAccount->account_number,
            'referral_code' => $subAccount->referral_code,
            'total_balance' => $subAccount->total_balance,
            'login_type' => $isEmail ? 'email' : 'phone',
            'token_preview' => substr($token, 0, 20) . '...',
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'last_login_at' => now(),
                ],
                'sub_account' => [
                    'id' => $subAccount->id,
                    'name' => $subAccount->name,
                    'account_number' => $subAccount->account_number,
                    'referral_code' => $subAccount->referral_code,
                    'status' => $subAccount->status,
                    'is_primary' => $subAccount->is_primary,
                    'total_balance' => $subAccount->total_balance,
                    'remaining_withdrawal_limit' => $subAccount->remaining_withdrawal_limit,
                    'total_deposit' => $subAccount->total_deposit,
                    'total_withdrawal' => $subAccount->total_withdrawal,
                    'total_package_purchase' => $subAccount->total_package_purchase,
                    'direct_referral_count' => $subAccount->direct_referral_count,
                    'sponsor_id' => $subAccount->referral_by_id,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        \Log::info('🚪 API LOGOUT REQUEST', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);

        // Simple token logout - just return success

        \Log::info('✅ API LOGOUT SUCCESS', [
            'user_id' => $user->id,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
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
