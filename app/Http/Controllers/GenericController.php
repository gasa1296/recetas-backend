<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenericController extends Controller
{
    public function genders()
    {
        return $this->success(
            __('messages.operation_success'),
            config('custom.gender'),
        );
    }
    public function prescriptionStatus(Request $request)
    {
        return $this->success(
            __('messages.operation_success'),
            config('custom.prescription.status'),
        );
    }
}
