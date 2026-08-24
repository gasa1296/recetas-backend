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
    // Configuración de firma digital
    'signature' => [
        // Certificado por defecto (fallback si el usuario no tiene certificado propio)
        'default_certificate' => [
            'path' => 'docker-compose/nginx/certs/recetas.localhost.crt',
            'key_path' => 'docker-compose/nginx/certs/recetas.localhost.key',
        ],
        // Time Stamping Authority (TSA)
        'tsa' => [
            'enabled' => env('TSA_ENABLED', false),
            'url' => env('TSA_URL', 'http://timestamp.digicert.com'),
            // Hash algorithm: 'sha256', 'sha384', 'sha512'
            'hash_algorithm' => env('TSA_HASH_ALGORITHM', 'sha256'),
        ],
    ],
    // Configuración de certificados X.509
    'certificate' => [
        // Días de validez del certificado
        'validity_days' => (int) env('CERTIFICATE_VALIDITY_DAYS', 365),
        // Días antes de expirar para refrescar automáticamente
        'refresh_days_before_expiry' => (int) env('CERTIFICATE_REFRESH_DAYS', 5),
    ],
];
