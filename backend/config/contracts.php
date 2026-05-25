<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Estados de Contrato
    |--------------------------------------------------------------------------
    |
    | Configuración visual (etiquetas, colores e iconos) para los estados
    | importados desde el CSV semanal de contratos.
    |
    | El orden de este array define la prioridad lógica en el Dashboard.
    |
    */

    'statuses' => [
        'Ofertado' => [
            'label' => 'Ofertado',
            'color' => 'azul claro',
            'icon' => 'fa-solid fa-file-signature'
        ],
        'En negociación' => [
            'label' => 'En negociación',
            'color' => 'azul intenso',
            'icon' => 'fa-solid fa-handshake'
        ],
        'Aceptado por el cliente' => [
            'label' => 'Aceptado',
            'color' => 'morado',
            'icon' => 'fa-solid fa-circle-check'
        ],
        'Procesado (M) - Pte fact.' => [
            'label' => 'Procesado',
            'color' => 'amarillo',
            'icon' => 'fa-solid fa-gears'
        ],
        'Facturado - Pte proc. (M)' => [
            'label' => 'Facturado',
            'color' => 'naranja',
            'icon' => 'fa-solid fa-file-invoice-dollar'
        ],
        'Cerrado' => [
            'label' => 'Cerrado',
            'color' => 'verde',
            'icon' => 'fa-solid fa-lock'
        ],
        'Baja' => [
            'label' => 'Baja',
            'color' => 'rojo apagado',
            'icon' => 'fa-solid fa-circle-xmark'
        ],
        'Renovación Tardía' => [
            'label' => 'Renov. Tardía',
            'color' => 'rojo oscuro',
            'icon' => 'fa-solid fa-clock-rotate-left'
        ],
        'vacio' => [
            'label' => 'Sin estado',
            'color' => 'gris',
            'icon' => 'fa-regular fa-circle-question'
        ],
    ],
];
