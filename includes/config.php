<?php
// Unified Database configuration for Replit (PostgreSQL) and cPanel (MySQL)
$db_url = getenv('DATABASE_URL');

// Enable error logging for cPanel debugging
if (!$db_url) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__FILE__) . '/../error_log.php');
}

if ($db_url) {
    // REPLIT ENVIRONMENT (PostgreSQL)
    if (preg_match('/^postgres(?:ql)?:\/\/([^:]+):([^@]+)@([^:]+):(\d+)\/(.+)$/', $db_url, $matches)) {
        $user = $matches[1];
        $pass = $matches[2];
        $host = $matches[3];
        $port = $matches[4];
        $db   = $matches[5];
    } else {
        $db_opts = parse_url($db_url);
        $host = $db_opts['host'] ?? 'localhost';
        $port = $db_opts['port'] ?? '5432';
        $db   = ltrim($db_opts['path'] ?? '', '/');
        $user = $db_opts['user'] ?? '';
        $pass = $db_opts['pass'] ?? '';
    }
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
} else {
    // CPANEL ENVIRONMENT (MySQL)
    $host = '127.0.0.1';
    $user = 'neodiqqi_eleni_user';
    $pass = 'a1e2y3t4h5';
    $db   = 'neodiqgi_Eli';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    // Log connection errors specifically
    if (!$db_url) {
        error_log("Database connection failed: " . $e->getMessage());
    }
    die("Database connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>