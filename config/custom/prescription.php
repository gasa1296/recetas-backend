<?php

return [
    'status' => [
        '0' => 'draft',
        '1' => 'active',
        '2' => 'partially_dispensed',
        '3' => 'fully_dispensed',
        '4' => 'expired',
        '5' => 'nulled',
    ],
    'status_keys' => [
        'draft' => '0',
        'active' => '1',
        'partially_dispensed' => '2',
        'fully_dispensed' => '3',
        'expired' => '4',
        'nulled' => '5',
    ],
    // colocar dias en orden descendente, para que el primer match sea el que se tome
    'expiration_days' => [
        'default' => 30,
        'Antibiotico' => 7,
        'Ansiolitico' => 0,
    ],
];
