<?php

return  [
    've' => [
        'medic_registration' => [
            'label' => 'Registro Médico',
            'rules' => 'required|numeric|digits:7',
        ],
        'medic_society' => [
            'label' => 'Sociedad Médica',
            'rules' => 'required|string|max:255',
        ],
    ],
];