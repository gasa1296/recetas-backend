<?php

use App\Http\Controllers\PublicPrescriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/public/prescriptions/{prescription}', [PublicPrescriptionController::class, 'show'])->name('web.public.prescription.show');
Route::post('/public/prescriptions/{prescription}/dispense', [PublicPrescriptionController::class, 'dispense'])->name('web.public.prescription.dispense');

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
