<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CPANEL ENVIRONMENT (MySQL)
$host = 'localhost';
$db   = 'neodigqi_Eli';         // በፎቶው ላይ ባለው መሰረት (g ከዚያ q)
$user = 'neodigqi_eleni_user2'; // በፎቶው ላይ ባለው መሰረት (g ከዚያ q)
$pass = 'a1e2y3t4h5';           
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

echo "<html><body style='font-family:sans-serif; padding:20px;'>";
echo "<h1>Database Connection Diagnostic</h1>";
echo "<hr>";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "<div style='color:green; font-weight:bold; font-size:1.2em;'>✅ SUCCESS: Connected to the database!</div>";
    
    // Check for tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "<h3>Tables found in '$db':</h3><ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange;'>⚠️ Connected, but no tables found. Did you import your SQL file?</p>";
    }

} catch (PDOException $e) {
    echo "<div style='color:red; font-weight:bold; font-size:1.2em;'>❌ CONNECTION FAILED!</div>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    echo "<h3>Troubleshooting Checklist:</h3>";
    echo "<ol>
            <li><strong>Check Privileges:</strong> In cPanel, go to <b>MySQL Databases</b>. Scroll to <b>'Add User To Database'</b>. Ensure <u>$user</u> is added to <u>$db</u> with <b>ALL PRIVILEGES</b>.</li>
            <li><strong>Check Password:</strong> Ensure the password in this script matches what you set in cPanel.</li>
            <li><strong>Check Spelling:</strong> Verify the 'q' in 'neodiqgi' vs 'neodigqi'.</li>
          </ol>";
}

echo "<hr><p>Testing complete.</p></body></html>";
?>