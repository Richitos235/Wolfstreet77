<?php

declare(strict_types=1);

namespace App\Helpers;

require_once __DIR__ . '/../Game/GameTimeManager.php';

use App\Game\GameTimeManager;

class TickHelper
{
    private GameTimeManager $timeManager;

    public function __construct()
    {
        $this->timeManager = new GameTimeManager();
    }

    public function getGameState(): ?array
    {
        return $this->timeManager->getGameState();
    }

    public function getGameTimeFormatted(): string
    {
        $data = $this->timeManager->syncTime();
        return $data['time'];
    }

    public function getGameDayDisplay(): string
    {
        $data = $this->timeManager->syncTime();
        return "DEN {$data['day']} / {$data['max_days']}";
    }

    public function getTickCountdown(): int
    {
        $state = $this->getGameState();
        if (!$state) return 0;

        $nextTick = strtotime($state['next_tick_timestamp']);
        return max(0, $nextTick - time());
    }

    public function getTickCountdownFormatted(): string
    {
        $seconds = $this->getTickCountdown();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}