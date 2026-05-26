<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/UserService.php';
require_once __DIR__ . '/app/Services/MarketService.php';
require_once __DIR__ . '/app/Helpers/TickHelper.php';
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
$money = (float)($user['money'] ?? 0);

$factoryDefinitions = [
    1 => ['name' => 'Varna Amfetaminu', 'price' => 50000, 'upkeep' => 4000, 'prod' => '500g Speed', 'risk' => 'Vysoký', 'class' => 'illegal'],
    2 => ['name' => 'Cannabis Farma', 'price' => 22000, 'upkeep' => 1200, 'prod' => '300g Weed', 'risk' => 'Nízký', 'class' => 'illegal'],
    3 => ['name' => 'Crypto Mining Rig', 'price' => 120000, 'upkeep' => 12000, 'prod' => '0.08 BTC', 'risk' => 'Nulový', 'class' => 'legal'],
    4 => ['name' => 'Chemická Laboratoř', 'price' => 250000, 'upkeep' => 18000, 'prod' => 'Chemicals', 'risk' => 'Střední', 'class' => 'legal'],
    5 => ['name' => 'Ropný Vrt', 'price' => 500000, 'upkeep' => 35000, 'prod' => '2000 Bbl Oil', 'risk' => 'Nulový', 'class' => 'legal'],
    6 => ['name' => 'AI Chip Factory', 'price' => 800000, 'upkeep' => 55000, 'prod' => '120 Chips', 'risk' => 'Nulový', 'class' => 'legal'],
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factories | Wolfstreet77</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/layout.css" />
    <style>
        .factory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: var(--space-md);
        }
        .factory-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .factory-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--space-md);
        }
        .factory-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent-blue);
        }
        .factory-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: var(--space-md);
            flex-grow: 1;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }
        .stat-label { color: var(--text-muted); }
        .stat-val { font-weight: 600; }
        .stat-val.green { color: var(--accent-green); }
        .stat-val.red { color: var(--accent-red); }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div style="background: var(--accent-blue); color: var(--bg-darker); font-weight: 900; padding: 4px 8px; border-radius: 4px;">W77</div>
            <span style="font-family: 'Rajdhani'; font-size: 20px; font-weight: 700;">WOLFSTREET<span style="color: var(--accent-blue);">77</span></span>
        </div>
        <nav class="sidebar-nav">
            <a href="home.php" class="nav-item">📊 Dashboard</a>
            <a href="markets.php" class="nav-item">📈 Markets</a>
            <a href="factories.php" class="nav-item active">🏭 Factories</a>
            <a href="#" class="nav-item">🤝 Syndicate</a>
            <a href="#" class="nav-item">🏦 Bank</a>
            <a href="logout.php" class="nav-item danger" style="margin-top: auto;">🚪 Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h1 style="font-size: 32px; margin-bottom: 4px;">Industrial Sector</h1>
                <p style="color: var(--text-muted); font-size: 14px;">Build your production empire and dominate the supply chain.</p>
            </div>
            <div class="glass-panel" style="padding: 8px 16px; text-align: center;">
                <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Available Cash</div>
                <div style="font-weight: 700; font-family: 'Rajdhani'; color: var(--accent-green);">$<?php echo number_format($money, 2); ?></div>
            </div>
        </header>

        <div class="factory-grid">
            <?php foreach ($factoryDefinitions as $id => $f): ?>
                <div class="glass-panel factory-card">
                    <div class="factory-header">
                        <h3 class="factory-name"><?php echo $f['name']; ?></h3>
                        <span class="badge <?php echo $f['class'] === 'legal' ? 'badge-green' : 'badge-red'; ?>" style="background: <?php echo $f['class'] === 'legal' ? 'rgba(63, 185, 80, 0.1)' : 'rgba(248, 81, 73, 0.1)'; ?>; color: <?php echo $f['class'] === 'legal' ? 'var(--accent-green)' : 'var(--accent-red)'; ?>;">
                            <?php echo strtoupper($f['class']); ?>
                        </span>
                    </div>

                    <div class="factory-stats">
                        <div class="stat-item">
                            <span class="stat-label">Purchase Price</span>
                            <span class="stat-val">$<?php echo number_format($f['price'], 0); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Daily Production</span>
                            <span class="stat-val green"><?php echo $f['prod']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Maintenance</span>
                            <span class="stat-val red">-$<?php echo number_format($f['upkeep'], 0); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Risk Level</span>
                            <span class="stat-val" style="color: <?php echo $f['risk'] === 'Vysoký' ? 'var(--accent-red)' : 'var(--accent-green)'; ?>;"><?php echo $f['risk']; ?></span>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" <?php echo ($money < $f['price']) ? 'disabled' : ''; ?>>
                        <?php echo ($money < $f['price']) ? 'Insufficient Funds' : 'Purchase Facility'; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>