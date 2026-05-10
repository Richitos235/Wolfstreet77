<?php

declare(strict_types=1);

namespace App\Providers;

use App\Config\AppConfig;
use App\Controllers\HealthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

class Application
{
    private AppConfig $config;
    private RouteProvider $routes;

    public function __construct(AppConfig $config, RouteProvider $routes)
    {
        $this->config = $config;
        $this->routes = $routes;
    }

    public function run(): void
    {
        $requestUri = strtok($_SERVER['REQUEST_URI'], '?') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $route = $this->routes->match($method, $requestUri);

        if ($route === null) {
            http_response_code(404);
            echo 'Route not found';
            return;
        }

        $controllerClass = $route['controller'];
        $action = $route['action'];

        $controller = new $controllerClass();
        $this->applyMiddleware($route['middleware'] ?? []);
        $controller->{$action}();
    }

    private function applyMiddleware(array $middleware): void
    {
        foreach ($middleware as $middlewareClass) {
            $instance = new $middlewareClass();
            $instance->handle();
        }
    }
}
