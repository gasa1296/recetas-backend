<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialtyRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        $specialtyId = $user->specialty?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'identification' => ['required', 'array'],
            'identification.medic_society' => ['required', 'string', 'max:255', 'unique:specialties,identification->medic_society,'.$specialtyId],
            'identification.medic_registration' => ['required', 'numeric', 'digits:7', 'unique:specialties,identification->medic_registration,'.$specialtyId],
        ];
    }
}
