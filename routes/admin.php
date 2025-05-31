<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserProfileController as AdminSelfProfileController;
use App\Http\Controllers\Admin\UserController; // New
use App\Http\Controllers\Admin\RoleController;   // New
use App\Http\Controllers\Admin\PermissionController; // New

Route::middleware(['auth', 'verified']) // Base auth for all admin routes
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role:Super-Admin|Admin|Member|Resource Person|Finance Admin|Finance Officer|Project/Allowance Manager'); // Allow more roles to see dashboard

        // Admin's own profile (accessible by any authenticated admin panel user)
        Route::get('/profile', [AdminSelfProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminSelfProfileController::class, 'update'])->name('profile.update');

        // User Management (Protected by 'manage-users' permission or specific roles)
        Route::middleware(['permission:manage-users|view-users']) // Or use specific role middleware if preferred
            ->group(function () {
                Route::resource('users', UserController::class);
                // Add a route for updating roles for a user
                Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.updateRoles')->middleware('permission:manage-users'); // More specific permission
            });

        // Role Management (Protected by 'manage-roles' permission)
        Route::middleware(['permission:manage-roles|view-roles'])
            ->group(function () {
                Route::resource('roles', RoleController::class);
                // Add a route for updating permissions for a role
                Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions')->middleware('permission:manage-roles');
            });

        // Permission Management (Protected by 'manage-permissions' permission)
        Route::middleware(['permission:manage-permissions|view-permissions'])
             ->resource('permissions', PermissionController::class)->only(['index', 'show']); // Typically, permissions are code-defined

});
