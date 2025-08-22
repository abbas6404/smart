<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserAuthController;
use App\Http\Controllers\Admins\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all the web routes for your MLM AutoBoard application.
| Routes are organized by access level: public (guest), protected user, and protected admin.
| Each section is clearly commented for easy understanding and maintenance.
|
*/

// ============================================================================
// ROOT ROUTE - Entry Point
// ============================================================================
// Redirects visitors from the root URL to the user login page
Route::get('/', function () {
    return redirect()->route('user.login');
});

// ============================================================================
// GUEST ROUTES - Public Access (No Authentication Required)
// ============================================================================
// These routes are accessible to anyone who visits your site
// Users must NOT be logged in to access these routes (guest middleware)
Route::middleware('guest')->group(function () {

    // ========================================
    // USER AUTHENTICATION ROUTES
    // ========================================
    // These routes handle user registration and login
    // Users can login with either phone number or email using the 'login' field

    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('user.login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('user.login.post');

    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('user.register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('user.register.post');
    
    // ========================================
    // ADMIN AUTHENTICATION ROUTES
    // ========================================
    // These routes handle admin login (admin registration is typically disabled)
    
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

// ============================================================================
// PROTECTED USER ROUTES - Authentication Required
// ============================================================================
// These routes require users to be logged in
// Users must be authenticated to access these routes (auth middleware)
Route::middleware('auth')->group(function () {

    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
});

// ============================================================================
// PROTECTED ADMIN ROUTES - Admin Authentication Required
// ============================================================================
// These routes require admin users to be logged in
// Users must be authenticated as admin to access these routes (auth.admin:admin middleware)
Route::middleware('auth.admin:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ============================================================================
// ROUTE FILE INCLUSIONS
// ============================================================================
// These files contain additional routes organized by functionality
// Each file focuses on a specific area of the application

// Include admin management routes (users, packages, transactions, etc.)
// These routes are already protected by the auth.admin:admin middleware
require __DIR__.'/admin.php';

// Include user dashboard routes (profile, packages, referrals, commissions, etc.)
// These routes are already protected by the auth middleware
require __DIR__.'/user.php';

// ============================================================================
// CRON JOB URLS - External Cron Services
// ============================================================================
// These URLs can be called by external cron services to trigger automated tasks
// They include basic security with a secret key parameter

Route::prefix('cron')->name('cron.')->group(function () {
    // Update purchase referral counts
    Route::get('/update-purchase-referrals', [\App\Http\Controllers\CronController::class, 'updatePurchaseReferrals'])
        ->name('update-purchase-referrals');
    
    // Process AutoBoard distribution
    Route::get('/process-auto-board', [\App\Http\Controllers\CronController::class, 'processAutoBoard'])
        ->name('process-auto-board');
    
    // Health check for cron monitoring
    Route::get('/health', [\App\Http\Controllers\CronController::class, 'health'])
        ->name('health');
});

