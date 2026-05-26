<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

abstract class Repository
{
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    protected function getConnection(): PDO
    {
        return $this->connection;
    }
}
