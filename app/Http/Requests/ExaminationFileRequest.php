<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExaminationFileRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:25600', // 25MB max
                'mimes:pdf,jpg,jpeg,png,webp',
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'file' => 'archivo adjunto (PDF o imagen)',
            'title' => 'título del adjunto',
            'description' => 'descripción u observaciones',
        ];
    }
}
