<?php
declare(strict_types=1);

function getDatabaseConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

$host     = 'mysql-20851571-bdkooper361-c43a.l.aivencloud.com';
$port     = 25642;
$dbName   = 'defaultdb';
$username = 'avnadmin';
$password = 'AVNS_Rx07fh8DmReklYNj5HG'; // Replace with the exact password copied from Step 1

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

        // Auto-initialize schema if tables do not exist
        $check = $pdo->query("SHOW TABLES LIKE 'listings'")->rowCount();
        if ($check === 0) {
            $schemaFile = dirname(__DIR__) . '/database/schema.sql';
            if (file_exists($schemaFile)) {
                $rawSql = file_get_contents($schemaFile);
                $statements = array_filter(array_map('trim', explode(';', $rawSql)));
                foreach ($statements as $stmt) {
                    if (!empty($stmt)) {
                        $pdo->exec($stmt);
                    }
                }
            }
        }

        return $pdo;
    } catch (PDOException $e) {
        throw new RuntimeException("Database connection failed: " . $e->getMessage());
    }
}
