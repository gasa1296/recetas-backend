<?php

use Illuminate\Validation\Rule;

return [
    'VE' => [
        'medic_registration' => [
            'label' => 'Registro Médico',
            'rules' => ['required', 'numeric', 'digits:7',
                Rule::unique('specialties', 'identification->medic_registration')->ignore($specialty->id)],
        ],
        'medic_society' => [
            'label' => 'Sociedad Médica',
            'rules' => ['required', 'string', 'max:255',
                Rule::unique('specialties', 'identification->medic_society')->ignore($specialty->id)],
        ],
    ],
];
