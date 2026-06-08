<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->name('auth')
    ->group(function () {
        Route::post('/auth/login', 'login');
        Route::post('/auth/logout', 'logout')->middleware('auth:sanctum');
    });
Route::controller(ResetController::class)
    ->name('password.')
    ->group(function () {
        Route::post('/password/request', 'request')->name('request');
        Route::post('/password/reset', 'reset')->name('reset');
    });
Route::controller(VerificationController::class)
    ->name('emailVerification.')
    ->group(function () {
        Route::get('/verification/verify', 'verify')->name('verify');
        Route::post('/verification/resend', 'resend')->name('resend');
    });

Route::controller(UniversityController::class)
    ->name('universities.')
    ->group(function () {
        Route::get('/universities', 'index');
        Route::get('/universities/{university}', 'show');
    });

Route::middleware('auth:sanctum')->name('profile')->group(function () {

    Route::controller(ProfileController::class)
        ->name('profile.')
        ->group(function () {
            Route::get('/profile', 'index');
            Route::put('/profile', 'update');
            Route::delete('/profile', 'destroy');
        });

    Route::controller(MedicamentController::class)
        ->name('medicaments.')
        ->group(function () {
            Route::get('/medicaments', 'index');
            Route::get('/medicaments/{medicament}', 'show');
        });

    Route::controller(PatientController::class)
        ->name('patients.')
        ->group(function () {
            Route::get('/patients', 'index');
            Route::get('/patients/{patient}', 'show');
            Route::post('/patients', 'store');
            Route::put('/patients/{patient}', 'update');
            Route::delete('/patients/{patient}', 'destroy');
        });

    Route::controller(PrescriptionController::class)
        ->name('prescriptions.')
        ->group(function () {
            Route::get('/prescriptions', 'index');
            Route::get('/prescriptions/{prescription}', 'show');
            Route::post('/prescriptions', 'store');
            Route::put('/prescriptions/{prescription}', 'update');
            Route::delete('/prescriptions/{prescription}', 'destroy');
            Route::post('/prescriptions/{prescription}/finish', 'finishPrescription');
        });

    Route::controller(RoomController::class)
        ->name('rooms.')
        ->group(function () {
            Route::get('/rooms', 'index');
            Route::get('/rooms/{room}', 'show');
            Route::post('/rooms', 'store');
            Route::put('/rooms/{room}', 'update');
            Route::delete('/rooms/{room}', 'destroy');
        });

    Route::controller(SpecialtyController::class)
        ->name('specialties.')
        ->group(function () {
            Route::get('/specialties', 'index');
            Route::get('/specialties/{specialty}', 'show');
            Route::post('/specialties', 'store');
            Route::put('/specialties/{specialty}', 'update');
            Route::delete('/specialties/{specialty}', 'destroy');
        });
});
