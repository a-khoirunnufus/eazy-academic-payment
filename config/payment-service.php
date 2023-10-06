<?php

return [
    'payment_service_url' => env('PAYMENT_SERVICE_URL', 'https://eazy-service.btpdev.my.id/api/academic-payment'),

    'payment_type' => [
        'paket' => [
            'value' => '1',
            'title' => 'Paket'
        ],
        'sks' => [
            'value' => '2',
            'title' => 'Per SKS'
        ],
        'matakuliah' => [
            'value' => '3',
            'title' => 'Per Matakuliah'
        ],
    ],
];
