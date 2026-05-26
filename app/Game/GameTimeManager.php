<?php

declare(strict_types=1);

namespace App\Game;

use App\Config\Database;
use PDO;

class GameTimeManager
{
    private PDO $db;
    private const MINUTES_PER_DAY = 1440;
    // 1 game day (1440 mins) = 3 real hours (180 mins)
    // 1440 / 180 = 8 game minutes per real minute
    private const GAME_MINS_PER_REAL_MIN = 8;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Synchronizes game time based on real-world elapsed time.
     */
    public function syncTime(): array
    {
        $state = $this->getGameState();
        if (!$state) {
            return ['day' => 1, 'time' => '00:00', 'max_days' => 160];
        }

        $lastUpdate = strtotime($state['last_tick_timestamp'] ?? date('Y-m-d H:i:s'));
        $now = time();
        $elapsedSeconds = $now - $lastUpdate;

        if ($elapsedSeconds > 0) {
            // Calculate passed game minutes: (seconds / 60) * 8
            $gameMinutesPassed = (int)floor(($elapsedSeconds / 60) * self::GAME_MINS_PER_REAL_MIN);
            
            if ($gameMinutesPassed > 0) {
                $newMinutes = $state['game_minutes'] + $gameMinutesPassed;
                $newDay = $state['current_day'];

                while ($newMinutes >= self::MINUTES_PER_DAY) {
                    $newMinutes -= self::MINUTES_PER_DAY;
                    $newDay++;
                }

                // Cap at max days
                if ($newDay > $state['max_game_days']) {
                    $newDay = $state['max_game_days'];
                    $newMinutes = self::MINUTES_PER_DAY - 1;
                }

                $this->updateState($newDay, $newMinutes, $now);
                
                // Refresh state for return
                $state['current_day'] = $newDay;
                $state['game_minutes'] = $newMinutes;
            }
        }

        return [
            'day' => (int)$state['current_day'],
            'time' => $this->formatMinutes((int)$state['game_minutes']),
            'max_days' => (int)$state['max_game_days']
        ];
    }

    public function getGameState(): ?array
    {
        $stmt = $this->db->query('SELECT * FROM game_state LIMIT 1');
        return $stmt->fetch() ?: null;
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    private function updateState(int $day, int $minutes, int $timestamp): void
    {
        $tsValue = date('Y-m-d H:i:s', $timestamp);
        
        $stmt = $this->db->prepare('
            UPDATE game_state 
            SET current_day = :day, 
                game_minutes = :minutes, 
                last_tick_timestamp = :ts,
                next_tick_timestamp = DATE_ADD(:ts_next, INTERVAL 3 HOUR)
            WHERE id = 1
        ');

        $stmt->execute([
            ':day' => $day,
            ':minutes' => $minutes,
            ':ts' => $tsValue,
            ':ts_next' => $tsValue
        ]);
    }
}