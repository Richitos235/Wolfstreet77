<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Helpers\SessionHelper;
use PDO;

class AuthService extends Service
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function register(string $username, string $email, string $password, string $roleType): array
    {
        if (!$this->validateUsername($username)) {
            return ['success' => false, 'error' => 'Username musí být 3-50 znaků dlouhý'];
        }

        if (!$this->validateEmail($email)) {
            return ['success' => false, 'error' => 'Neplatná email adresa'];
        }

        if (!$this->validatePassword($password)) {
            return ['success' => false, 'error' => 'Heslo musí být minimálně 6 znaků'];
        }

        if (!$this->isValidRoleType($roleType)) {
            return ['success' => false, 'error' => 'Neplatný typ role'];
        }

        if ($this->userExists($username, $email)) {
            return ['success' => false, 'error' => 'Uživatel s tímto jménem nebo emailem již existuje'];
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare('
                INSERT INTO users 
                (username, email, password, role_type, money, bank_money, strength, intelligence, tolerance, next_tick)
                VALUES 
                (:username, :email, :password, :role_type, 10000.00, 0.00, 10, 10, 10, DATE_ADD(NOW(), INTERVAL 6 HOUR))
            ');

            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role_type' => $roleType,
            ]);

            $userId = (int)$this->db->lastInsertId();

            SessionHelper::start();
            SessionHelper::set('user_id', $userId);
            SessionHelper::set('username', $username);
            SessionHelper::set('user', [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'role_type' => $roleType,
            ]);

            return ['success' => true, 'user_id' => $userId];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Chyba při registraci: ' . $e->getMessage()];
        }
    }

    public function login(string $username, string $password): array
    {
        $stmt = $this->db->prepare('
            SELECT id, username, email, password, role_type 
            FROM users 
            WHERE username = :username OR email = :email
        ');

        $stmt->execute([':username' => $username, ':email' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Neplatné jméno nebo heslo'];
        }

        SessionHelper::start();
        SessionHelper::set('user_id', $user['id']);
        SessionHelper::set('username', $user['username']);
        SessionHelper::set('user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role_type' => $user['role_type'],
        ]);

        return ['success' => true, 'user_id' => $user['id']];
    }

    public function logout(): void
    {
        SessionHelper::destroy();
    }

    private function validateUsername(string $username): bool
    {
        return strlen($username) >= 3 && strlen($username) <= 50 && preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }

    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validatePassword(string $password): bool
    {
        return strlen($password) >= 6;
    }

    private function isValidRoleType(string $roleType): bool
    {
        return in_array($roleType, ['trader', 'gangster', 'pimp'], true);
    }

    private function userExists(string $username, string $email): bool
    {
        $stmt = $this->db->prepare('
            SELECT 1 FROM users 
            WHERE username = :username OR email = :email
        ');

        $stmt->execute([':username' => $username, ':email' => $email]);
        return $stmt->fetch() !== false;
    }
}
