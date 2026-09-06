<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use App\Models\Brand;
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
                $matchedBrand = Brand::where('name', 'like', '%'.$medicament['recommended_brand'].'%')->first();
                if ($matchedBrand) {
                    $brandId = $matchedBrand->id;
                    $laboratoryId = $matchedBrand->laboratory_id;
                }
            } elseif ($brandId && ! $laboratoryId) {
                $brand = Brand::find($brandId);
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
            'patient_id' => ['required', 'integer', Rule::exists('patients', 'id')->where('user_id', auth()->id())],
            'status' => ['nullable', 'integer'],
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
            'temp' => __('validation.attributes.temp'),
            'weight' => __('validation.attributes.weight'),
            'height' => __('validation.attributes.height'),
            'pressure' => __('validation.attributes.pressure'),
            'saturation' => __('validation.attributes.saturation'),
            'ppm' => __('validation.attributes.ppm'),
            'allergy' => __('validation.attributes.allergy'),
            'diagnostic' => __('validation.attributes.diagnostic'),
            'diet' => __('validation.attributes.diet'),
            'comments' => __('validation.attributes.comments'),
            'medicaments' => __('validation.attributes.medicaments'),
            'medicaments.*.dosage' => __('validation.attributes.medicaments.*.dosage'),
            'medicaments.*.frequency' => __('validation.attributes.medicaments.*.frequency'),
            'medicaments.*.duration' => __('validation.attributes.medicaments.*.duration'),
            'medicaments.*.medicament_quantity' => __('validation.attributes.medicaments.*.medicament_quantity'),
            'medicaments.*.medicament_quantity_letters' => __('validation.attributes.medicaments.*.medicament_quantity_letters'),
            'medicaments.*.recommended_brand' => __('validation.attributes.medicaments.*.recommended_brand'),
            'medicaments.*.brand_id' => __('validation.attributes.medicaments.*.brand_id'),
            'medicaments.*.laboratory_id' => __('validation.attributes.medicaments.*.laboratory_id'),
            'room_id' => __('validation.attributes.room_id'),
            'specialty_id' => __('validation.attributes.specialty_id'),
            'patient_id' => __('validation.attributes.patient_id'),
            'status' => __('validation.attributes.status'),
        ];
    }
}
