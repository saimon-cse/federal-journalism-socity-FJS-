<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController; // Example
use App\Http\Controllers\Frontend\ProfileController as FrontendProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('frontend.home'); // Example public home

// Authentication routes (from Breeze or your setup)
// Typically includes login, register, password reset, email verification
require __DIR__.'/auth.php';


// Authenticated User Routes (for their own data)
Route::middleware(['auth', 'verified'])->group(function () {
    // User's Own Dashboard (different from admin dashboard)
    Route::get('/dashboard', function () {
        // This could be a controller action too
        return view('frontend.dashboard'); // Example: resources/views/frontend/dashboard.blade.php
    })->name('dashboard'); // This might conflict with admin.dashboard if not careful with naming or usage

    // User's Own Profile Management
    Route::prefix('my-profile')->name('frontend.profile.')->group(function () {
        Route::get('/', [FrontendProfileController::class, 'show'])->name('show'); // e.g., /my-profile
        // If you want a separate edit view from the show view:
        // Route::get('/edit', [FrontendProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [FrontendProfileController::class, 'update'])->name('update'); // Handles the form submission from show/edit
    });

    // Other authenticated user routes (e.g., my applications, my trainings) will go here
});


// Admin panel routes are typically in a separate file like admin.php
// but need to be registered in RouteServiceProvider.
// The following is just to ensure the /admin prefix itself doesn't clash if web.php has it.
// Route::prefix('admin')->middleware(['auth', 'verified'])->group(function() {
// This is handled by including routes/admin.php in RouteServiceProvider
// });


use App\Http\Controllers\Frontend\MembershipController; // New

Route::middleware(['auth', 'verified'])->group(function () {
    // ... other authenticated routes (dashboard, profile)

    Route::prefix('membership')->name('frontend.membership.')->group(function () {
        Route::get('/apply', [MembershipController::class, 'createApplicationForm'])->name('apply.create')->middleware('not_a_member'); // Only non-members can apply
        Route::post('/apply', [MembershipController::class, 'storeApplication'])->name('apply.store')->middleware('not_a_member');
        Route::get('/application-status', [MembershipController::class, 'applicationStatus'])->name('application.status'); // To show current status or if already a member
    });
});
