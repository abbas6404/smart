<?php

use App\Http\Controllers\Agents\AgentController;
use Illuminate\Support\Facades\Route;

// Agent Protected Routes (Authentication Required)
Route::prefix('agent')->name('agent.')->middleware(['auth:agent'])->group(function () {
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AgentController::class, 'profile'])->name('index');
        Route::get('/edit', [AgentController::class, 'editProfile'])->name('edit');
        Route::put('/', [AgentController::class, 'updateProfile'])->name('update');
        Route::put('/password', [AgentController::class, 'updatePassword'])->name('password');
    });
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [AgentController::class, 'financial'])->name('index');
        Route::get('/transactions', [AgentController::class, 'transactions'])->name('transactions');
        Route::get('/balance', [AgentController::class, 'balance'])->name('balance');
        Route::post('/withdraw', [AgentController::class, 'withdraw'])->name('withdraw');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AgentController::class, 'users'])->name('index');
        Route::get('/{user}', [AgentController::class, 'showUser'])->name('show');
        Route::get('/{user}/transactions', [AgentController::class, 'userTransactions'])->name('transactions');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AgentController::class, 'reports'])->name('index');
        Route::get('/financial', [AgentController::class, 'financialReport'])->name('financial');
        Route::get('/users', [AgentController::class, 'usersReport'])->name('users');
        Route::get('/export/{type}', [AgentController::class, 'exportReport'])->name('export');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AgentController::class, 'settings'])->name('index');
        Route::put('/update', [AgentController::class, 'updateSettings'])->name('update');
    });
});
