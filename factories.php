<?php
declare(strict_types=1);

// Odstraňujeme "/../", protože "app" je ve stejné složce jako tento soubor
require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/UserService.php';
require_once __DIR__ . '/app/Services/MarketService.php';
require_once __DIR__ . '/app/Helpers/TickHelper.php';
// Pokud máš i FactoryService, přidej ho:
require_once __DIR__ . '/app/Services/FactoryService.php';

use App\Helpers\SessionHelper;
use App\Services\UserService;
use App\Services\FactoryService;
use App\Helpers\TickHelper;

SessionHelper::start();

if (!SessionHelper::isAuthenticated()) {
    header('Location: login.php');
    exit;
}

$userId = SessionHelper::getCurrentUserId();
$userService = new UserService();
$factoryService = new FactoryService();
$tickHelper = new TickHelper();

$user = $userService->getUserById($userId);
$userFactories = $factoryService->getUserFactories($userId); // Vrací seznam budov, které hráč má
$availableFactories = $factoryService->getAllAvailableFactories(); // Vrací definice všech budov

// Definice budov (pokud nejsou v DB, můžeme je mít v poli pro prototyp)
$factoryDefinitions = [
    1 => ['name' => 'Varna Amfetaminu', 'price' => 50000, 'upkeep' => 4000, 'prod' => '500g Speed', 'risk' => 'Vysoký', 'class' => 'illegal'],
    2 => ['name' => 'Cannabis Farma', 'price' => 22000, 'upkeep' => 1200, 'prod' => '300g Weed', 'risk' => 'Nízký', 'class' => 'illegal'],
    3 => ['name' => 'Crypto Mining Rig', 'price' => 120000, 'upkeep' => 12000, 'prod' => '0.08 BTC', 'risk' => 'Nulový', 'class' => 'legal'],
    4 => ['name' => 'Chemická Laboratoř', 'price' => 250000, 'upkeep' => 18000, 'prod' => 'Chemicals', 'risk' => 'Střední', 'class' => 'legal'],
    5 => ['name' => 'Ropný Vrt', 'price' => 500000, 'upkeep' => 35000, 'prod' => '2000 Bbl Oil', 'risk' => 'Nulový', 'class' => 'legal'],
    6 => ['name' => 'AI Chip Factory', 'price' => 800000, 'upkeep' => 55000, 'prod' => '120 Chips', 'risk' => 'Nulový', 'class' => 'legal'],
    7 => ['name' => 'Zbrojní Továrna', 'price' => 1200000, 'upkeep' => 95000, 'prod' => '50 Weapons', 'risk' => 'Vysoký', 'class' => 'illegal'],
    8 => ['name' => 'Padělatelská Dílna', 'price' => 150000, 'upkeep' => 25000, 'prod' => '80 000 $ (Dirty)', 'risk' => 'Extrémní', 'class' => 'illegal'],
    9 => ['name' => 'Soukromá Vojenská Akademie', 'price' => 2500000, 'upkeep' => 150000, 'prod' => 'Mercenaries', 'risk' => 'Střední', 'class' => 'legal'],
    10 => ['name' => 'Diamantový Důl', 'price' => 5000000, 'upkeep' => 300000, 'prod' => '50 Carats', 'risk' => 'Nízký', 'class' => 'legal'],
];

$money = (float)($user['money'] ?? 0);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Industrial Sector | Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css">
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css">
    <style>
        .factory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .factory-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }
        .factory-card:hover { border-color: var(--green); }
        .factory-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .factory-type { font-size: 12px; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; }
        .illegal { background: rgba(255, 71, 87, 0.2); color: #ff4757; }
        .legal { background: rgba(46, 213, 115, 0.2); color: #2ed573; }
        
        .stat-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .stat-label { color: var(--muted); }
        
        .factory-actions { margin-top: auto; padding-top: 20px; }
        .btn-buy { width: 100%; padding: 12px; background: var(--green); color: #000; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-collect { width: 100%; padding: 12px; background: #5352ed; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .progress-container { background: #1a252f; height: 8px; border-radius: 4px; margin: 15px 0; overflow: hidden; }
        .progress-bar { background: var(--green); height: 100%; width: 65%; /* Dynamicky podle času */ }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Wolfstreet77</h2>
        <nav class="sidebar-nav">
            <a href="home.php" class="nav-item">📊 Dashboard</a>
            <a href="markets.php" class="nav-item">📈 Markets</a>
            <a href="factories.php" class="nav-item nav-item--active">🏭 Factories</a>
            <a href="#" class="nav-item">🤝 Syndicate</a>
            <a href="#" class="nav-item">💰 Bank</a>
            <a href="logout.php" class="nav-item nav-item--danger">🚪 Logout</a>
        </nav>
    </aside>

    <main class="dashboard-content">
        <header class="dashboard-header">
            <div>
                <h1>Industrial Sector</h1>
                <p style="color: var(--muted);">Build your production empire and dominate the supply chain.</p>
            </div>
            <div class="header-info">
                <span class="game-day">Cash: <strong><?php echo number_format($money, 0); ?> $</strong></span>
            </div>
        </header>

        <div class="factory-grid">
            <?php foreach ($factoryDefinitions as $id => $f): ?>
                <div class="factory-card">
                    <div class="factory-header">
                        <h3><?php echo $f['name']; ?></h3>
                        <span class="factory-type <?php echo $f['class']; ?>"><?php echo $f['class']; ?></span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">Cena:</span>
                        <span><?php echo number_format($f['price'], 0); ?> $</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Produkce:</span>
                        <span class="green"><?php echo $f['prod']; ?> / den</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Údržba:</span>
                        <span class="negative">-<?php echo number_format($f['upkeep'], 0); ?> $</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Riziko:</span>
                        <span style="color: <?php echo $f['risk'] == 'Vysoký' ? '#ff4757' : '#2ed573'; ?>"><?php echo $f['risk']; ?></span>
                    </div>

                    <?php 
                    // Simulace: Má hráč budovu?
                    $owned = false; 
                    ?>

                    <div class="factory-actions">
                        <?php if($owned): ?>
                            <div class="progress-container">
                                <div class="progress-bar"></div>
                            </div>
                            <button class="btn-collect">VYZVEDNOUT PRODUKCI</button>
                        <?php else: ?>
                            <button class="btn-buy" <?php echo ($money < $f['price']) ? 'disabled' : ''; ?>>
                                <?php echo ($money < $f['price']) ? 'NEDOSTATEK FINANCÍ' : 'KOUPIT BUDOVU'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

</body>
</html>