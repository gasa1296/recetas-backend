<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionRequest extends FormRequest
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
     * @return array<string, ValidationRule', 'array<mixed>', 'string>
     */
    public function rules(): array
    {
        return [
            'temp' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'pressure' => ['nullable', 'numeric', 'min:0'],
            'saturation' => ['nullable', 'numeric', 'min:0'],
            'ppm' => ['nullable', 'numeric', 'min:0'],
            'allergy' => ['nullable', 'string'],
            'diagnostic' => ['nullable', 'string'],
            'diet' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'medicaments' => ['nullable', 'array'],
            'medicaments.*.id' => ['nullable', 'exists:medicaments,id'],
            'medicaments.*.dosage' => ['nullable', 'string'],
            'medicaments.*.frequency' => ['nullable', 'string'],
            'medicaments.*.duration' => ['nullable', 'string'],
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')->where('user_id', auth()->id())],
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')->where('user_id', auth()->id())],
        ];
    }

    protected function passedValidation()
    {
        $medicaments = $this->input('medicaments', []);
        foreach ($medicaments as $medicament) {
            $id = $medicament['id'];
            $this->merge([
                "medicament_data.{$id}.dosage" => $medicament['dosage'] ?? null,
                "medicament_data.{$id}.frequency" => $medicament['frequency'] ?? null,
                "medicament_data.{$id}.duration" => $medicament['duration'] ?? null,
            ]);
        }
        $this->merge([
            'status' => config('custom.prescription.status.0'),
        ]);
    }
}
