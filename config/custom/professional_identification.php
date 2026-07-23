<?php

return [
    'medic_registration' => [
        'label' => 'Registro Médico',
        'rules' => ['required', 'numeric', 'digits:7', 'unique:specialties,identification->medic_registration'],
    ],
    'medic_society' => [
        'label' => 'Sociedad Médica',
        'rules' => ['required', 'string', 'max:255', 'unique:specialties,identification->medic_society'],
    ],
];
