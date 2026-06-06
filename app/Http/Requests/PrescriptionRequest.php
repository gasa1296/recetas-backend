<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionRequest extends FormRequest
{
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
     * @return array<string, ValidationRule', 'array<mixed>', 'string>
     */
    public function rules(): array
    {
        return [
            'temp' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'pressure' => ['required', 'numeric', 'min:0'],
            'saturation' => ['required', 'numeric', 'min:0'],
            'ppm' => ['required', 'numeric', 'min:0'],
            'allergy' => ['nullable', 'string'],
            'diagnostic' => ['nullable', 'string'],
            'diet' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'medicament_data' => ['nullable', 'array'],
            'medicament_data.*.id' => ['exists:medicaments,id'],
            'medicament_data.*.dosage' => ['required', 'numeric', 'min:0'],
            'medicament_data.*.frequency' => ['required', 'string'],
            'medicament_data.*.duration' => ['required', 'string'],
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')->where('user_id', auth()->id())],
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')->where('user_id', auth()->id())],
        ];
    }
}
