<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Providers\Application;
use App\Providers\RouteProvider;
use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);
$dotenv = Dotenv::createImmutable($rootPath);
$dotenv->safeLoad();

$appConfig = new AppConfig($rootPath);
$routeProvider = new RouteProvider($appConfig);

return new Application($appConfig, $routeProvider);
