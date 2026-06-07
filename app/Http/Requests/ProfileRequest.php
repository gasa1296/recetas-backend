<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\JsonValidationResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    use JsonValidationResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name1' => ['nullable', 'string'],
            'last_name2' => ['nullable', 'string'],
            'phone' => ['nullable', 'array'],
            'phone.*' => ['string'],
            'gender' => ['required', 'string'],
            'password' => ['string'],
        ];
    }
}
