<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubAccount;
use App\Helpers\AccountNumberHelper;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Show account management page
     */
    public function index()
    {
        $user = Auth::user();
        $currentAccount = $user->getCurrentSubAccount();
        $allAccounts = $user->getAllSubAccounts();
        
        return view('users.accounts.index', compact('currentAccount', 'allAccounts'));
    }

    /**
     * Show account switching page
     */
    public function switch()
    {
        $user = Auth::user();
        $currentAccount = $user->getCurrentSubAccount();
        $allAccounts = $user->getAllSubAccounts();
        
        return view('users.accounts.switch', compact('currentAccount', 'allAccounts'));
    }

    /**
     * Switch to a different sub account
     */
    public function switchAccount(Request $request)
    {
        $request->validate([
            'sub_account_id' => 'required|exists:sub_accounts,id'
        ]);

        $user = Auth::user();
        $subAccount = $user->subAccounts()->find($request->sub_account_id);
        
        if (!$subAccount) {
            return back()->with('error', 'Account not found or access denied.');
        }

        if ($user->switchToSubAccount($subAccount->id)) {
            return redirect()->route('user.dashboard.index')
                ->with('success', "Switched to account: {$subAccount->name}");
        }

        return back()->with('error', 'Failed to switch account.');
    }

    /**
     * Show create new account form
     */
    public function create()
    {
        $user = Auth::user();
        $currentAccount = $user->getCurrentSubAccount();
        
        return view('users.accounts.create', compact('currentAccount'));
    }

    /**
     * Store new sub account
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'referral_code' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        try {
            $newAccount = $user->createNewSubAccount($request->name);
            
            // Set sponsor if referral code provided
            if ($request->referral_code) {
                $this->setSponsorForAccount($newAccount, $request->referral_code);
            }

            return redirect()->route('user.accounts.index')
                ->with('success', "New account '{$newAccount->name}' created successfully!");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create account. Please try again.');
        }
    }

    /**
     * Show specific account details
     */
    public function show(SubAccount $subAccount)
    {
        $user = Auth::user();
        
        // Ensure user owns this account
        if ($subAccount->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $currentAccount = $user->getCurrentSubAccount();
        
        return view('users.accounts.show', compact('subAccount', 'currentAccount'));
    }

    /**
     * Set account as primary
     */
    public function setPrimary(Request $request, SubAccount $subAccount)
    {
        $user = Auth::user();
        
        // Ensure user owns this account
        if ($subAccount->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Remove primary from all other accounts
        $user->subAccounts()->update(['is_primary' => false]);
        
        // Set this account as primary
        $subAccount->update(['is_primary' => true]);
        
        // Clear session and use primary account
        session()->forget('current_sub_account_id');
        
        return redirect()->route('user.accounts.index')
            ->with('success', "Account '{$subAccount->name}' is now your primary account.");
    }

    /**
     * Delete sub account (only if not primary and no active packages)
     */
    public function destroy(SubAccount $subAccount)
    {
        $user = Auth::user();
        
        // Ensure user owns this account
        if ($subAccount->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Prevent deletion of primary account
        if ($subAccount->is_primary) {
            return back()->with('error', 'Cannot delete primary account.');
        }

        // Prevent deletion if account has active package
        if ($subAccount->active_package_id) {
            return back()->with('error', 'Cannot delete account with active package.');
        }

        // Prevent deletion if account has balance
        if ($subAccount->total_balance > 0) {
            return back()->with('error', 'Cannot delete account with balance. Please withdraw funds first.');
        }

        $accountName = $subAccount->name;
        $subAccount->delete();

        return redirect()->route('user.accounts.index')
            ->with('success', "Account '{$accountName}' deleted successfully.");
    }

    /**
     * Show referral performance for current account
     */
    public function referrals()
    {
        $user = Auth::user();
        $currentAccount = $user->getCurrentSubAccount();
        
        if (!$currentAccount) {
            return redirect()->route('user.accounts.index')
                ->with('error', 'No active account found.');
        }

        $referralStats = $currentAccount->getReferralStats();
        $topReferrals = $currentAccount->getTopReferrals(5);
        $recentActivities = $currentAccount->getRecentReferralActivities(10);
        
        return view('users.accounts.referrals', compact(
            'currentAccount', 
            'referralStats', 
            'topReferrals', 
            'recentActivities'
        ));
    }

    /**
     * Helper method to set sponsor for new account
     */
    private function setSponsorForAccount(SubAccount $account, string $referralInput)
    {
        try {
            // Try to find sponsor by referral code first
            $sponsor = SubAccount::where('referral_code', $referralInput)->first();
            
            // If not found by referral code, try by account number
            if (!$sponsor) {
                $sponsor = SubAccount::where('account_number', $referralInput)->first();
            }
            
            if ($sponsor && $sponsor->id !== $account->id) {
                $account->update(['referral_by_id' => $sponsor->id]);
                $sponsor->increment('direct_referral_count');
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to set sponsor for new account', [
                'account_id' => $account->id,
                'referral_input' => $referralInput,
                'error' => $e->getMessage()
            ]);
        }
    }
}
