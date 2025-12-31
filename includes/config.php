<?php
// Database configuration using PostgreSQL (from Replit environment)
$db_url = getenv('DATABASE_URL');

// Robust parsing for DATABASE_URL
if (preg_match('/^postgres(?:ql)?:\/\/([^:]+):([^@]+)@([^:]+):(\d+)\/(.+)$/', $db_url, $matches)) {
    $user = $matches[1];
    $pass = $matches[2];
    $host = $matches[3];
    $port = $matches[4];
    $db   = $matches[5];
} else {
    // Fallback to parse_url if regex fails, but with better error handling
    $db_opts = parse_url($db_url);
    $host = $db_opts['host'] ?? 'localhost';
    $port = $db_opts['port'] ?? '5432';
    $db   = ltrim($db_opts['path'] ?? '', '/');
    $user = $db_opts['user'] ?? '';
    $pass = $db_opts['pass'] ?? '';
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>