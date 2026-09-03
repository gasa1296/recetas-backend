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

    public function prepareForValidation()
    {
        $this->merge([
            'specialty_id' => auth()->user()->specialty?->id,
            'status' => config('custom.prescription.status_keys.draft'),
        ]);
        $formatter = new \NumberFormatter(app()->getLocale(), \NumberFormatter::SPELLOUT);
        $medicaments = $this->input('medicaments', []);
        $this->offsetUnset('medicaments');

        foreach ($medicaments as $medicament) {
            if (empty($medicament['id'])) {
                continue;
            }
            $id = $medicament['id'];
            $brandId = $medicament['brand_id'] ?? null;
            $laboratoryId = $medicament['laboratory_id'] ?? null;
            if (! $brandId && ! empty($medicament['recommended_brand'])) {
                $matchedBrand = \App\Models\Brand::where('name', 'like', '%'.$medicament['recommended_brand'].'%')->first();
                if ($matchedBrand) {
                    $brandId = $matchedBrand->id;
                    $laboratoryId = $matchedBrand->laboratory_id;
                }
            } elseif ($brandId && ! $laboratoryId) {
                $brand = \App\Models\Brand::find($brandId);
                if ($brand) {
                    $laboratoryId = $brand->laboratory_id;
                }
            }

            $this->merge([
                'medicaments.'.$id.'.dosage' => $medicament['dosage'] ?? null,
                'medicaments.'.$id.'.frequency' => $medicament['frequency'] ?? null,
                'medicaments.'.$id.'.duration' => $medicament['duration'] ?? null,
                'medicaments.'.$id.'.medicament_quantity' => $medicament['medicament_quantity'] ?? null,
                'medicaments.'.$id.'.medicament_quantity_letters' => $formatter->format($medicament['medicament_quantity'] ?? ''),
                'medicaments.'.$id.'.recommended_brand' => $medicament['recommended_brand'] ?? null,
                'medicaments.'.$id.'.brand_id' => $brandId,
                'medicaments.'.$id.'.laboratory_id' => $laboratoryId,
            ]);
        }
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
            'medicaments.*.dosage' => ['required_with:medicaments', 'string'],
            'medicaments.*.frequency' => ['required_with:medicaments', 'string'],
            'medicaments.*.duration' => ['required_with:medicaments', 'string'],
            'medicaments.*.medicament_quantity' => ['required_with:medicaments', 'numeric', 'min:0'],
            'medicaments.*.medicament_quantity_letters' => ['required_with:medicaments', 'string'],
            'medicaments.*.recommended_brand' => ['nullable', 'string'],
            'medicaments.*.brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'medicaments.*.laboratory_id' => ['nullable', 'integer', 'exists:laboratories,id'],
            'room_id' => ['required', 'integer', Rule::exists('rooms', 'id')->where('user_id', auth()->id())],
            'specialty_id' => ['required', 'integer', Rule::exists('specialties', 'id')->where('user_id', auth()->id())],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'status' => ['nullable', 'integer'],
        ];
    }
}
