<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Admins\AdminAuthController;
use App\Http\Controllers\Agents\AgentAuthController;

// Root URL - Smart redirect based on user type
Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard.index');
    } elseif (Auth::guard('agent')->check()) {
        return redirect()->route('agent.dashboard.index');
    } elseif (Auth::guard('web')->check()) {
        return redirect()->route('user.dashboard.index');
    }
    return redirect()->route('login');
})->name('home');

// Smart Admin Root Route
Route::get('/admin', function () {
    if (Auth::guard('admin')->check()) {
        return app(\App\Http\Controllers\Admins\AdminController::class)->dashboard();
    }
    return redirect()->route('admin.login');
})->name('admin.root');

// Smart Agent Root Route
Route::get('/agent', function () {
    if (Auth::guard('agent')->check()) {
        return app(\App\Http\Controllers\Agents\AgentController::class)->dashboard();
    }
    return redirect()->route('agent.login');
})->name('agent.root');

// Guest Routes (Unauthenticated Users Only)
Route::middleware(['web', 'guest'])->group(function () {
    
    // User Authentication
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login')->middleware('throttle:10,1');
    Route::post('/login', [UserAuthController::class, 'login'])->name('user.login.post')->middleware('throttle:5,1');
    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('user.register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('user.register.post')->middleware('throttle:3,1');
    
    // Admin Authentication
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login')->middleware('throttle:10,1');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    });
    
    // Agent Authentication
    Route::prefix('agent')->name('agent.')->group(function () {
        Route::get('/login', [AgentAuthController::class, 'showLogin'])->name('login')->middleware('throttle:10,1');
        Route::post('/login', [AgentAuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    });
});

// Logout Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
});

Route::middleware(['web', 'auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::middleware(['web', 'auth:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::post('/logout', [AgentAuthController::class, 'logout'])->name('logout');
});

// Route File Inclusions
require __DIR__.'/user.php';
require __DIR__.'/admin.php';
require __DIR__.'/agent.php';

// Cron Job Routes
Route::prefix('cron')->name('cron.')->middleware(['web', 'throttle:10,1'])->group(function () {
    Route::get('/process-auto-board', [\App\Http\Controllers\CronController::class, 'processAutoBoard'])->name('process-auto-board')->middleware('throttle:5,1');
    Route::get('/update-purchase-referrals', [\App\Http\Controllers\CronController::class, 'updatePurchaseReferrals'])->name('update-purchase-referrals')->middleware('throttle:5,1');
    Route::get('/process-commissions', [\App\Http\Controllers\CronController::class, 'processCommissions'])->name('process-commissions')->middleware('throttle:5,1');
    Route::get('/health', [\App\Http\Controllers\CronController::class, 'health'])->name('health');
    Route::get('/cleanup', [\App\Http\Controllers\CronController::class, 'cleanup'])->name('cleanup')->middleware('throttle:2,1');
});



// Fallback Route
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json(['error' => 'Route not found'], 404);
    }
    return response()->view('errors.404', [], 404);
})->name('fallback');

