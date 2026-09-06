<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'identification' => ['required', 'string', 'max:50', 'unique:users,identification'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['required_with:phone', 'string'],
            'specialty' => ['required', 'array'],
            'specialty.name' => ['required_with:specialty', 'string', 'max:255'],
            'specialty.identification' => ['required_with:specialty', 'array'],
            'specialty.identification.medic_society' => ['required_with:specialty.identification', 'string', 'max:255', 'unique:specialties,identification->medic_society'],
            'specialty.identification.medic_registration' => ['required_with:specialty.identification', 'numeric', 'digits:7', 'unique:specialties,identification->medic_registration'],
            'saved_signature' => ['nullable', 'string', 'max:65535', 'regex:/^[A-Za-z0-9+\/\n\r]*={0,2}$/'],
            'room' => ['nullable', 'array'],
            'room.name' => ['required_with:room', 'string', 'max:255'],
            'room.identification' => ['nullable', 'string', 'max:50'],
            'room.zip' => ['nullable', 'string', 'max:20'],
            'room.address' => ['nullable', 'string', 'max:255'],
            'room.phone' => ['nullable'],
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
            'identification' => __('validation.attributes.identification'),
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'phone' => __('validation.attributes.phone'),
            'phone.*' => __('validation.attributes.phone.*'),
            'specialty' => __('validation.attributes.specialty'),
            'specialty.name' => __('validation.attributes.specialty.name'),
            'specialty.identification' => __('validation.attributes.specialty.identification'),
            'specialty.identification.medic_society' => __('validation.attributes.specialty.identification.medic_society'),
            'specialty.identification.medic_registration' => __('validation.attributes.specialty.identification.medic_registration'),
            'saved_signature' => __('validation.attributes.saved_signature'),
            'room' => __('validation.attributes.room'),
            'room.name' => __('validation.attributes.room.name'),
            'room.identification' => __('validation.attributes.room.identification'),
            'room.zip' => __('validation.attributes.room.zip'),
            'room.address' => __('validation.attributes.room.address'),
            'room.phone' => __('validation.attributes.room.phone'),
        ];
    }
}
