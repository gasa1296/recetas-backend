<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() !== null;
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['required_with:phone', 'string'],
            'password' => ['nullable', 'string', 'confirmed'],
            'specialty' => ['nullable', 'array'],
            'specialty.name' => ['required_with:specialty', 'string', 'max:255'],
            'specialty.identification' => ['required_with:specialty', 'array'],
            'specialty.identification.medic_society' => ['required_with:identification', 'string', 'max:255', 'unique:specialties,identification->medic_society,'.$specialtyId],
            'specialty.identification.medic_registration' => ['required_with:identification', 'numeric', 'digits:7', 'unique:specialties,identification->medic_registration,'.$specialtyId],
        ];
    }
}
