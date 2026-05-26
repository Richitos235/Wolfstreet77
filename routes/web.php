<?php

return [
    'GET /' => [
        'controller' => App\Controllers\HealthController::class,
        'action' => 'status',
        'middleware' => [],
    ],
];
