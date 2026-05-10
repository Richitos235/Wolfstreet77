<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

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
            SELECT id, name, short_name, current_price, previous_price, trend, volatility
            FROM market_stocks
            ORDER BY name ASC
        ');

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStockById(int $stockId): ?array
    {
        $stmt = $this->db->prepare('
            SELECT id, name, short_name, current_price, previous_price, min_price, max_price, 
                   trend, volatility, created_at, updated_at
            FROM market_stocks
            WHERE id = :id
        ');

        $stmt->execute([':id' => $stockId]);
        return $stmt->fetch() ?: null;
    }

    public function getStockPriceChange(int $stockId): array
    {
        $stock = $this->getStockById($stockId);
        if (!$stock) {
            return ['error' => 'Stock not found'];
        }

        $change = $stock['current_price'] - $stock['previous_price'];
        $changePercent = ($stock['previous_price'] > 0) ? ($change / $stock['previous_price']) * 100 : 0;

        return [
            'stock_id' => $stockId,
            'short_name' => $stock['short_name'],
            'current_price' => $stock['current_price'],
            'previous_price' => $stock['previous_price'],
            'change' => round($change, 2),
            'change_percent' => round($changePercent, 2),
            'trend' => $stock['trend'],
        ];
    }

    public function getStockHistory(int $stockId, int $limit = 50): array
    {
        $stmt = $this->db->prepare('
            SELECT price, game_tick, created_at
            FROM market_history
            WHERE stock_id = :stock_id
            ORDER BY created_at DESC
            LIMIT :limit
        ');

        $stmt->bindValue(':stock_id', $stockId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_reverse($stmt->fetchAll());
    }

    public function getNewsFeeds(int $limit = 10): array
    {
        $stmt = $this->db->prepare('
            SELECT id, title, content, category, is_pinned, created_at
            FROM news_feed
            ORDER BY is_pinned DESC, created_at DESC
            LIMIT :limit
        ');

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getActiveEvents(): array
    {
        $stmt = $this->db->prepare('
            SELECT id, title, description, effect_type, effect_value, created_at, expires_at
            FROM game_events
            WHERE is_active = TRUE AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY created_at DESC
        ');

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getGameState(): ?array
    {
        $stmt = $this->db->prepare('
            SELECT current_tick, current_day, last_tick_timestamp, next_tick_timestamp, is_tick_running
            FROM game_state
            WHERE id = 1
        ');

        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getTickCountdown(): int
    {
        $state = $this->getGameState();
        if (!$state) {
            return 0;
        }

        $nextTick = strtotime($state['next_tick_timestamp']);
        $now = time();
        $countdown = max(0, $nextTick - $now);

        return $countdown;
    }
}
