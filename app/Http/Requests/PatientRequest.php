<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name1' => ['nullable', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ];
    }
}
