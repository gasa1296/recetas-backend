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

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'signature' => __('validation.attributes.signature'),
            'save_signature' => __('validation.attributes.save_signature'),
        ];
    }
}
