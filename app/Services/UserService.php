<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class UserService extends Service
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getUserById(int $userId): ?array
    {
        $stmt = $this->db->prepare('
            SELECT id, username, email, role_type, money, bank_money, strength, intelligence, tolerance, 
                   current_day, game_ticks, next_tick, last_tick, created_at
            FROM users 
            WHERE id = :id AND is_active = TRUE
        ');

        $stmt->execute([':id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function getUserPortfolio(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT ps.id, ps.stock_id, ms.name, ms.short_name, ps.quantity, ps.buy_price_total, 
                   ms.current_price, (ps.quantity * ms.current_price) as current_value
            FROM player_stocks ps
            JOIN market_stocks ms ON ps.stock_id = ms.id
            WHERE ps.user_id = :user_id AND ps.quantity > 0
            ORDER BY ms.name ASC
        ');

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getPortfolioValue(int $userId): float
    {
        $stmt = $this->db->prepare('
            SELECT COALESCE(SUM(ps.quantity * ms.current_price), 0) as total_value
            FROM player_stocks ps
            JOIN market_stocks ms ON ps.stock_id = ms.id
            WHERE ps.user_id = :user_id AND ps.quantity > 0
        ');

        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return (float)($result['total_value'] ?? 0);
    }

    public function getTotalAssets(int $userId): float
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return 0;
        }

        $cash = (float)$user['money'] + (float)$user['bank_money'];
        $portfolio = $this->getPortfolioValue($userId);
        return $cash + $portfolio;
    }

    public function getRecentTransactions(int $userId, int $limit = 10): array
    {
        $stmt = $this->db->prepare('
            SELECT transaction_type, amount, balance_after, description, created_at
            FROM transactions
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        ');

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
