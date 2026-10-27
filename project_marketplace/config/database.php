<?php
declare(strict_types=1);

function getDatabaseConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host     = getenv('DB_HOST') ?: 'mysql-20851571-bdkooper361-c43a.l.aivencloud.com';
    $port     = (int)(getenv('DB_PORT') ?: 25642);
    $dbName   = getenv('DB_NAME') ?: 'defaultdb';
    $username = getenv('DB_USER') ?: 'avnadmin';
    $password = getenv('DB_PASS') ?: 'AVNS_Rx07fh8DmReklYNj5HG';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_SSL_CA       => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        throw new RuntimeException("Database connection failed: " . $e->getMessage());
    }
}
