<?php

use App\Http\Controllers\PublicPrescriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/public/prescriptions/{prescription}', [PublicPrescriptionController::class, 'show'])->name('public.prescription.show')->middleware('throttle:60,1');
Route::post('/public/prescriptions/{prescription}/dispense', [PublicPrescriptionController::class, 'dispense'])->name('public.prescription.dispense')->middleware(['auth:sanctum', 'throttle:30,1']);

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
