<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class FinishPrescriptionRequest extends FormRequest
{
    use JsonValidationResponse;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $hasSavedSignature = ! empty(auth()->user()?->saved_signature);

        return [
            'signature' => [
                $hasSavedSignature ? 'nullable' : 'required',
                'string',
                'regex:/^[A-Za-z0-9+\/\n\r]*={0,2}$/',
            ],
            'save_signature' => ['nullable', 'boolean'],
        ];
    }
}
