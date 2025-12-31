<?php
// Database configuration for MySQL (cPanel)
// INSTRUCTIONS: Update these values with your cPanel MySQL credentials

$host = 'localhost';           // Usually 'localhost' for cPanel
$username = 'your_mysql_user'; // Your MySQL username from cPanel
$password = 'your_mysql_pass'; // Your MySQL password from cPanel
$database = 'your_db_name';    // Your database name from cPanel

$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
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
