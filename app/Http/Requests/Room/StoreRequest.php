<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\CustomFormRequest;

class StoreRequest extends CustomFormRequest
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
            'data' => ['required', 'array'],
            'data.*.id' => ['nullable', 'numeric'],
            'data.*.name' => ['nullable', 'string'],
            'data.*.zip' => ['required', 'string'],
            'data.*.street' => ['required', 'string'],
            'data.*.colony' => ['required', 'string'],
            'data.*.state' => ['required', 'string'],
            'data.*.delegation' => ['required', 'string'],
            'data.*.n_exterior' => ['required',],
            'data.*.n_interior' => ['nullable',],
            'data.*.address' => ['nullable', 'string'],
            'data.*.phone' => ['nullable', 'string'],
            'data.*.design' => ['nullable', 'string'],
            'data.*.fav' => ['nullable'],
            'data.*.auto_email' => ['nullable'],
            'data.*.auto_whatsapp' => ['nullable'],
            'data.*.logo' => ['nullable', 'string'],
            'logo' => ['nullable', 'array'],
            'logo.*' => ['nullable', 'file', 'mimes:jpg,png'],
        ];
    }
}
