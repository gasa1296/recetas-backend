<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionRequest extends FormRequest
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
            'temp' => ['nullable', 'numeric'],
            'weight' => ['nullable ', 'numeric'],
            'height' => ['nullable ', 'numeric'],
            'pressure' => ['nullable ', 'string'],
            'saturation' => ['nullable ', 'numeric'],
            'ppm' => ['nullable ', 'numeric'],
            'allergy' => ['nullable ', 'string'],
            'diagnostic' => ['nullable', 'string'],
            'diet' => ['nullable ', 'string'],
            'add' => ['nullable ', 'string'],
            'medicaments' => ['nullable ', 'array'],
            'room_id' => ['required ', 'numeric'],
            'patient_id' => ['required ', 'numeric'],
        ];
    }
}
