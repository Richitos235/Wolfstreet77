<?php
// Konfigurace připojení
$host     = '127.0.0.1'; // nebo 'localhost'
$db       = 'wolf';
$user     = 'root';      // pokud používáš jiného uživatele, změň ho zde
$pass     = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Pokus o připojení
    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "<h2>✅ Připojení k databázi '$db' bylo úspěšné!</h2>";

    // Malý test - vypíšeme počet akcií v trhu
    $stmt = $pdo->query("SELECT COUNT(*) as pocet FROM market_stocks");
    $row = $stmt->fetch();
    
    echo "V databázi je aktuálně monitorováno <strong>" . $row['pocet'] . "</strong> akcií.";

} catch (\PDOException $e) {
    // Pokud nastane chyba, vypíše se zde
    echo "<h2>❌ Chyba připojení:</h2>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>