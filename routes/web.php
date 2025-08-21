<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserAuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect to login page
Route::get('/', function () {
    return redirect()->route('user.login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // User Authentication
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('user.login');
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('user.register');
    Route::post('/register', [UserAuthController::class, 'register']);
    

});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
});



// Include admin routes
require __DIR__.'/admin.php';

// Include user routes
require __DIR__.'/user.php';

