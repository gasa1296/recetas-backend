<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use App\Models\Examination;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExaminationRequest extends FormRequest
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
    public function prepareForValidation(): void
    {
        if (! empty($_POST)) {
            $this->merge($_POST);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(Examination::VALID_TYPES)],
            'examined_at' => ['nullable', 'date'],
            'laboratory_name' => ['nullable', 'string', 'max:255'],
            'findings' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'string', Rule::in(Examination::VALID_STATUSES)],
            'prescription_id' => ['nullable', 'integer', 'exists:prescriptions,id'],
            'file' => [
                'nullable',
                'file',
                'max:25600', // 25MB max
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre del examen',
            'type' => 'tipo de examen',
            'examined_at' => 'fecha de realización',
            'laboratory_name' => 'laboratorio o centro de diagnóstico',
            'findings' => 'hallazgos u observaciones',
            'status' => 'estado del informe',
            'prescription_id' => 'récipe médico asociado',
            'file' => 'archivo adjunto (PDF o imagen)',
        ];
    }
}
