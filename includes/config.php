<?php
// Database configuration using PostgreSQL (from Replit environment)
$db_url = getenv('DATABASE_URL');
$db_opts = parse_url($db_url);

$host = $db_opts['host'];
$port = $db_opts['port'];
$db   = ltrim($db_opts['path'], '/');
$user = $db_opts['user'];
$pass = $db_opts['pass'];

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

session_start();
?>