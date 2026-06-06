<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultingRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
            'n_exterior' => ['required'],
            'n_interior' => ['required'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'fav' => ['nullable', 'boolean'],
            'auto_email' => ['nullable', 'boolean'],
            'auto_whatsapp' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'file', 'mimes:jpg,png'],
        ];
    }
}
