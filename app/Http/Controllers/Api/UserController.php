<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\AutoBoard;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load(['sponsor', 'referrals', 'activePackage']);
        
        return response()->json([
            'user' => $user
        ]);
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

        return response()->json([
            'user' => $user,
            'message' => 'Profile updated successfully'
        ]);
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

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }

    /**
     * Get user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [
            'total_balance' => $user->subAccount?->total_balance ?? 0,
            'direct_referrals' => $user->referrals()->count(),
            'total_commissions' => $user->commissions()->sum('amount'),
            'auto_income' => $user->autoBoardIncome()->sum('amount'),
        ];

        return response()->json([
            'stats' => $stats
        ]);
    }

    /**
     * Get dashboard stats
     */
    public function stats()
    {
        $user = Auth::user();
        $stats = [
            'total_balance' => $user->subAccount?->total_balance ?? 0,
            'direct_referrals' => $user->referrals()->count(),
            'total_commissions' => $user->commissions()->sum('amount'),
            'auto_income' => $user->autoBoardIncome()->sum('amount'),
        ];

        return response()->json([
            'stats' => $stats
        ]);
    }

    /**
     * Get user's packages
     */
    public function myPackages()
    {
        $user = Auth::user();
        $purchases = $user->packagePurchases()->with('package')->paginate(10);
        
        return response()->json([
            'purchases' => $purchases
        ]);
    }

    /**
     * Purchase package
     */
    public function purchasePackage(Request $request, Package $package)
    {
        $user = Auth::user();
        
        if (($user->subAccount?->total_balance ?? 0) < $package->amount) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
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

        return response()->json([
            'message' => 'Package purchased successfully'
        ]);
    }

    /**
     * Get specific package purchase
     */
    public function showMyPackage($purchase)
    {
        $user = Auth::user();
        $purchase = $user->packagePurchases()->with('package')->findOrFail($purchase);
        
        return response()->json([
            'purchase' => $purchase
        ]);
    }

    /**
     * Get user referrals
     */
    public function referrals()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->with('activePackage')->paginate(10);
        
        return response()->json([
            'referrals' => $referrals
        ]);
    }

    /**
     * Get user network
     */
    public function network()
    {
        $user = Auth::user();
        $network = $user->getNetwork();
        
        return response()->json([
            'network' => $network
        ]);
    }

    /**
     * Get user tree
     */
    public function tree()
    {
        $user = Auth::user();
        $tree = $user->getTree();
        
        return response()->json([
            'tree' => $tree
        ]);
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

        return response()->json([
            'message' => 'Invite sent successfully'
        ]);
    }

    /**
     * Get user commissions
     */
    public function commissions()
    {
        $user = Auth::user();
        $commissions = $user->commissions()->latest()->paginate(15);
        
        return response()->json([
            'commissions' => $commissions
        ]);
    }

    /**
     * Get commission history
     */
    public function commissionHistory()
    {
        $user = Auth::user();
        $commissions = $user->commissions()->latest()->paginate(20);
        
        return response()->json([
            'commissions' => $commissions
        ]);
    }

    /**
     * Get commission summary
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
        
        return response()->json([
            'summary' => $summary
        ]);
    }

    /**
     * Withdraw commission
     */
    public function withdrawCommission(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bank,paypal,crypto',
            'payment_details' => 'required|string',
        ]);

        if ($request->amount > ($user->subAccount?->total_balance ?? 0)) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
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

        return response()->json([
            'message' => 'Withdrawal request submitted successfully'
        ]);
    }

    /**
     * Get auto board
     */
    public function autoBoard()
    {
        $user = Auth::user();
        $autoBoard = AutoBoard::first();
        $userStatus = $user->autoBoardStatus();
        
        return response()->json([
            'auto_board' => $autoBoard,
            'user_status' => $userStatus
        ]);
    }

    /**
     * Get auto board status
     */
    public function autoBoardStatus()
    {
        $user = Auth::user();
        $status = $user->autoBoardStatus();
        
        return response()->json([
            'status' => $status
        ]);
    }

    /**
     * Get auto board earnings
     */
    public function autoBoardEarnings()
    {
        $user = Auth::user();
        $earnings = $user->autoBoardIncome()->latest()->paginate(15);
        
        return response()->json([
            'earnings' => $earnings
        ]);
    }

    /**
     * Join auto board
     */
    public function joinAutoBoard(Request $request)
    {
        $user = Auth::user();
        
        if ($user->autoBoardStatus()->is_active) {
            return response()->json([
                'message' => 'Already a member of auto board'
            ], 400);
        }

        // Join auto board logic
        $user->joinAutoBoard();

        return response()->json([
            'message' => 'Successfully joined auto board'
        ]);
    }

    /**
     * Leave auto board
     */
    public function leaveAutoBoard(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->autoBoardStatus()->is_active) {
            return response()->json([
                'message' => 'Not a member of auto board'
            ], 400);
        }

        // Leave auto board logic
        $user->leaveAutoBoard();

        return response()->json([
            'message' => 'Successfully left auto board'
        ]);
    }

    /**
     * Get user transactions
     */
    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->latest()->paginate(15);
        
        return response()->json([
            'transactions' => $transactions
        ]);
    }

    /**
     * Get specific transaction
     */
    public function showTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        return response()->json([
            'transaction' => $transaction
        ]);
    }

    /**
     * Get deposits
     */
    public function deposits()
    {
        $user = Auth::user();
        $deposits = $user->transactions()->where('type', 'deposit')->latest()->paginate(15);
        
        return response()->json([
            'deposits' => $deposits
        ]);
    }

    /**
     * Get withdrawals
     */
    public function withdrawals()
    {
        $user = Auth::user();
        $withdrawals = $user->transactions()->where('type', 'withdrawal')->latest()->paginate(15);
        
        return response()->json([
            'withdrawals' => $withdrawals
        ]);
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

        return response()->json([
            'message' => 'Deposit request submitted successfully'
        ]);
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
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
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

        return response()->json([
            'message' => 'Withdrawal request submitted successfully'
        ]);
    }

    /**
     * Get support
     */
    public function support()
    {
        return response()->json([
            'message' => 'Support system'
        ]);
    }

    /**
     * Get tickets
     */
    public function tickets()
    {
        $user = Auth::user();
        $tickets = $user->supportTickets()->latest()->paginate(10);
        
        return response()->json([
            'tickets' => $tickets
        ]);
    }

    /**
     * Create ticket
     */
    public function createTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $user = Auth::user();
        
        // Create support ticket logic
        // This would create a ticket in the support_tickets table

        return response()->json([
            'message' => 'Ticket created successfully'
        ]);
    }

    /**
     * Get ticket
     */
    public function showTicket($ticket)
    {
        $user = Auth::user();
        $ticket = $user->supportTickets()->findOrFail($ticket);
        
        return response()->json([
            'ticket' => $ticket
        ]);
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

        return response()->json([
            'message' => 'Reply sent successfully'
        ]);
    }

    /**
     * Get notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        
        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Get specific notification
     */
    public function showNotification($notification)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notification);
        
        return response()->json([
            'notification' => $notification
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notification)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notification);
        
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }
}
