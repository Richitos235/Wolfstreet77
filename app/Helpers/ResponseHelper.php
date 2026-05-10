<?php

declare(strict_types=1);

namespace App\Helpers;

class ResponseHelper
{
    public static function json(array $payload, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
