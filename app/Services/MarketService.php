<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;
use Exception;

class MarketService extends Service
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllStocks(): array
    {
        $stmt = $this->db->prepare('
            SELECT id, name, short_name, current_price, previous_price, trend, volatility, total_supply, available_supply
            FROM market_stocks
            ORDER BY name ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStockById(int $stockId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM market_stocks WHERE id = :id');
        $stmt->execute([':id' => $stockId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Fetches the latest news items from the feed.
     */
    public function getNewsFeeds(int $limit = 5): array
    {
        $stmt = $this->db->prepare('
            SELECT id, title, content, category, is_pinned, created_at
            FROM news_feed
            WHERE expires_at IS NULL OR expires_at > NOW()
            ORDER BY is_pinned DESC, created_at DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetches currently active game events.
     */
    public function getActiveEvents(): array
    {
        $stmt = $this->db->prepare('
            SELECT id, title, description, effect_type, effect_value, created_at, expires_at
            FROM game_events
            WHERE is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buyStock(int $userId, int $stockId, int $quantity): array
    {
        if ($quantity <= 0) return ['success' => false, 'error' => 'Množství musí být kladné.'];

        try {
            $this->db->beginTransaction();

            // 1. Get Stock Data
            $stmt = $this->db->prepare('SELECT * FROM market_stocks WHERE id = :id FOR UPDATE');
            $stmt->execute([':id' => $stockId]);
            $stock = $stmt->fetch();

            if (!$stock) throw new Exception('Akcie neexistuje.');
            if ($stock['available_supply'] < $quantity) throw new Exception('Nedostatečná nabídka na trhu.');

            $totalCost = $stock['current_price'] * $quantity;

            // 2. Get User Data
            $stmt = $this->db->prepare('SELECT money FROM users WHERE id = :id FOR UPDATE');
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();

            if ($user['money'] < $totalCost) throw new Exception('Nedostatek financí.');

            // 3. Deduct Money
            $stmt = $this->db->prepare('UPDATE users SET money = money - :cost WHERE id = :id');
            $stmt->execute([':cost' => $totalCost, ':id' => $userId]);

            // 4. Update Market Supply
            $stmt = $this->db->prepare('UPDATE market_stocks SET available_supply = available_supply - :qty WHERE id = :id');
            $stmt->execute([':qty' => $quantity, ':id' => $stockId]);

            // 5. Update Player Portfolio
            $stmt = $this->db->prepare('
                INSERT INTO player_stocks (user_id, stock_id, quantity, buy_price_total)
                VALUES (:uid, :sid, :qty, :cost)
                ON DUPLICATE KEY UPDATE 
                quantity = quantity + :qty,
                buy_price_total = buy_price_total + :cost
            ');
            $stmt->execute([':uid' => $userId, ':sid' => $stockId, ':qty' => $quantity, ':cost' => $totalCost]);

            // 6. Log Transaction
            $stmt = $this->db->prepare('
                INSERT INTO transactions (user_id, transaction_type, amount, related_stock_id, description, created_at)
                VALUES (:uid, "buy", :amount, :sid, :desc, NOW())
            ');
            $stmt->execute([
                ':uid' => $userId,
                ':amount' => -$totalCost,
                ':sid' => $stockId,
                ':desc' => "Nákup {$quantity}x {$stock['short_name']}"
            ]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sellStock(int $userId, int $stockId, int $quantity): array
    {
        if ($quantity <= 0) return ['success' => false, 'error' => 'Množství musí být kladné.'];

        try {
            $this->db->beginTransaction();

            // 1. Get Player Holdings
            $stmt = $this->db->prepare('SELECT quantity FROM player_stocks WHERE user_id = :uid AND stock_id = :sid FOR UPDATE');
            $stmt->execute([':uid' => $userId, ':sid' => $stockId]);
            $holding = $stmt->fetch();

            if (!$holding || $holding['quantity'] < $quantity) throw new Exception('Nevlastníte dostatek akcií.');

            // 2. Get Stock Price
            $stmt = $this->db->prepare('SELECT * FROM market_stocks WHERE id = :id FOR UPDATE');
            $stmt->execute([':id' => $stockId]);
            $stock = $stmt->fetch();

            $totalGain = $stock['current_price'] * $quantity;

            // 3. Add Money to User
            $stmt = $this->db->prepare('UPDATE users SET money = money + :gain WHERE id = :id');
            $stmt->execute([':gain' => $totalGain, ':id' => $userId]);

            // 4. Update Market Supply
            $stmt = $this->db->prepare('UPDATE market_stocks SET available_supply = available_supply + :qty WHERE id = :id');
            $stmt->execute([':qty' => $quantity, ':id' => $stockId]);

            // 5. Update Player Portfolio
            $stmt = $this->db->prepare('UPDATE player_stocks SET quantity = quantity - :qty WHERE user_id = :uid AND stock_id = :sid');
            $stmt->execute([':qty' => $quantity, ':uid' => $userId, ':sid' => $stockId]);

            // 6. Log Transaction
            $stmt = $this->db->prepare('
                INSERT INTO transactions (user_id, transaction_type, amount, related_stock_id, description, created_at)
                VALUES (:uid, "sell", :amount, :sid, :desc, NOW())
            ');
            $stmt->execute([
                ':uid' => $userId,
                ':amount' => $totalGain,
                ':sid' => $stockId,
                ':desc' => "Prodej {$quantity}x {$stock['short_name']}"
            ]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateMarketPrices(): void
    {
        $stocks = $this->getAllStocks();
        foreach ($stocks as $stock) {
            $volatility = (float)$stock['volatility'];
            $changePercent = (mt_rand(-100, 100) / 100) * $volatility;
            $newPrice = $stock['current_price'] * (1 + ($changePercent / 100));
            
            // Ensure price doesn't drop below 1.00
            $newPrice = max(1.00, round($newPrice, 2));
            $trend = $newPrice >= $stock['current_price'] ? 'rising' : 'falling';

            $stmt = $this->db->prepare('
                UPDATE market_stocks 
                SET previous_price = current_price, 
                    current_price = :new_price, 
                    trend = :trend,
                    available_supply = LEAST(total_supply, available_supply + (total_supply * 0.02))
                WHERE id = :id
            ');
            $stmt->execute([
                ':new_price' => $newPrice,
                ':trend' => $trend,
                ':id' => $stock['id']
            ]);

            // Log to history
            $stmt = $this->db->prepare('INSERT INTO market_history (stock_id, price, created_at) VALUES (:sid, :price, NOW())');
            $stmt->execute([':sid' => $stock['id'], ':price' => $newPrice]);
        }
    }
}