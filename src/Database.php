<?php

namespace App;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
            $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'gcsoft_homolog';
            $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'gcsoft';
            $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? 'gcsoft123';

            self::$instance = new PDO(
                "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        }
        return self::$instance;
    }
}
