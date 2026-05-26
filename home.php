<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/UserService.php';
require_once __DIR__ . '/app/Services/MarketService.php';
require_once __DIR__ . '/app/Helpers/TickHelper.php';
require_once __DIR__ . '/app/Game/GameTimeManager.php';

use App\Helpers\SessionHelper;
use App\Services\UserService;
use App\Services\MarketService;
use App\Helpers\TickHelper;

SessionHelper::start();

if (!SessionHelper::isAuthenticated()) {
    header('Location: login.php');
    exit;
}

$userId = SessionHelper::getCurrentUserId();
$userService = new UserService();
$marketService = new MarketService();
$tickHelper = new TickHelper();

$user = $userService->getUserById($userId);
if (!$user) {
    SessionHelper::destroy();
    header('Location: login.php?error=expired');
    exit;
}

$portfolio = $userService->getUserPortfolio($userId);
$money = (float)($user['money'] ?? 0);
$bankMoney = (float)($user['bank_money'] ?? 0);
$portfolioValue = (float)$userService->getPortfolioValue($userId);
$totalAssets = (float)$userService->getTotalAssets($userId);

$stocks = $marketService->getAllStocks() ?: [];
$newsFeeds = $marketService->getNewsFeeds(5) ?: [];

$tickCountdown = $tickHelper->getTickCountdownFormatted();
$gameDayDisplay = $tickHelper->getGameDayDisplay();
$gameTime = $tickHelper->getGameTimeFormatted();

$roleNames = [
    'trader' => 'Elite Trader',
    'gangster' => 'Street Enforcer',
    'pimp' => 'Underworld Boss'
];
$roleName = $roleNames[$user['role_type']] ?? $user['role_type'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wolfstreet77 | Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/layout.css" />
    <style>
        .stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }
        .stat-card {
            padding: var(--space-md);
        }
        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 800;
            font-family: 'Rajdhani';
            color: var(--accent-blue);
        }
        .stat-value.money { color: var(--accent-green); }
        
        .progress-stat {
            margin-bottom: var(--space-md);
        }
        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .progress-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: var(--accent-blue);
            box-shadow: 0 0 10px var(--accent-blue-glow);
        }
        
        .market-table {
            width: 100%;
            border-collapse: collapse;
        }
        .market-table th {
            text-align: left;
            padding: 12px;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
        }
        .market-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-glass);
        }
        .positive { color: var(--accent-green); }
        .negative { color: var(--accent-red); }
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
            <a href="home.php" class="nav-item active">📊 Dashboard</a>
            <a href="markets.php" class="nav-item">📈 Markets</a>
            <a href="factories.php" class="nav-item">🏭 Factories</a>
            <a href="#" class="nav-item">🤝 Syndicate</a>
            <a href="#" class="nav-item">🏦 Bank</a>
            <a href="logout.php" class="nav-item danger" style="margin-top: auto;">🚪 Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h1 style="font-size: 32px; margin-bottom: 4px;">Dashboard</h1>
                <p style="color: var(--text-muted); font-size: 14px;">Welcome back, <span style="color: var(--accent-blue); font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></span> (<?php echo $roleName; ?>)</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="glass-panel" style="padding: 8px 16px; text-align: center;">
                    <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);" id="gameDay"><?php echo $gameDayDisplay; ?></div>
                    <div style="font-weight: 700; font-family: 'Rajdhani'; font-size: 18px;" id="gameTime">ČAS: <?php echo $gameTime; ?></div>
                </div>
                <div class="glass-panel" style="padding: 8px 16px; text-align: center; border-color: var(--accent-blue);">
                    <div style="font-size: 10px; text-transform: uppercase; color: var(--accent-blue);">Next Tick</div>
                    <div style="font-weight: 700; font-family: 'Rajdhani'; color: var(--accent-blue);" id="tickCountdown"><?php echo $tickCountdown; ?></div>
                </div>
            </div>
        </header>

        <div class="stat-row">
            <div class="glass-panel stat-card">
                <div class="stat-label">Liquid Cash</div>
                <div class="stat-value money">$<?php echo number_format($money, 2); ?></div>
            </div>
            <div class="glass-panel stat-card">
                <div class="stat-label">Bank Balance</div>
                <div class="stat-value">$<?php echo number_format($bankMoney, 2); ?></div>
            </div>
            <div class="glass-panel stat-card">
                <div class="stat-label">Portfolio Value</div>
                <div class="stat-value">$<?php echo number_format($portfolioValue, 2); ?></div>
            </div>
            <div class="glass-panel stat-card">
                <div class="stat-label">Total Net Worth</div>
                <div class="stat-value money">$<?php echo number_format($totalAssets, 2); ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: var(--space-lg);">
            <div class="glass-panel">
                <h2 style="font-size: 20px; margin-bottom: var(--space-md); display: flex; align-items: center; gap: 8px;">
                    <span style="color: var(--accent-blue);">📈</span> Market Overview
                </h2>
                <table class="market-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Price</th>
                            <th>Change</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($stocks, 0, 6) as $stock): 
                            $diff = (float)$stock['current_price'] - (float)$stock['previous_price'];
                        ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--accent-blue);"><?php echo htmlspecialchars($stock['short_name']); ?></td>
                                <td style="font-family: 'Rajdhani'; font-weight: 600;">$<?php echo number_format((float)$stock['current_price'], 2); ?></td>
                                <td class="<?php echo $diff >= 0 ? 'positive' : 'negative'; ?>" style="font-weight: 600;">
                                    <?php echo ($diff >= 0 ? '+' : '') . number_format($diff, 2); ?>
                                </td>
                                <td><?php echo $stock['trend'] === 'rising' ? '↗' : '↘'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top: var(--space-md); text-align: center;">
                    <a href="markets.php" class="btn btn-secondary btn-block">View All Markets</a>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
                <div class="glass-panel">
                    <h2 style="font-size: 20px; margin-bottom: var(--space-md);">👤 Character Stats</h2>
                    <div class="progress-stat">
                        <div class="progress-header">
                            <span class="stat-label">Strength</span>
                            <span style="font-weight: 700;"><?php echo $user['strength']; ?></span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $user['strength']; ?>%"></div></div>
                    </div>
                    <div class="progress-stat">
                        <div class="progress-header">
                            <span class="stat-label">Intelligence</span>
                            <span style="font-weight: 700;"><?php echo $user['intelligence']; ?></span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $user['intelligence']; ?>%; background: var(--accent-purple);"></div></div>
                    </div>
                    <div class="progress-stat">
                        <div class="progress-header">
                            <span class="stat-label">Tolerance</span>
                            <span style="font-weight: 700;"><?php echo $user['tolerance']; ?></span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $user['tolerance']; ?>%; background: var(--accent-green);"></div></div>
                    </div>
                </div>

                <div class="glass-panel">
                    <h2 style="font-size: 20px; margin-bottom: var(--space-md);">📰 Global Intel</h2>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach (array_slice($newsFeeds, 0, 3) as $news): ?>
                            <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border-glass);">
                                <span class="badge badge-blue" style="margin-bottom: 4px; display: inline-block;"><?php echo htmlspecialchars($news['category']); ?></span>
                                <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;"><?php echo htmlspecialchars($news['title']); ?></div>
                                <div style="font-size: 12px; color: var(--text-muted); line-height: 1.4;"><?php echo htmlspecialchars(substr($news['content'], 0, 80)); ?>...</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script type="module" src="/assets/js/core/app.js"></script>
</body>
</html>