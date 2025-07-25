<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\CustomFormRequest;

class RegisterRequest extends CustomFormRequest
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
            'first_name' => ['required', 'string'],
            'last_name1' => ['required', 'string'],
            'last_name2' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['nullable', 'string'],
            'phone1' => ['nullable', 'json'],
            'phone2' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'fesa' => ['required',],
            'rooms' => ['required', 'array'],
            'specializations' => ['required', 'array'],
            'rooms.*.id_ext' => ['nullable'],
            'rooms.*.name' => ['nullable', 'string'],
            'rooms.*.zip' => ['required', 'string'],
            'rooms.*.street' => ['required', 'string'],
            'rooms.*.colony' => ['required', 'string'],
            'rooms.*.state' => ['required', 'string'],
            'rooms.*.delegation' => ['required', 'string'],
            'rooms.*.n_exterior' => ['required',],
            'rooms.*.n_interior' => ['nullable',],
            'rooms.*.address' => ['nullable', 'string'],
            'rooms.*.phone' => ['nullable', 'string'],
            'rooms.*.fav' => ['nullable'],
            'rooms.*.auto_email' => ['nullable'],
            'rooms.*.auto_whatsapp' => ['nullable'],
            'rooms.*.design' => ['nullable', 'string'],
            'specializations.*.name' => ['required', 'string'],
            'specializations.*.id_ext' => ['nullable'],
            'specializations.*.identification' => ['required', 'unique:specializations'],
            'specializations.*.university' => ['nullable', 'string'],
            'specializations.*.logo' => ['nullable', 'string'],
            'logo_room' => ['nullable', 'array'],
            'logo_spec' => ['nullable', 'array'],
            'logo_room.*' => ['nullable', 'file', 'mimes:jpg,png'],
            'logo_spec.*' => ['nullable', 'file', 'mimes:jpg,png'],

            'idCX' => ['nullable'],
            'clienteEcommerce' => ['nullable'],
        ];
    }
}
