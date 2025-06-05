<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});







use App\Http\Controllers\User\MembershipController as UserMembershipController;
// ...

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    // ... other user profile routes ...

    Route::prefix('membership')->name('membership.')->group(function () {
        Route::get('/apply', [UserMembershipController::class, 'showApplyForm'])->name('apply.form');
        Route::post('/apply', [UserMembershipController::class, 'processApplication'])->name('apply.process');

        Route::get('/payment/{membership}', [UserMembershipController::class, 'showPaymentForm'])->name('payment.form')->middleware('can:manage,membership'); // Policy for ownership
        Route::post('/payment/{membership}', [UserMembershipController::class, 'processPayment'])->name('payment.process')->middleware('can:manage,membership');

        Route::get('/status', [UserMembershipController::class, 'showStatus'])->name('status'); // Part of profile.memberships perhaps
    });

    // Example route within user profile for memberships
    Route::get('/profile/memberships', [App\Http\Controllers\User\ProfileController::class, 'memberships'])->name('profile.memberships');

});





// In routes/web.php (admin group)



require __DIR__.'/auth.php';
