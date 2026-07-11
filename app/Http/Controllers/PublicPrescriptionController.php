<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Support\Facades\Storage;

class PublicPrescriptionController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string|int $prescription)
    {
        if (config('app.debug')) {
            $prescription = Prescription::where('id', $prescription)->orWhere('prescription_hash', $prescription)->firstOrFail();
        } else {
            $prescription = Prescription::where('prescription_hash', $prescription)->firstOrFail();
        }

        if ($prescription->signed_file) {
            $path = Storage::disk('local')->path($prescription->signed_file->path);

            return response()->file($path);

        }
    }
}
