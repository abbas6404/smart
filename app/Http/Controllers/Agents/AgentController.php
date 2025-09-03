<?php

namespace App\Http\Controllers\Agents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Transaction;

class AgentController extends Controller
{
    /**
     * Show agent dashboard
     */
    public function dashboard()
    {
        $agent = Auth::guard('agent')->user();
        
        // Get basic statistics
        $stats = [
            'total_users' => User::count(),
            'total_balance' => $agent->total_balance,
            'available_balance' => $agent->getAvailableBalance(),
            'total_credit' => $agent->total_credit,
            'total_debit' => $agent->total_debit,
        ];

        return view('agents.dashboard.index', compact('stats'));
    }

    /**
     * Show agent profile
     */
    public function profile()
    {
        $agent = Auth::guard('agent')->user();
        return view('agents.profile.index', compact('agent'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $agent = Auth::guard('agent')->user();
        return view('agents.profile.edit', compact('agent'));
    }

    /**
     * Update agent profile
     */
    public function updateProfile(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:agents,phone,' . $agent->id,
            'email' => 'nullable|string|email|max:255|unique:agents,email,' . $agent->id,
            'address' => 'nullable|string|max:500',
        ]);

        $agent->update($request->only(['name', 'phone', 'email', 'address']));

        return redirect()->route('agent.profile.index')
                        ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update agent password
     */
    public function updatePassword(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $agent->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $agent->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('agent.profile.index')
                        ->with('success', 'Password updated successfully.');
    }

    /**
     * Show financial dashboard
     */
    public function financial()
    {
        $agent = Auth::guard('agent')->user();
        
        $stats = [
            'total_balance' => $agent->total_balance,
            'available_balance' => $agent->getAvailableBalance(),
            'safety_balance' => $agent->safety_balance,
            'total_credit' => $agent->total_credit,
            'total_debit' => $agent->total_debit,
        ];

        return view('agents.financial.index', compact('stats'));
    }

    /**
     * Show transactions
     */
    public function transactions()
    {
        $agent = Auth::guard('agent')->user();
        
        // Get recent transactions (you'll need to implement this based on your transaction structure)
        $transactions = collect(); // Placeholder

        return view('agents.financial.transactions', compact('transactions'));
    }

    /**
     * Show balance details
     */
    public function balance()
    {
        $agent = Auth::guard('agent')->user();
        return view('agents.financial.balance', compact('agent'));
    }

    /**
     * Process withdrawal request
     */
    public function withdraw(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $agent->getAvailableBalance(),
            'description' => 'nullable|string|max:500',
        ]);

        // Process withdrawal logic here
        // This would typically create a withdrawal request

        return redirect()->route('agent.financial.index')
                        ->with('success', 'Withdrawal request submitted successfully.');
    }

    /**
     * Show users managed by this agent
     */
    public function users()
    {
        $agent = Auth::guard('agent')->user();
        
        // Get users managed by this agent (you'll need to implement this relationship)
        $users = collect(); // Placeholder

        return view('agents.users.index', compact('users'));
    }

    /**
     * Show specific user details
     */
    public function showUser(User $user)
    {
        return view('agents.users.show', compact('user'));
    }

    /**
     * Show user transactions
     */
    public function userTransactions(User $user)
    {
        // Get user transactions (you'll need to implement this)
        $transactions = collect(); // Placeholder

        return view('agents.users.transactions', compact('user', 'transactions'));
    }

    /**
     * Show reports
     */
    public function reports()
    {
        return view('agents.reports.index');
    }

    /**
     * Show financial report
     */
    public function financialReport()
    {
        $agent = Auth::guard('agent')->user();
        
        // Generate financial report data
        $reportData = [
            'agent' => $agent,
            'period' => 'Last 30 days', // You can make this dynamic
        ];

        return view('agents.reports.financial', compact('reportData'));
    }

    /**
     * Show users report
     */
    public function usersReport()
    {
        // Generate users report data
        $reportData = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
        ];

        return view('agents.reports.users', compact('reportData'));
    }

    /**
     * Export report
     */
    public function exportReport($type)
    {
        // Implement report export logic
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Show settings
     */
    public function settings()
    {
        $agent = Auth::guard('agent')->user();
        return view('agents.settings.index', compact('agent'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $request->validate([
            'safety_balance' => 'nullable|numeric|min:0|max:' . $agent->total_balance,
        ]);

        if ($request->has('safety_balance')) {
            $agent->update(['safety_balance' => $request->safety_balance]);
        }

        return redirect()->route('agent.settings.index')
                        ->with('success', 'Settings updated successfully.');
    }
}
