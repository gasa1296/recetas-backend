<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
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
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'identification' => ['required',],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['required_with:phone', 'string'],
            'gender' => ['required', 'string', Rule::in(array_keys(config('custom.gender')))],
            'birth_date' => ['nullable', 'date'],
        ];
    }
}
