<?php

return [
    'GET /api/health' => [
        'controller' => App\Controllers\HealthController::class,
        'action' => 'status',
        'middleware' => [],
    ],
];
