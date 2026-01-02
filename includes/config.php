<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CPANEL ENVIRONMENT (MySQL)
$host = 'localhost';
$db   = 'neodigqi_Eli';         
$user = 'neodigqi_eleni_user2'; 
$pass = 'a1e2y3t4h5';           
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Silent fail or custom error for production
    die("Connection failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>