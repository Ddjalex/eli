<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// REPLIT ENVIRONMENT (PostgreSQL)
// use the DATABASE_URL environment variable
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    // Parse the DATABASE_URL
    $url = parse_url($db_url);
    $host = $url['host'];
    $port = $url['port'] ?? 5432;
    $db   = ltrim($url['path'], '/');
    $user = $url['user'];
    $pass = $url['pass'];

    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
} else {
    // Fallback or development
    die("DATABASE_URL not found.");
}

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>