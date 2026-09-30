<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    public static function connect(): PDO
    {
        $host = Environment::get('DB_HOST');
        $database = Environment::get('DB_DATABASE');
        $username = Environment::get('DB_USERNAME');

        if ($host === '' || $database === '' || $username === '') {
            throw new \RuntimeException('Database configuration is incomplete.');
        }

        $port = Environment::get('DB_PORT', '3306');
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

        return new PDO($dsn, $username, Environment::get('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
