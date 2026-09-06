<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
        $isSignatureOnly = $this->has('saved_signature') && ! $this->has('first_name') && ! $this->has('last_name');

        return [
            'first_name' => [$isSignatureOnly ? 'sometimes' : 'required', 'string'],
            'last_name' => [$isSignatureOnly ? 'sometimes' : 'required', 'string'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['required_with:phone', 'string'],
            'password' => ['nullable', 'string', 'confirmed'],
            'specialty' => ['nullable', 'array'],
            'specialty.name' => ['required_with:specialty', 'string', 'max:255'],
            'specialty.identification' => ['required_with:specialty', 'array'],
            'specialty.identification.medic_society' => ['required_with:identification', 'string', 'max:255', 'unique:specialties,identification->medic_society,'.$specialtyId],
            'specialty.identification.medic_registration' => ['required_with:identification', 'numeric', 'digits:7', 'unique:specialties,identification->medic_registration,'.$specialtyId],
            'saved_signature' => ['nullable', 'string'],
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
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'phone' => __('validation.attributes.phone'),
            'phone.*' => __('validation.attributes.phone.*'),
            'password' => __('validation.attributes.password'),
            'specialty' => __('validation.attributes.specialty'),
            'specialty.name' => __('validation.attributes.specialty.name'),
            'specialty.identification' => __('validation.attributes.specialty.identification'),
            'specialty.identification.medic_society' => __('validation.attributes.specialty.identification.medic_society'),
            'specialty.identification.medic_registration' => __('validation.attributes.specialty.identification.medic_registration'),
            'saved_signature' => __('validation.attributes.saved_signature'),
        ];
    }
}
