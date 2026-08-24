<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PrescriptionTemplateRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function prepareForValidation()
    {
        $formatter = new \NumberFormatter(app()->getLocale(), \NumberFormatter::SPELLOUT);
        $medicaments = $this->input('medicaments', []);
        $this->offsetUnset('medicaments');

        foreach ($medicaments as $medicament) {
            if (empty($medicament['id'])) {
                continue;
            }
            $id = $medicament['id'];
            $this->merge([
                'medicaments.'.$id.'.dosage' => $medicament['dosage'] ?? null,
                'medicaments.'.$id.'.frequency' => $medicament['frequency'] ?? null,
                'medicaments.'.$id.'.duration' => $medicament['duration'] ?? null,
                'medicaments.'.$id.'.medicament_quantity' => $medicament['medicament_quantity'] ?? null,
                'medicaments.'.$id.'.medicament_quantity_letters' => $formatter->format($medicament['medicament_quantity'] ?? ''),
                'medicaments.'.$id.'.recommended_brand' => $medicament['recommended_brand'] ?? null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'medicaments' => ['nullable', 'array'],
            'medicaments.*.dosage' => ['required_with:medicaments', 'string'],
            'medicaments.*.frequency' => ['required_with:medicaments', 'string'],
            'medicaments.*.duration' => ['required_with:medicaments', 'string'],
            'medicaments.*.medicament_quantity' => ['required_with:medicaments', 'numeric', 'min:0'],
            'medicaments.*.medicament_quantity_letters' => ['required_with:medicaments', 'string'],
            'medicaments.*.recommended_brand' => ['nullable', 'string'],
        ];
    }
}
