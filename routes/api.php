<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultingRoomController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WhatsappController;
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
Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    //Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::delete('logout', 'logout')->middleware(['auth:sanctum'])->name('logout');
});
Route::controller(WhatsappController::class)->prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('templates', 'getTemplates')->name('templates');
    Route::get('send/{prescription}', 'sendMessage')->name('send');
});
Route::get('university', UniversityController::class);
Route::controller(ResetController::class)->prefix('password')->name('password.')->group(function () {
    Route::post('request', 'request')->name('request');
    Route::post('reset', 'reset')->name('reset');
    Route::post('request/magento', 'resetPasswordMagento')->name('resetPasswordMagento');
});
Route::controller(ConsultingRoomController::class)->prefix('room')->name('room.')->group(function () {
    Route::get('designs', 'getFormats')->name('designs');
});

Route::middleware(['auth:sanctum',/*'verified'*/])->group(function () {

    Route::controller(VerificationController::class)->prefix('verification')->name('verification.')->group(function () {
        Route::get('verify/{id}', 'verify')->name('verify');
        Route::post('resend', 'resend')->name('resend');
        Route::get('notice', 'notice')->name('notice');
    });
    Route::controller(AuthController::class)->prefix('profile')->group(function () {
        Route::get('', 'show')->name('show');
        Route::put('', 'update')->name('update');
        Route::delete('', 'destroy')->name('delete');
    });
    Route::controller(PrescriptionController::class)->prefix('prescription')->name('prescription.')->group(function () {
        Route::get('', 'show')->name('show');
        Route::post('', 'store')->name('store');
        Route::get('/{prescription}', 'show')->name('show');
    });
    Route::apiResources([
        'room' => ConsultingRoomController::class,
        'specialization' => SpecializationController::class,
        'patient' => PatientController::class,
    ]);

});
