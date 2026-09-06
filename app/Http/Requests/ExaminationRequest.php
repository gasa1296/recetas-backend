<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use App\Models\Examination;
use App\Models\Patient;
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
        return auth()->check();
    }


    public function rules(): array
    {
        $patient = $this->route('patient');
        $patientId = $patient instanceof Patient ? $patient->id : $patient;

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(Examination::VALID_TYPES)],
            'examined_at' => ['nullable', 'date'],
            'laboratory_name' => ['nullable', 'string', 'max:255'],
            'findings' => ['nullable', 'string', 'max:10000'],
            'status' => ['nullable', 'string', Rule::in(Examination::VALID_STATUSES)],
            'prescription_id' => [
                'nullable',
                'integer',
                Rule::exists('prescriptions', 'id')
                    ->where('user_id', auth()->id())
                    ->when($patientId, fn ($rule) => $rule->where('patient_id', $patientId)),
            ],
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
            'name' => __('validation.attributes.examination_name'),
            'type' => __('validation.attributes.examination_type'),
            'examined_at' => __('validation.attributes.examined_at'),
            'laboratory_name' => __('validation.attributes.laboratory_name'),
            'findings' => __('validation.attributes.findings'),
            'status' => __('validation.attributes.report_status'),
            'prescription_id' => __('validation.attributes.prescription_id'),
            'file' => __('validation.attributes.attached_file'),
        ];
    }
}
