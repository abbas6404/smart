<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('user')->name('user.')->middleware(['auth'])->group(function () {
    
    // User Dashboard
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard.index');
    
    // User Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('index');
        Route::get('/edit', [UserController::class, 'editProfile'])->name('edit');
        Route::put('/', [UserController::class, 'updateProfile'])->name('update');
        Route::put('/password', [UserController::class, 'updatePassword'])->name('password');
        Route::put('/security', [UserController::class, 'updateSecurity'])->name('security');
    });
    
    // Investment Packages
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [UserController::class, 'packages'])->name('index');
        Route::get('/{package}', [UserController::class, 'showPackage'])->name('show');
        Route::post('/{package}/purchase', [UserController::class, 'purchasePackage'])->name('purchase');
        Route::get('/my-packages', [UserController::class, 'myPackages'])->name('my');
        Route::get('/my-packages/{purchase}', [UserController::class, 'showMyPackage'])->name('my.show');
    });
    
    // Referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [UserController::class, 'referrals'])->name('index');
        Route::get('/network', [UserController::class, 'network'])->name('network');
        Route::get('/tree', [UserController::class, 'tree'])->name('tree');
        Route::post('/invite', [UserController::class, 'sendInvite'])->name('invite');
    });
    
    // Commissions
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [UserController::class, 'commissions'])->name('index');
        Route::get('/history', [UserController::class, 'commissionHistory'])->name('history');
        Route::get('/summary', [UserController::class, 'commissionSummary'])->name('summary');
        Route::get('/withdraw', [UserController::class, 'withdrawCommission'])->name('withdraw');
        Route::post('/withdraw', [UserController::class, 'processWithdrawal'])->name('withdraw.process');
    });
    
    // Auto Board
    Route::prefix('auto-board')->name('auto-board.')->group(function () {
        Route::get('/', [UserController::class, 'autoBoard'])->name('index');
        Route::get('/status', [UserController::class, 'autoBoardStatus'])->name('status');
        Route::get('/earnings', [UserController::class, 'autoBoardEarnings'])->name('earnings');
        Route::post('/join', [UserController::class, 'joinAutoBoard'])->name('join');
        Route::post('/leave', [UserController::class, 'leaveAutoBoard'])->name('leave');
    });
    
    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [UserController::class, 'transactions'])->name('index');
        Route::get('/{transaction}', [UserController::class, 'showTransaction'])->name('show');
        Route::get('/deposits', [UserController::class, 'deposits'])->name('deposits');
        Route::get('/withdrawals', [UserController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/deposit', [UserController::class, 'createDeposit'])->name('deposit.create');
        Route::post('/withdrawal', [UserController::class, 'createWithdrawal'])->name('withdrawal.create');
    });
    
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
