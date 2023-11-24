<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ConsultingRoomController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware(['auth:sanctum', 'verified']);
});

Route::controller(VerificationController::class)->prefix('email')->group(function () {
    Route::get('verify/{id}/{hash}','verify');
    Route::post('verify/resend','resend');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::controller(AuthController::class)->prefix('profile')->group(function () {
        Route::get('profile', 'login');
        Route::put('profile', 'update');
        Route::delete('profile', 'destroy');
    });
    Route::apiResources([
        'room'=> ConsultingRoomController::class,
        'patient' => PatientController::class,
    ]);
});
