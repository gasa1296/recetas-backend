<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialtyRequest extends FormRequest
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
        $user = auth()->user();
        $conf = config('custom.professional_identification.' . $user->country_code, []);
        $specialtyIdRules = [];
        foreach ($conf as $key => $value) {
            $specialtyIdRules['specialty.identification.' . $key] = $value['rules'] ?? [];
        }
        return [
            'name' => ['required', 'string', 'max:255'],
            'identification' => ['required', 'string', 'max:255'],
        ] + $specialtyIdRules;
    }
}
