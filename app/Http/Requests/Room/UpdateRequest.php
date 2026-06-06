<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\CustomFormRequest;

class UpdateRequest extends CustomFormRequest
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
            'name' => ['nullable', 'string'],
            'zip' => ['required', 'string'],
            'street' => ['required', 'string'],
            'colony' => ['required', 'string'],
            'state' => ['required', 'string'],
            'delegation' => ['required', 'string'],
            'n_exterior' => ['required'],
            'n_interior' => ['nullable'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'fav' => ['nullable'],
            'auto_email' => ['nullable'],
            'auto_whatsapp' => ['nullable'],
            'design' => ['nullable', 'string'],
            'logo' => ['nullable', 'file', 'mimes:jpg,png'],
        ];
    }
}
