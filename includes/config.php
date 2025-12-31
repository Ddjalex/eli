<?php
// Unified Database configuration for Replit (PostgreSQL) and cPanel (MySQL)
$db_url = getenv('DATABASE_URL');

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
    // NOTE: If you still get 'Access denied', please check:
    // 1. The database name is exactly 'neodiqgi_Eli' (check for typos like q vs g)
    // 2. The user 'neodiqqi_eleni_user' is added to 'neodiqgi_Eli' with ALL PRIVILEGES
    // 3. The password 'a1e2y3t4h5' is correct
    $host = '127.0.0.1'; // Using 127.0.0.1 instead of localhost can sometimes bypass socket issues
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
    die("Database connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>