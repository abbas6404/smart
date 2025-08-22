<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AutoBoardController;
use App\Http\Controllers\Api\PurchaseReferralController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
// Note: Login supports both phone number and email using the 'login' field
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{package}', [PackageController::class, 'show']);

// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::delete('/user', [UserController::class, 'deleteAccount']);
    
    // User Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::get('/dashboard/stats', [UserController::class, 'stats']);
    
    // Investment Packages
    Route::get('/user/packages', [UserController::class, 'myPackages']);
    Route::post('/packages/{package}/purchase', [UserController::class, 'purchasePackage']);
    Route::get('/user/packages/{purchase}', [UserController::class, 'showMyPackage']);
    
    // Referrals
    Route::get('/user/referrals', [UserController::class, 'referrals']);
    Route::get('/user/network', [UserController::class, 'network']);
    Route::get('/user/tree', [UserController::class, 'tree']);
    Route::post('/user/referrals/invite', [UserController::class, 'sendInvite']);
    
    // Commissions
    Route::get('/user/commissions', [UserController::class, 'commissions']);
    Route::get('/user/commissions/history', [UserController::class, 'commissionHistory']);
    Route::get('/user/commissions/summary', [UserController::class, 'commissionSummary']);
    Route::post('/user/commissions/withdraw', [UserController::class, 'withdrawCommission']);
    
    // Auto Board
    Route::get('/user/auto-board', [UserController::class, 'autoBoard']);
    Route::get('/user/auto-board/status', [UserController::class, 'autoBoardStatus']);
    Route::get('/user/auto-board/earnings', [UserController::class, 'autoBoardEarnings']);
    Route::post('/user/auto-board/join', [UserController::class, 'joinAutoBoard']);
    Route::post('/user/auto-board/leave', [UserController::class, 'leaveAutoBoard']);
    
    // Transactions
    Route::get('/user/transactions', [UserController::class, 'transactions']);
    Route::get('/user/transactions/{transaction}', [UserController::class, 'showTransaction']);
    Route::get('/user/transactions/deposits', [UserController::class, 'deposits']);
    Route::get('/user/transactions/withdrawals', [UserController::class, 'withdrawals']);
    Route::post('/user/transactions/deposit', [UserController::class, 'createDeposit']);
    Route::post('/user/transactions/withdrawal', [UserController::class, 'createWithdrawal']);
    
    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [UserController::class, 'support'])->name('index');
        Route::get('/tickets', [UserController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/create', [UserController::class, 'createTicket'])->name('tickets.create');
        Route::post('/tickets', [UserController::class, 'storeTicket'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [UserController::class, 'showTicket'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [UserController::class, 'replyTicket'])->name('tickets.reply');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [UserController::class, 'notifications'])->name('index');
        Route::get('/{notification}', [UserController::class, 'showNotification'])->name('show');
        Route::post('/{notification}/read', [UserController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [UserController::class, 'markAllAsRead'])->name('read-all');
    });
});

// Webhook Routes (if needed for payment gateways)
Route::post('/webhooks/payment', [TransactionController::class, 'paymentWebhook']);
Route::post('/webhooks/withdrawal', [TransactionController::class, 'withdrawalWebhook']);

// Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});
