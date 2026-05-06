<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('LISTEO_DB_HOST') ?: '127.0.0.1';
    $port = getenv('LISTEO_DB_PORT') ?: '3306';
    $database = getenv('LISTEO_DB_NAME') ?: 'listeo';
    $username = getenv('LISTEO_DB_USER') ?: 'root';
    $password = getenv('LISTEO_DB_PASS') ?: 'root123';

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
