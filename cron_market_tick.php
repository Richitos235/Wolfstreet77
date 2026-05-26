<?php

declare(strict_types=1);

require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/MarketService.php';

use App\Services\MarketService;

$marketService = new MarketService();

echo "Updating Market Prices...\n";
$marketService->updateMarketPrices();
echo "Market Update Completed.\n";