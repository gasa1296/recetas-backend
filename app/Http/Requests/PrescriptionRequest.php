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
            'medicaments.*.id' => ['required_with:medicaments', 'exists:medicaments,id'],
            'medicaments.*.dosage' => ['required_with:medicaments', 'string'],
            'medicaments.*.frequency' => ['required_with:medicaments', 'string'],
            'medicaments.*.duration' => ['required_with:medicaments', 'string'],
            'medicaments.*.medicament_quantity' => ['required_with:medicaments', 'numeric', 'min:0'],
            'medicaments.*.medicament_quantity_letters' => ['required_with:medicaments', 'string'],
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
                "medicament_data.{$id}.medicament_quantity" => $medicament['medicament_quantity'] ?? null,
                "medicament_data.{$id}.medicament_quantity_letters" => $medicament['medicament_quantity_letters'] ?? null,
            ]);
        }
        $this->merge([
            'status' => config('custom.prescription.status.0'),
        ]);
    }
}
