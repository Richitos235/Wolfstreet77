<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../app/Helpers/SessionHelper.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Services/Service.php';
require_once __DIR__ . '/../app/Services/MarketService.php';
require_once __DIR__ . '/../app/Helpers/TickHelper.php';

use App\Helpers\SessionHelper;
use App\Services\MarketService;
use App\Helpers\TickHelper;

SessionHelper::start();

if (!SessionHelper::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$marketService = new MarketService();
$tickHelper = new TickHelper();

$gameState = $tickHelper->getGameState();
$countdown = $tickHelper->getTickCountdown();

echo json_encode([
    'success' => true,
    'countdown' => $countdown,
    'formatted' => $tickHelper->getTickCountdownFormatted(),
    'day' => $gameState['current_day'],
    'tick' => $gameState['current_tick'],
    'next_tick' => $gameState['next_tick_timestamp'],
]);
