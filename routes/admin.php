<?php

use App\Http\Controllers\Admins\AdminController;
use Illuminate\Support\Facades\Route;

// Admin Routes (No Authentication Required)
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard.index');
    
    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('delete');
        Route::post('/{user}/activate', [AdminController::class, 'activateUser'])->name('activate');
        Route::post('/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('deactivate');
    });

    // Sub Accounts Management
    Route::prefix('sub-accounts')->name('sub-accounts.')->group(function () {
        Route::get('/', [AdminController::class, 'subAccounts'])->name('index');
        Route::get('/{subAccount}', [AdminController::class, 'showSubAccount'])->name('show');
        Route::put('/{subAccount}', [AdminController::class, 'updateSubAccount'])->name('update');
    });
    
    // Packages Management
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [AdminController::class, 'packages'])->name('index');
        Route::get('/create', [AdminController::class, 'createPackage'])->name('create');
        Route::post('/', [AdminController::class, 'storePackage'])->name('store');
        Route::get('/{package}', [AdminController::class, 'showPackage'])->name('show');
        Route::get('/{package}/edit', [AdminController::class, 'editPackage'])->name('edit');
        Route::put('/{package}', [AdminController::class, 'updatePackage'])->name('update');
        Route::delete('/{package}', [AdminController::class, 'deletePackage'])->name('delete');
    });
    
    // Transactions Management
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [AdminController::class, 'transactions'])->name('index');
        Route::get('/{transaction}', [AdminController::class, 'showTransaction'])->name('show');
        Route::put('/{transaction}', [AdminController::class, 'updateTransaction'])->name('update');
        Route::post('/{transaction}/approve', [AdminController::class, 'approveTransaction'])->name('approve');
        Route::post('/{transaction}/reject', [AdminController::class, 'rejectTransaction'])->name('reject');
    });

    // Withdrawals Management
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminController::class, 'withdrawals'])->name('index');
        Route::get('/{withdrawal}', [AdminController::class, 'showWithdrawal'])->name('show');
        Route::put('/{withdrawal}', [AdminController::class, 'updateWithdrawal'])->name('update');
        Route::post('/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('approve');
        Route::post('/{withdrawal}/reject', [AdminController::class, 'rejectWithdrawal'])->name('reject');
    });
    
    // Auto Board Management
    Route::prefix('auto-board')->name('auto-board.')->group(function () {
        Route::get('/', [AdminController::class, 'autoBoard'])->name('index');
        Route::post('/process-distribution', [AdminController::class, 'processDistribution'])->name('process-distribution');
    });

    // Commissions Management
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [AdminController::class, 'commissions'])->name('index');
        Route::get('/{commission}', [AdminController::class, 'showCommission'])->name('show');
        Route::put('/{commission}', [AdminController::class, 'updateCommission'])->name('update');
    });
    
    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminController::class, 'settings'])->name('index');
        Route::post('/update-general', [AdminController::class, 'updateGeneralSettings'])->name('update-general');
        Route::post('/update-commission', [AdminController::class, 'updateCommissionSettings'])->name('update-commission');
        Route::post('/update-auto-board', [AdminController::class, 'updateAutoBoardSettings'])->name('update-auto-board');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminController::class, 'reports'])->name('index');
        Route::get('/users', [AdminController::class, 'userReports'])->name('users');
        Route::get('/commissions', [AdminController::class, 'commissionReports'])->name('commissions');
        Route::get('/transactions', [AdminController::class, 'transactionReports'])->name('transactions');
        Route::get('/export/{type}', [AdminController::class, 'exportReport'])->name('export');
    });
});
