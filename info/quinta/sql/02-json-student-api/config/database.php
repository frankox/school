<?php

declare(strict_types=1);

return static function (): PDO {
    $dsn = getenv('DB_DSN') ?: 'mysql:host=127.0.0.1;dbname=school;charset=utf8mb4';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
};
