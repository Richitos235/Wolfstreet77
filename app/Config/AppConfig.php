<?php

declare(strict_types=1);

namespace App\Config;

class AppConfig
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    public function getPublicPath(): string
    {
        return $this->rootPath . '/public';
    }

    public function getConfigPath(): string
    {
        return $this->rootPath . '/config';
    }

    public function getDatabaseConfig(): array
    {
        return require $this->getConfigPath() . '/database.php';
    }
}
