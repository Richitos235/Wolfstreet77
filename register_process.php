<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';

use App\Helpers\SessionHelper;
use App\Config\Database;

SessionHelper::start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!SessionHelper::verifyCSRFToken($token)) {
    die("Kritická chyba: Neplatný CSRF token.");
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role_type'] ?? 'trader';

if (empty($username) || empty($email) || empty($password)) {
    header('Location: register.php?error=empty_fields');
    exit;
}

try {
    $db = Database::connect();

    $stmt = $db->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
    $stmt->execute(['u' => $username, 'e' => $email]);
    
    if ($stmt->fetch()) {
        header('Location: register.php?error=exists');
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password, role_type, money, bank_money, current_day, next_tick, created_at) 
            VALUES (:username, :email, :password, :role, 10000.00, 0.00, 1, DATE_ADD(NOW(), INTERVAL 6 HOUR), NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'username' => $username,
        'email'    => $email,
        'password' => $hashedPassword,
        'role'     => $role
    ]);

    header('Location: login.php?success=registered');
    exit;

} catch (PDOException $e) {
    error_log("Chyba při registraci: " . $e->getMessage());
    header('Location: register.php?error=server_error');
    exit;
}