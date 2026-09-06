<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientMediaUpdateRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => [
                'sometimes',
                'string',
                Rule::in(File::VALID_CATEGORIES),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'evolution_stage' => [
                'nullable',
                'string',
                Rule::in(File::VALID_STAGES),
            ],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'category' => __('validation.attributes.medical_category'),
            'title' => __('validation.attributes.title'),
            'description' => __('validation.attributes.attachment_description'),
            'evolution_stage' => __('validation.attributes.evolution_stage'),
            'meta' => __('validation.attributes.meta'),
        ];
    }
}
