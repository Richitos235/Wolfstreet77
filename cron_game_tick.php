<?php

declare(strict_types=1);

require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Game/GameTimeManager.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/MarketService.php';

use App\Game\GameTimeManager;
use App\Services\MarketService;

$timeManager = new GameTimeManager();
$marketService = new MarketService();

echo "Starting Game Tick Update...\n";

// 1. Update Game Time
$dayChanged = $timeManager->syncTime();
$currentTime = $timeManager->getFormattedTime();
$state = $timeManager->getGameState();

echo "Current Game Time: Day {$state['current_day']} - {$currentTime}\n";

// 2. If day changed, trigger economy updates
if ($dayChanged) {
    echo "New Day Detected! Triggering economy tick...\n";
    // Here we would call MarketService to update prices, 
    // trigger events, and process factory production.
    // For now, we log the event.
}

echo "Tick Update Completed.\n";