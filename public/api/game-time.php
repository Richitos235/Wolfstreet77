<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../app/Config/Database.php';
require_once __DIR__ . '/../../app/Game/GameTimeManager.php';

use App\Game\GameTimeManager;

try {
    $timeManager = new GameTimeManager();
    $data = $timeManager->syncTime();
    
    echo json_encode([
        'success' => true,
        'time' => $data['time'],
        'day' => $data['day'],
        'max_days' => $data['max_days']
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}