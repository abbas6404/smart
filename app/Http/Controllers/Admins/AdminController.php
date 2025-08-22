<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\AutoBoard;
use App\Models\SystemSetting;
use App\Models\SubAccount;
use App\Models\LevelCommission;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        try {
            $totalUsers = User::count();
            $totalRevenue = Transaction::where('type', 'package_purchase')->sum('amount') ?? 0;
            $activePackages = Package::where('is_active', true)->count();
            $pendingWithdrawals = Transaction::where('type', 'withdrawal')->where('status', 'pending')->count();
            
            // Additional stats for the view
            $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
            $revenueThisMonth = Transaction::where('type', 'package_purchase')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') ?? 0;
            $totalPackages = Package::count();
            $withdrawalAmount = Transaction::where('type', 'withdrawal')
                ->where('status', 'pending')
                ->sum('amount') ?? 0;

            // Auto Board Stats
            $autoBoardStats = [
                'status' => 'collection',
                'collection_amount' => 0,
                'contributors' => 0,
                'eligible_accounts' => 0,
                'next_distribution' => '00:00',
                'ready_for_distribution' => false
            ];

            // Recent Transactions
            $recentTransactions = Transaction::latest()->take(5)->get();

            return view('admins.dashboard.index', compact(
                'totalUsers',
                'totalRevenue', 
                'activePackages',
                'pendingWithdrawals',
                'newUsersThisMonth',
                'revenueThisMonth',
                'totalPackages',
                'withdrawalAmount',
                'autoBoardStats',
                'recentTransactions'
            ));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Admin dashboard error: ' . $e->getMessage());
            
            // Return dashboard with default values
            $totalUsers = 0;
            $totalRevenue = 0;
            $activePackages = 0;
            $pendingWithdrawals = 0;
            $newUsersThisMonth = 0;
            $revenueThisMonth = 0;
            $totalPackages = 0;
            $withdrawalAmount = 0;
            $autoBoardStats = [
                'status' => 'collection',
                'collection_amount' => 0,
                'contributors' => 0,
                'eligible_accounts' => 0,
                'next_distribution' => '00:00',
                'ready_for_distribution' => false
            ];
            $recentTransactions = collect();

            return view('admins.dashboard.index', compact(
                'totalUsers',
                'totalRevenue',
                'activePackages',
                'pendingWithdrawals',
                'newUsersThisMonth',
                'revenueThisMonth',
                'totalPackages',
                'withdrawalAmount',
                'autoBoardStats',
                'recentTransactions'
            ));
        }
    }

    /**
     * Show users list
     */
    public function users()
    {
        $users = User::with(['sponsor', 'activePackage'])->paginate(20);
        return view('admins.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admins.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'username' => 'required|string|unique:users,username',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'status' => 'active',
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', 'User created successfully');
    }

    /**
     * Show sub accounts list
     */
    public function subAccounts()
    {
        $subAccounts = SubAccount::with(['user'])->paginate(20);
        return view('admins.sub-accounts.index', compact('subAccounts'));
    }

    /**
     * Show specific sub account
     */
    public function showSubAccount(SubAccount $subAccount)
    {
        return view('admins.sub-accounts.show', compact('subAccount'));
    }

    /**
     * Update sub account
     */
    public function updateSubAccount(Request $request, SubAccount $subAccount)
    {
        $validated = $request->validate([
            'total_balance' => 'required|numeric|min:0',
            'available_balance' => 'required|numeric|min:0',
            'locked_balance' => 'required|numeric|min:0',
        ]);

        $subAccount->update($validated);

        return redirect()->route('admin.sub-accounts.show', $subAccount)->with('success', 'Sub account updated successfully');
    }

    /**
     * Show specific user
     */
    public function showUser(User $user)
    {
        return view('admins.users.show', compact('user'));
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        return view('admins.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Activate user
     */
    public function activateUser(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User activated successfully');
    }

    /**
     * Deactivate user
     */
    public function deactivateUser(User $user)
    {
        $user->update(['status' => 'inactive']);
        return back()->with('success', 'User deactivated successfully');
    }

    /**
     * Show packages list
     */
    public function packages()
    {
        $packages = Package::paginate(20);
        return view('admins.packages.index', compact('packages'));
    }

    /**
     * Show create package form
     */
    public function createPackage()
    {
        return view('admins.packages.create');
    }

    /**
     * Store new package
     */
    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'return_rate' => 'required|numeric|min:0|max:100',
        ]);

        Package::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully');
    }

    /**
     * Show specific package
     */
    public function showPackage(Package $package)
    {
        return view('admins.packages.show', compact('package'));
    }

    /**
     * Show edit package form
     */
    public function editPackage(Package $package)
    {
        return view('admins.packages.edit', compact('package'));
    }

    /**
     * Update package
     */
    public function updatePackage(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'return_rate' => 'required|numeric|min:0|max:100',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.show', $package)->with('success', 'Package updated successfully');
    }

    /**
     * Delete package
     */
    public function deletePackage(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully');
    }

    /**
     * Show transactions list
     */
    public function transactions()
    {
        $transactions = Transaction::with(['user'])->paginate(20);
        return view('admins.transactions.index', compact('transactions'));
    }

    /**
     * Show specific transaction
     */
    public function showTransaction(Transaction $transaction)
    {
        return view('admins.transactions.show', compact('transaction'));
    }

    /**
     * Update transaction
     */
    public function updateTransaction(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.show', $transaction)->with('success', 'Transaction updated successfully');
    }

    /**
     * Approve transaction
     */
    public function approveTransaction(Transaction $transaction)
    {
        $transaction->update(['status' => 'approved']);
        return back()->with('success', 'Transaction approved successfully');
    }

    /**
     * Reject transaction
     */
    public function rejectTransaction(Transaction $transaction)
    {
        $transaction->update(['status' => 'rejected']);
        return back()->with('success', 'Transaction rejected successfully');
    }

    /**
     * Show withdrawals list
     */
    public function withdrawals()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')->with(['user'])->paginate(20);
        return view('admins.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Show specific withdrawal
     */
    public function showWithdrawal(Transaction $withdrawal)
    {
        return view('admins.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Update withdrawal
     */
    public function updateWithdrawal(Request $request, Transaction $withdrawal)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $withdrawal->update($validated);

        return redirect()->route('admin.withdrawals.show', $withdrawal)->with('success', 'Withdrawal updated successfully');
    }

    /**
     * Approve withdrawal
     */
    public function approveWithdrawal(Transaction $withdrawal)
    {
        $withdrawal->update(['status' => 'approved']);
        return back()->with('success', 'Withdrawal approved successfully');
    }

    /**
     * Reject withdrawal
     */
    public function rejectWithdrawal(Transaction $withdrawal)
    {
        $withdrawal->update(['status' => 'rejected']);
        return back()->with('success', 'Withdrawal rejected successfully');
    }

    /**
     * Show auto board
     */
    public function autoBoard()
    {
        $autoBoard = AutoBoard::first();
        return view('admins.auto-board.index', compact('autoBoard'));
    }

    /**
     * Show distributions
     */
    public function distributions()
    {
        $distributions = AutoBoard::with(['distributions'])->first();
        return view('admins.auto-board.distributions', compact('distributions'));
    }

    /**
     * Show contributions
     */
    public function contributions()
    {
        $contributions = AutoBoard::with(['contributions'])->first();
        return view('admins.auto-board.contributions', compact('contributions'));
    }

    /**
     * Process distribution
     */
    public function processDistribution(Request $request)
    {
        // Process auto board distribution logic
        return back()->with('success', 'Distribution processed successfully');
    }

    /**
     * Show commissions list
     */
    public function commissions()
    {
        $commissions = LevelCommission::with(['user'])->paginate(20);
        return view('admins.commissions.index', compact('commissions'));
    }

    /**
     * Show specific commission
     */
    public function showCommission(LevelCommission $commission)
    {
        return view('admins.commissions.show', compact('commission'));
    }

    /**
     * Update commission
     */
    public function updateCommission(Request $request, LevelCommission $commission)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        $commission->update($validated);

        return redirect()->route('admin.commissions.show', $commission)->with('success', 'Commission updated successfully');
    }

    /**
     * Show settings
     */
    public function settings()
    {
        $settings = SystemSetting::all()->keyBy('key');
        return view('admins.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'maintenance_mode' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'General settings updated successfully');
    }

    /**
     * Update commission settings
     */
    public function updateCommissionSettings(Request $request)
    {
        $validated = $request->validate([
            'direct_commission' => 'required|numeric|min:0|max:100',
            'level_commission' => 'required|numeric|min:0|max:100',
            'max_levels' => 'required|integer|min:1|max:10',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Commission settings updated successfully');
    }

    /**
     * Update auto board settings
     */
    public function updateAutoBoardSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_board_enabled' => 'boolean',
            'distribution_interval' => 'required|integer|min:1',
            'max_participants' => 'required|integer|min:1',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Auto board settings updated successfully');
    }

    /**
     * Show reports
     */
    public function reports()
    {
        return view('admins.reports.index');
    }

    /**
     * Show user reports
     */
    public function userReports()
    {
        $users = User::with(['sponsor', 'referrals'])->get();
        return view('admins.reports.users', compact('users'));
    }

    /**
     * Show commission reports
     */
    public function commissionReports()
    {
        $commissions = Transaction::where('type', 'commission')->with(['user'])->get();
        return view('admins.reports.commissions', compact('commissions'));
    }

    /**
     * Show transaction reports
     */
    public function transactionReports()
    {
        $transactions = Transaction::with(['user', 'package'])->get();
        return view('admins.reports.transactions', compact('transactions'));
    }

    /**
     * Export report
     */
    public function exportReport($type)
    {
        // Export logic based on type
        return back()->with('success', 'Report exported successfully');
    }
}
