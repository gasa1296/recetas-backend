<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
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
    Route::get('profile', 'login')->middleware(['auth:sanctum', 'verified']);
    Route::put('profile', 'update')->middleware(['auth:sanctum', 'verified']);
    Route::delete('profile', 'destroy')->middleware(['auth:sanctum', 'verified']);
});
Route::controller(VerificationController::class)->prefix('email')->group(function () {
    Route::get('verify/{id}/{hash}','verify');
    Route::post('verify/resend','resend');
});