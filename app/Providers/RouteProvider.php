<?php

declare(strict_types=1);

namespace App\Providers;

use App\Config\AppConfig;

class RouteProvider
{
    private AppConfig $config;
    private array $routes = [];

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        $webRoutes = require $this->config->getRootPath() . '/routes/web.php';
        $apiRoutes = require $this->config->getRootPath() . '/routes/api.php';

        $this->routes = array_merge($webRoutes, $apiRoutes);
    }

    public function match(string $method, string $path): ?array
    {
        return $this->routes[$method . ' ' . $path] ?? null;
    }
}
