<?php

declare(strict_types=1);

namespace App\Controllers;

class HealthController extends Controller
{
    public function status(): void
    {
        $this->jsonResponse([
            'status' => 'ok',
            'environment' => getenv('APP_ENV') ?: 'production',
            'timestamp' => time(),
        ]);
    }
}
