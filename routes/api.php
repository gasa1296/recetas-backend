<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientMediaController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPrescriptionController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\StatisticController;
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
                Route::post('/auth/register', 'register')->name('register');
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
                Route::post('/public/prescriptions/{prescription}/dispense', 'dispense')->name('dispense');
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
                    Route::delete('/patients/{patient}', 'destroy')->name('destroy');
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
                    Route::post('/prescriptions/{prescription}/null', 'nullPrescription')->name('null');
                    Route::post('/prescriptions/{prescription}/resend', 'resend')->name('resend');
                    Route::match(['get', 'post'], '/prescriptions/{prescription}/file', 'getFile')->name('file');
                });
            Route::controller(SpecialtyController::class)
                ->name('specialty.')
                ->group(function () {
                    Route::get('/specialty', 'index')->name('index');
                    Route::post('/specialty', 'store')->name('store');
                    Route::put('/specialty', 'update')->name('update');
                });
            Route::controller(RoomController::class)
                ->name('rooms.')
                ->group(function () {
                    Route::get('/rooms', 'index')->name('index');
                    Route::get('/rooms/{room}', 'show')->name('show');
                    Route::post('/rooms', 'store')->name('store');
                    Route::put('/rooms/{room}', 'update')->name('update');
                    Route::delete('/rooms/{room}', 'destroy')->name('destroy');
                });
            Route::controller(PrescriptionTemplateController::class)
                ->name('prescription_templates.')
                ->group(function () {
                    Route::get('/prescription-templates', 'index')->name('index');
                    Route::get('/prescription-templates/{prescription_template}', 'show')->name('show');
                    Route::post('/prescription-templates', 'store')->name('store');
                    Route::put('/prescription-templates/{prescription_template}', 'update')->name('update');
                    Route::delete('/prescription-templates/{prescription_template}', 'destroy')->name('destroy');
                });
            Route::controller(AppointmentController::class)
                ->name('appointments.')
                ->group(function () {
                    Route::get('/appointments', 'index')->name('index');
                    Route::get('/appointments/{appointment}', 'show')->name('show');
                    Route::post('/appointments', 'store')->name('store');
                    Route::put('/appointments/{appointment}', 'update')->name('update');
                    Route::delete('/appointments/{appointment}', 'destroy')->name('destroy');
                    Route::post('/appointments/{appointment}/status', 'updateStatus')->name('status');
                });
            Route::controller(PatientMediaController::class)
                ->name('patients.media.')
                ->group(function () {
                    Route::get('/patients/{patient}/media', 'index')->name('index');
                    Route::post('/patients/{patient}/media', 'store')->name('store');
                    Route::get('/patients/{patient}/media/{file}', 'show')->name('show');
                    Route::get('/patients/{patient}/media/{file}/stream', 'stream')->name('stream');
                    Route::get('/patients/{patient}/media/{file}/download', 'download')->name('download');
                    Route::put('/patients/{patient}/media/{file}', 'update')->name('update');
                    Route::delete('/patients/{patient}/media/{file}', 'destroy')->name('destroy');
                });
            Route::controller(ExaminationController::class)
                ->name('patients.examinations.')
                ->group(function () {
                    Route::get('/patients/{patient}/examinations', 'index')->name('index');
                    Route::post('/patients/{patient}/examinations', 'store')->name('store');
                    Route::get('/patients/{patient}/examinations/{examination}', 'show')->name('show');
                    Route::put('/patients/{patient}/examinations/{examination}', 'update')->name('update');
                    Route::delete('/patients/{patient}/examinations/{examination}', 'destroy')->name('destroy');
                    Route::post('/patients/{patient}/examinations/{examination}/files', 'addFile')->name('files.add');
                    Route::delete('/patients/{patient}/examinations/{examination}/files/{file}', 'removeFile')->name('files.remove');
                });
            Route::controller(StatisticController::class)
                ->prefix('statistics')
                ->name('statistics.')
                ->group(function () {
                    Route::get('/overview', 'overview')->name('overview');
                    Route::get('/by-medicament', 'byMedicament')->name('by_medicament');
                    Route::get('/by-brand', 'byBrand')->name('by_brand');
                    Route::get('/by-laboratory', 'byLaboratory')->name('by_laboratory');
                    Route::get('/by-patient', 'byPatient')->name('by_patient');
                    Route::get('/timeline', 'timeline')->name('timeline');
                });
            Route::controller(StatisticController::class)
                ->group(function () {
                    Route::get('/laboratories', 'laboratories')->name('laboratories.index');
                    Route::get('/brands', 'brands')->name('brands.index');
                });
        });
    }
});
