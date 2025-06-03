<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GeographyController; // Import the new controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Geography API Routes (No auth needed for these typically, as they populate forms)
Route::prefix('geography')->name('api.geography.')->group(function () {
    Route::get('/divisions', [GeographyController::class, 'getAllDivisions'])->name('divisions.all');
    Route::get('/districts/{division}', [GeographyController::class, 'getDistricts'])->name('districts.by_division');
    Route::get('/upazilas/{district}', [GeographyController::class, 'getUpazilas'])->name('upazilas.by_district');
});

// ... other API routes
