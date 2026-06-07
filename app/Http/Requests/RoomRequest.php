<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
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
        return [
            'name' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'street' => ['required', 'string'],
            'colony' => ['required', 'string'],
            'state' => ['required', 'string'],
            'delegation' => ['required', 'string'],
            'n_exterior' => ['required', 'string'],
            'n_interior' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'array'],
            'phone.*' => ['string'],
            'fav' => ['nullable', 'boolean'],
            'auto_email' => ['nullable', 'boolean'],
            'auto_whatsapp' => ['nullable', 'boolean'],
        ];
    }
}
