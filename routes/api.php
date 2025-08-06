<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LegalarioController;
use App\Http\Controllers\MagentoController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionEquipmentController;
use App\Http\Controllers\PrescriptionMedicamentController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SEUSPrescriptionController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ConsultingRoomController;
use App\Http\Controllers\PatientController;
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
    Route::post('recregf2', 'register')->name('register');
    Route::post('register', 'requestSignup')->name('request_register');
    Route::post('login', 'login')->name('login');
    Route::delete('logout', 'logout')->middleware(['auth:sanctum', /*'verified'*/])->name('logout');
});
Route::controller(WhatsappController::class)->prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('templates', 'getTemplates')->name('templates');
    Route::get('send/{prescription}', 'sendMessage')->name('send');
});
Route::controller(MagentoController::class)->prefix('auth')->name('auth.')->group(function () {
    Route::post('medic', 'getMedic')->name('getMedic');
    Route::post('generateMagentoToken', 'getToken')->name('generateMagentoToken');
    Route::post('getUserByTokenMagento', 'getUser')->name('getUserByTokenMagento');
    Route::post('specializations', 'getSpecialization')->name('getSpecialization');
    Route::post('states', 'getStates')->name('getStates');
});
Route::apiResource('university', UniversityController::class);
Route::controller(ResetController::class)->prefix('password')->name('password.')->group(function () {
    Route::post('request', 'request')->name('request');
    Route::post('reset', 'reset')->name('reset');
    Route::post('request/magento', 'resetPasswordMagento')->name('resetPasswordMagento');
});
Route::controller(SEUSPrescriptionController::class)->prefix('receta')->name('public_prescription.')->group(function () {
    Route::get('', 'getByClient')->name('getByClient');
    Route::post('file', 'addFile')->name('addFile');
    Route::get('{prescription:code}', 'show')->name('showPrescription');
    Route::post('{prescription:code}', 'addClient')->name('addClient');
    Route::put('{prescription:code}', 'updateStatus')->name('updateStatus');
    Route::get('{prescription:code}/file', 'getFile')->name('getFile');
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
        Route::get('', 'show')->name('show');
        Route::put('', 'update')->name('update');
        Route::delete('', 'destroy')->name('delete');
    });
    Route::get('most_used', [PrescriptionMedicamentController::class, 'mostUsed']);
    Route::apiResources([
        'room' => ConsultingRoomController::class,
        'specialization' => SpecializationController::class,
        'patient' => PatientController::class,
        'prescription' => PrescriptionController::class,
        'prescription.medicament' => PrescriptionMedicamentController::class,
    ]);
    Route::controller(PrescriptionController::class)->prefix('prescription')->name('prescription.')->group(function () {
        Route::get('{prescription}/email', 'sendEmailNotification')->name('email');
        Route::get('{prescription}/document', 'createDocument')->name('document');
        Route::get('{prescription}/file', 'getFile')->name('getFile');
        Route::post('{prescription}/file', 'addFile')->name('addFile');
    });

    Route::controller(LegalarioController::class)->prefix('prescription')->name('prescription.')->group(function () {
        Route::post('medicaments', 'getMedicaments')->name('medicaments');
        Route::get('{prescription}/sign', 'createSigner')->name('sign');
    });

});
