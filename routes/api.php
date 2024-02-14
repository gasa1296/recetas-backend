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
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::delete('logout', 'logout')->middleware(['auth:sanctum', /*'verified'*/]);
});
Route::controller(ResetController::class)->prefix('password')->name('password.')->group(function () {
    Route::post('request', 'request')->name('request');
    Route::post('reset', 'reset')->name('reset');
});
Route::controller(PrescriptionController::class)->prefix('receta')->name('public_prescription.')->group(function () {
    Route::get('', 'getByClient');
    Route::post('file', 'addFile');
    Route::get('{prescription}', 'show');
    Route::post('{prescription}', 'addClient');
    Route::put('{prescription}', 'updateStatus');
    Route::get('medicament/{desc}', 'getMedicament');
    Route::get('{prescription}/file', 'getFile')->name('getFile');
});
Route::controller(ConsultingRoomController::class)->prefix('room')->name('room.')->group(function () {
    Route::get('designs', 'getFormats')->name('designs');
});

Route::middleware(['auth:sanctum', /*'verified'*/])->group(function () {

    Route::controller(VerificationController::class)->prefix('verification')->name('verification.')->group(function () {
        Route::get('verify/{id}', 'verify')->name('verify');
        Route::post('resend', 'resend')->name('resend');
        Route::get('notice', 'notice')->name('notice');
    });
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
        'equipment' => EquipmentController::class,
        'prescription.medicament' => PrescriptionMedicamentController::class,
        'prescription.equipment' => PrescriptionEquipmentController::class,
    ]);
    Route::controller(PrescriptionController::class)->prefix('prescription')->name('prescription.')->group(function () {
        Route::get('{prescription}/email', 'sendEmailNotification')->name('email');
        Route::get('{prescription}/sign', 'createSigner')->name('sign');
        Route::get('{prescription}/document', 'createDocument')->name('document');
        Route::get('{prescription}/file', 'getFile')->name('getFile');
    });
});
