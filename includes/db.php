<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = (string) (getenv('DB_HOST') ?: DB_HOST);
    $name = (string) (getenv('DB_NAME') ?: DB_NAME);
    $user = (string) (getenv('DB_USER') ?: DB_USER);
    $pass = (string) (getenv('DB_PASS') ?: DB_PASS);
    $charset = (string) (getenv('DB_CHARSET') ?: DB_CHARSET);

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $name, $charset);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
