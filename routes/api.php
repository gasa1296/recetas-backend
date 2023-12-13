<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionEquipmentController;
use App\Http\Controllers\PrescriptionMedicamentController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ConsultingRoomController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicamentController;
use App\Models\Prescription;
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
    Route::delete('logout', 'logout')->middleware(['auth:sanctum', /*'verified'*/]);
});

Route::controller(VerificationController::class)->prefix('verify')->group(function () {
    Route::get('verify/{id}','verify')->name('verification.verify');
    Route::post('resend','resend')->name('verification.resend');
    Route::get('notice', 'notice')->name('verification.notice');
});

Route::controller(ResetController::class)->prefix('reset')->group(function () {
    Route::post('request', 'request')->name('reset.request');
    Route::post('reset', 'reset')->name('reset.reset');
});

Route::controller(PrescriptionController::class)->prefix('receta')->group(function () {
    Route::get('', 'getByClient');
    Route::get('{receta}', 'show');
    Route::post('{receta}', 'addClient');
    Route::put('{receta}', 'updateStatus');
});

Route::middleware(['auth:sanctum', /*'verified'*/])->group(function () {

    Route::controller(AuthController::class)->prefix('profile')->group(function () {
        Route::get('', 'show');
        Route::put('', 'update');
        Route::delete('', 'destroy');
    });
    Route::apiResources([
        'room'=> ConsultingRoomController::class,
        'specialization' => SpecializationController::class,
        'patient' => PatientController::class,
        'prescription' => PrescriptionController::class,
        'medicament' => MedicamentController::class,
        'equipment' => EquipmentController::class,
        'prescription.medicament' => PrescriptionMedicamentController::class,
        'prescription.equipment' => PrescriptionEquipmentController::class,
    ]);
});
