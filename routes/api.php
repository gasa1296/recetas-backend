<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPrescriptionController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

(new class
{
    public function __construct()
    {
        $this->authRoutes();
        $this->publicRoutes();
        $this->medicRoutes();
    }

    private function authRoutes()
    {
        Route::controller(AuthController::class)
            ->name('auth.')
            ->group(function () {
                Route::post('/auth/login', 'login')->name('login');
                Route::post('/auth/logout', 'logout')->name('logout')->middleware('auth:sanctum');
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
    }

    private function publicRoutes()
    {
        Route::controller(PublicPrescriptionController::class)->name('public.prescription.')
            ->group(function () {
                Route::get('/public/prescriptions/{prescription}', 'show')->name('show');
            });
    }

    private function medicRoutes()
    {
        Route::middleware('auth:sanctum')->name('profile')->group(function () {
            Route::controller(GenericController::class)
                ->name('generic.')
                ->group(function () {
                    Route::get('/genders', 'genders')->name('genders');
                    Route::get('/prescription-status', 'prescriptionStatus')->name('prescription_status');
                });

            Route::controller(ProfileController::class)
                ->name('profile.')
                ->group(function () {
                    Route::get('/profile', 'index')->name('index');
                    Route::put('/profile', 'update')->name('update');
                    Route::delete('/profile', 'destroy')->name('destroy');
                });

            Route::controller(MedicamentController::class)
                ->name('medicaments.')
                ->group(function () {
                    Route::get('/medicaments', 'index')->name('index');
                    Route::get('/medicaments/{medicament}', 'show')->name('show');
                });

            Route::controller(PatientController::class)
                ->name('patients.')
                ->group(function () {
                    Route::get('/patients', 'index')->name('index');
                    Route::get('/patients/{patient}', 'show')->name('show');
                    Route::post('/patients', 'store')->name('store');
                    Route::put('/patients/{patient}', 'update')->name('update');
                });

            Route::controller(PrescriptionController::class)
                ->name('prescriptions.')
                ->group(function () {
                    Route::get('/prescriptions', 'index')->name('index');
                    Route::get('/prescriptions/{prescription}', 'show')->name('show');
                    Route::post('/prescriptions', 'store')->name('store');
                    Route::put('/prescriptions/{prescription}', 'update')->name('update');
                    Route::delete('/prescriptions/{prescription}', 'destroy')->name('destroy');
                    Route::post('/prescriptions/{prescription}/finish', 'finishPrescription')->name('finish');
                    Route::post('/prescriptions/{prescription}/file', 'getFile')->name('file');
                });
            Route::get('specialty/identification-config', [SpecialtyController::class, 'getSpecialtyIdentificationConfig'])->name('specialty.identification-config');
            Route::controller(RoomController::class)
                ->name('rooms.')
                ->group(function () {
                    Route::get('/rooms', 'index')->name('index');
                    Route::get('/rooms/{room}', 'show')->name('show');
                    Route::post('/rooms', 'store')->name('store');
                    Route::put('/rooms/{room}', 'update')->name('update');
                    Route::delete('/rooms/{room}', 'destroy')->name('destroy');
                });
        });
    }
});
