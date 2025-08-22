<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\AutoBoard;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $currentAccount = $user->getCurrentSubAccount();
        
        $stats = [
            'total_balance' => $currentAccount?->total_balance ?? 0,
            'direct_referrals' => $currentAccount?->direct_referral_count ?? 0,
            'total_commissions' => ($currentAccount?->total_sponsor_commission ?? 0) + ($currentAccount?->total_generation_commission ?? 0),
            'auto_income' => $currentAccount?->total_auto_income ?? 0,
        ];

        $recentTransactions = $user->transactions()->latest()->take(5)->get();
        $autoBoardStats = $user->autoBoardStatus();

        return view('users.dashboard.index', compact('user', 'currentAccount', 'stats', 'recentTransactions', 'autoBoardStats'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load(['sponsor', 'referrals', 'activePackage']);
        
        return view('users.profile.index', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('users.profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        return redirect()->route('user.profile.index')->with('success', 'Profile updated successfully');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'two_factor_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        $user->update($validated);

        return back()->with('success', 'Security settings updated successfully');
    }

    /**
     * Show available packages
     */
    public function packages()
    {
        $packages = Package::where('is_active', true)->get();
        return view('users.packages.index', compact('packages'));
    }

    /**
     * Show specific package
     */
    public function showPackage(Package $package)
    {
        return view('users.packages.show', compact('package'));
    }

    /**
     * Purchase package
     */
    public function purchasePackage(Request $request, Package $package)
    {
        $user = Auth::user();
        
        if (($user->subAccount?->total_balance ?? 0) < $package->amount) {
            return back()->with('error', 'Insufficient balance');
        }

        // Create package purchase transaction
        Transaction::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'type' => 'package_purchase',
            'amount' => $package->amount,
            'status' => 'pending',
        ]);

        // Deduct balance
        $user->subAccount->decrement('total_balance', $package->amount);

        return redirect()->route('user.packages.my')->with('success', 'Package purchased successfully');
    }

    /**
     * Show user's packages
     */
    public function myPackages()
    {
        $user = Auth::user();
        $purchases = $user->packagePurchases()->with('package')->paginate(10);
        
        return view('users.packages.my', compact('purchases'));
    }

    /**
     * Show specific package purchase
     */
    public function showMyPackage($purchase)
    {
        $purchase = Auth::user()->packagePurchases()->with('package')->findOrFail($purchase);
        return view('users.packages.my-show', compact('purchase'));
    }

    /**
     * Show referrals
     */
    public function referrals()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->with('activePackage')->paginate(10);
        
        return view('users.referrals.index', compact('referrals'));
    }

    /**
     * Show network
     */
    public function network()
    {
        $user = Auth::user();
        $network = $user->getNetwork();
        
        return view('users.referrals.network', compact('network'));
    }

    /**
     * Show tree view
     */
    public function tree()
    {
        $user = Auth::user();
        $tree = $user->getTree();
        
        return view('users.referrals.tree', compact('tree'));
    }

    /**
     * Send invite
     */
    public function sendInvite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        // Send invite logic
        // This could send an email with referral link

        return back()->with('success', 'Invite sent successfully');
    }

    /**
     * Show commissions
     */
    public function commissions()
    {
        $user = Auth::user();
        $commissions = $user->commissions()->latest()->paginate(15);
        
        return view('users.commissions.index', compact('commissions'));
    }

    /**
     * Show commission history
     */
    public function commissionHistory()
    {
        $user = Auth::user();
        $commissions = $user->commissions()->latest()->paginate(20);
        
        return view('users.commissions.history', compact('commissions'));
    }

    /**
     * Show commission summary
     */
    public function commissionSummary()
    {
        $user = Auth::user();
        $summary = [
            'total_earned' => $user->commissions()->sum('amount'),
            'this_month' => $user->commissions()->whereMonth('created_at', now()->month)->sum('amount'),
            'this_year' => $user->commissions()->whereYear('created_at', now()->year)->sum('amount'),
            'pending' => $user->commissions()->where('status', 'pending')->sum('amount'),
        ];
        
        return view('users.commissions.summary', compact('summary'));
    }

    /**
     * Show withdrawal form
     */
    public function withdrawCommission()
    {
        $user = Auth::user();
        $availableBalance = $user->commissions()->where('status', 'approved')->sum('amount');
        
        return view('users.commissions.withdraw', compact('availableBalance'));
    }

    /**
     * Process withdrawal
     */
    public function processWithdrawal(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank,paypal,crypto',
            'payment_details' => 'required|string',
        ]);

        if ($request->amount > ($user->subAccount?->total_balance ?? 0)) {
            return back()->with('error', 'Insufficient balance');
        }

        // Create withdrawal transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
        ]);

        // Deduct balance
        $user->subAccount->decrement('total_balance', $request->amount);

        return redirect()->route('user.transactions.withdrawals')->with('success', 'Withdrawal request submitted successfully');
    }

    /**
     * Show auto board
     */
    public function autoBoard()
    {
        $user = Auth::user();
        $autoBoard = AutoBoard::first();
        $userStatus = $user->autoBoardStatus();
        
        return view('users.auto-board.index', compact('autoBoard', 'userStatus'));
    }

    /**
     * Show auto board status
     */
    public function autoBoardStatus()
    {
        $user = Auth::user();
        $status = $user->autoBoardStatus();
        
        return view('users.auto-board.status', compact('status'));
    }

    /**
     * Show auto board earnings
     */
    public function autoBoardEarnings()
    {
        $user = Auth::user();
        $earnings = $user->autoBoardIncome()->latest()->paginate(15);
        
        return view('users.auto-board.earnings', compact('earnings'));
    }

    /**
     * Join auto board
     */
    public function joinAutoBoard(Request $request)
    {
        $user = Auth::user();
        
        if ($user->autoBoardStatus()->is_active) {
            return back()->with('error', 'Already a member of auto board');
        }

        // Join auto board logic
        $user->joinAutoBoard();

        return back()->with('success', 'Successfully joined auto board');
    }

    /**
     * Leave auto board
     */
    public function leaveAutoBoard(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->autoBoardStatus()->is_active) {
            return back()->with('error', 'Not a member of auto board');
        }

        // Leave auto board logic
        $user->leaveAutoBoard();

        return back()->with('success', 'Successfully left auto board');
    }

    /**
     * Show transactions
     */
    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->paginate(15);
        
        return view('users.transactions.index', compact('transactions'));
    }

    /**
     * Show specific transaction
     */
    public function showTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('users.transactions.show', compact('transaction'));
    }

    /**
     * Show deposits
     */
    public function deposits()
    {
        $user = Auth::user();
        $deposits = $user->transactions()->where('type', 'deposit')->latest()->paginate(15);
        
        return view('users.transactions.deposits', compact('deposits'));
    }

    /**
     * Show withdrawals
     */
    public function withdrawals()
    {
        $user = Auth::user();
        $withdrawals = $user->transactions()->where('type', 'withdrawal')->latest()->paginate(15);
        
        return view('users.transactions.withdrawals', compact('withdrawals'));
    }

    /**
     * Create deposit
     */
    public function createDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank,paypal,crypto',
        ]);

        $user = Auth::user();

        // Create deposit transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('user.transactions.deposits')->with('success', 'Deposit request submitted successfully');
    }

    /**
     * Create withdrawal
     */
    public function createWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank,paypal,crypto',
            'payment_details' => 'required|string',
        ]);

        $user = Auth::user();
        
        if ($request->amount > ($user->subAccount?->total_balance ?? 0)) {
            return back()->with('error', 'Insufficient balance');
        }

        // Create withdrawal transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
        ]);

        // Deduct balance
        $user->subAccount->decrement('total_balance', $request->amount);

        return redirect()->route('user.transactions.withdrawals')->with('success', 'Withdrawal request submitted successfully');
    }

    /**
     * Show support
     */
    public function support()
    {
        return view('users.support.index');
    }

    /**
     * Show tickets
     */
    public function tickets()
    {
        $user = Auth::user();
        $tickets = $user->supportTickets()->latest()->paginate(10);
        
        return view('users.support.tickets', compact('tickets'));
    }

    /**
     * Show create ticket form
     */
    public function createTicket()
    {
        return view('users.support.create');
    }

    /**
     * Store ticket
     */
    public function storeTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $user = Auth::user();
        
        // Create support ticket logic
        // This would create a ticket in the support_tickets table

        return redirect()->route('user.support.tickets')->with('success', 'Ticket created successfully');
    }

    /**
     * Show ticket
     */
    public function showTicket($ticket)
    {
        $user = Auth::user();
        $ticket = $user->supportTickets()->findOrFail($ticket);
        
        return view('users.support.show', compact('ticket'));
    }

    /**
     * Reply to ticket
     */
    public function replyTicket(Request $request, $ticket)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $ticket = $user->supportTickets()->findOrFail($ticket);
        
        // Add reply logic
        // This would add a reply to the ticket

        return back()->with('success', 'Reply sent successfully');
    }

    /**
     * Show notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        
        return view('users.notifications.index', compact('notifications'));
    }

    /**
     * Show specific notification
     */
    public function showNotification($notification)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notification);
        
        return view('users.notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notification)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notification);
        
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read');
    }
}
