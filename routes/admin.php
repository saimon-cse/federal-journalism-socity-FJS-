<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserProfileController as AdminSelfProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\MembershipApplicationController;
// Import other admin controllers for future modules
// Example:
// use App\Http\Controllers\Admin\DivisionController;
// use App\Http\Controllers\Admin\DistrictController;
// use App\Http\Controllers\Admin\UpazilaController;
// use App\Http\Controllers\Admin\SettingController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role_or_permission:Super-Admin|Admin|view-dashboard');

        // Admin's own profile
        Route::get('/profile', [AdminSelfProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminSelfProfileController::class, 'update'])->name('profile.update');

        // User Management
        Route::middleware(['permission:manage-users|view-users'])
            ->group(function () {
                Route::resource('users', UserController::class);
                Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.updateRoles')->middleware('permission:manage-users');
                // If you ever need to quickly update a user's status (e.g., activate/deactivate member)
                // Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus')->middleware('permission:edit-users');
            });

        // Role Management
        Route::middleware(['permission:manage-roles|view-roles'])
            ->group(function () {
                Route::resource('roles', RoleController::class);
                Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions')->middleware('permission:manage-roles');
            });

        // Permission Management
        Route::middleware(['permission:manage-permissions|view-permissions'])
            ->resource('permissions', PermissionController::class)->only(['index', 'show']);

        // Geographic Data Management (Optional - if admin needs to manage these beyond seeders)
        /*
        Route::middleware(['permission:manage-locations'])->group(function () {
            Route::resource('divisions', DivisionController::class);
            Route::resource('districts', DistrictController::class);
            Route::resource('upazilas', UpazilaController::class);
        });
        */

        // Settings Management (To be implemented in Phase 4)
        /*
        Route::middleware(['permission:manage-settings'])->group(function () {
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        });



        Route::middleware(['permission:manage-memberships|view-memberships']) // New permission
            ->prefix('membership-applications')
            ->name('membership.applications.')
            ->group(function () {
                Route::get('/', [MembershipApplicationController::class, 'index'])->name('index');
                Route::get('/{user}/review', [MembershipApplicationController::class, 'review'])->name('review'); // User model is applicant
                Route::post('/{user}/approve', [MembershipApplicationController::class, 'approve'])->name('approve')->middleware('permission:manage-memberships');
                Route::post('/{user}/reject', [MembershipApplicationController::class, 'reject'])->name('reject')->middleware('permission:manage-memberships');
            });

        */
    });


// // ... other admin routes
// use App\Http\Controllers\Admin\PaymentAccountController; // New
// use App\Http\Controllers\Admin\PaymentVerificationController; // New

// Route::middleware(['auth', 'verified'])
//     ->prefix('admin')
//     ->name('admin.')
//     ->group(function () {
//         // ... other admin routes

//         // Payment Accounts Management
//         Route::middleware(['permission:manage-settings|manage-payment-accounts']) // New permission
//             ->resource('payment-accounts', PaymentAccountController::class);

//         // Payment Verification
//         Route::middleware(['permission:manage-payments|verify-payments']) // New permissions
//             ->prefix('payments')
//             ->name('payments.')
//             ->group(function () {
//                 Route::get('/', [PaymentVerificationController::class, 'index'])->name('index');
//                 Route::get('/{payment}/review', [PaymentVerificationController::class, 'review'])->name('review');
//                 Route::post('/{payment}/verify', [PaymentVerificationController::class, 'verify'])->name('verify');
//                 Route::post('/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('reject');
//             });
//     });


use App\Http\Controllers\Admin\PaymentAccountController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\FinancialLedgerController;
use App\Http\Controllers\Admin\FinancialTransactionCategoryController;

// ... other routes

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // ... other admin routes (dashboard, users, roles etc.)

    // Payment Accounts Management
    Route::resource('payment-accounts', PaymentAccountController::class)->except(['show']);

    // Payment Methods Management
    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);

    // Payments Overview & Verification
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show'); // To view payment details
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify'); // For manual verification action
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject'); // For rejecting a manual payment

    // Financial Transaction Categories (Needed for Ledger)
    Route::resource('financial-transaction-categories', FinancialTransactionCategoryController::class)->except(['show']);

    // Financial Ledger Management
    Route::get('financial-ledgers', [FinancialLedgerController::class, 'index'])->name('financial-ledgers.index');
    Route::get('financial-ledgers/create', [FinancialLedgerController::class, 'create'])->name('financial-ledgers.create');
    Route::post('financial-ledgers', [FinancialLedgerController::class, 'store'])->name('financial-ledgers.store');
    // Edit/Delete for ledger entries might be complex or disallowed depending on accounting practices.
    // Route::get('financial-ledgers/{financialLedger}/edit', [FinancialLedgerController::class, 'edit'])->name('financial-ledgers.edit');
    // Route::put('financial-ledgers/{financialLedger}', [FinancialLedgerController::class, 'update'])->name('financial-ledgers.update');
    // Route::delete('financial-ledgers/{financialLedger}', [FinancialLedgerController::class, 'destroy'])->name('financial-ledgers.destroy');

});
