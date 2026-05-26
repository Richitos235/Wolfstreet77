<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Services\MarketService;

class TickHelper
{
    private MarketService $marketService;

    public function __construct()
    {
        $this->marketService = new MarketService();
    }

    public function getGameState(): ?array
    {
        return $this->marketService->getGameState();
    }

    public function getTickCountdown(): int
    {
        return $this->marketService->getTickCountdown();
    }

    public function getTickCountdownFormatted(): string
    {
        $seconds = $this->getTickCountdown();
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getGameDay(): int
    {
        $state = $this->getGameState();
        return $state['current_day'] ?? 1;
    }

    public function getGameTick(): int
    {
        $state = $this->getGameState();
        return $state['current_tick'] ?? 0;
    }

    public function getNextTickTime(): string
    {
        $state = $this->getGameState();
        return $state['next_tick_timestamp'] ?? '';
    }
}
