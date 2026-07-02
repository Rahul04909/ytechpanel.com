<?php
/**
 * YTech Panels — PDO Database Connection
 * Uses environment variables loaded from .env via bootstrap.php
 * Returns a singleton PDO instance.
 */

require_once __DIR__ . '/bootstrap.php';

/**
 * Returns a singleton PDO connection.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $name = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
        $msg   = $debug ? $e->getMessage() : 'Database connection failed. Please try again later.';
        error_log('Database connection failed: ' . $e->getMessage());
        if (!headers_sent()) {
            http_response_code(500);
        }
        throw new RuntimeException($msg, 0, $e);
    }

    return $pdo;
}
