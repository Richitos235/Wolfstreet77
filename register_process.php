<?php

declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';

use App\Helpers\SessionHelper;
use App\Config\Database;

SessionHelper::start();

// 1. Kontrola, zda data přišla přes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// 2. Ověření CSRF tokenu (ochrana proti útokům)
$token = $_POST['csrf_token'] ?? '';
if (!SessionHelper::verifyCSRFToken($token)) {
    die("Kritická chyba: Neplatný CSRF token.");
}

// 3. Načtení a vyčištění dat
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role_type'] ?? 'trader';

// 4. Základní validace
if (empty($username) || empty($email) || empty($password)) {
    header('Location: register.php?error=empty_fields');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=invalid_email');
    exit;
}

if (strlen($password) < 6) {
    header('Location: register.php?error=password_too_short');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // 5. Kontrola, zda uživatel nebo email již neexistuje
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
    $stmt->execute(['u' => $username, 'e' => $email]);
    
    if ($stmt->fetch()) {
        header('Location: register.php?error=exists');
        exit;
    }

    // 6. Zahashování hesla (nikdy neukládat v čistém textu!)
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 7. Vložení uživatele do databáze
    // Nastavíme startovní hodnoty: Den 1, příští Tick za 6 hodin
    $sql = "INSERT INTO users (username, email, password, role_type, money, bank_money, current_day, next_tick, created_at) 
            VALUES (:username, :email, :password, :role, 10000.00, 0.00, 1, DATE_ADD(NOW(), INTERVAL 6 HOUR), NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'username' => $username,
        'email'    => $email,
        'password' => $hashedPassword,
        'role'     => $role
    ]);

    // 8. Úspěch - přesměrujeme na login
    header('Location: login.php?success=registered');
    exit;

} catch (PDOException $e) {
    // Logování chyby (v produkci nezobrazovat $e->getMessage())
    error_log("Chyba při registraci: " . $e->getMessage());
    header('Location: register.php?error=server_error');
    exit;
}