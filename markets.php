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

// OPRAVA: Ošetření, aby funkce number_format nedostala null nebo prázdný string
$money = (float)($user['money'] ?? 0);
$bankMoney = (float)($user['bank_money'] ?? 0);

$portfolioValue = (float)$userService->getPortfolioValue($userId);
$totalAssets = (float)$userService->getTotalAssets($userId);

$stocks = $marketService->getAllStocks() ?? [];
$tickCountdown = $tickHelper->getTickCountdownFormatted();
$gameDay = $tickHelper->getGameDay();

$roleNames = ['trader' => 'Trader', 'gangster' => 'Gangster', 'pimp' => 'Boss'];
$roleName = $roleNames[$user['role_type']] ?? 'Citizen';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markets | Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css">
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css">
    <style>
        /* Rychlá oprava pro tlačítka nákupu v tabulce */
        .btn-action {
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            transition: 0.2s;
            display: inline-block;
        }
        .btn-buy { background: var(--green); color: #071018; }
        .btn-sell { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-action:hover { transform: scale(1.05); filter: brightness(1.2); }
        
        .stock-symbol-box {
            display: flex;
            flex-direction: column;
        }
        .stock-full-name {
            font-size: 11px;
            color: var(--muted);
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Wolfstreet77</h2>
        <nav class="sidebar-nav">
            <a href="home.php" class="nav-item">📊 Dashboard</a>
            <a href="markets.php" class="nav-item nav-item--active">📈 Markets</a>
            <a href="factories.php" class="nav-item">🏭 Factories</a>
            <a href="#" class="nav-item">🤝 Syndicate</a>
            <a href="#" class="nav-item">💰 Bank</a>
            <a href="#" class="nav-item">⚔️ Wars</a>
            <a href="#" class="nav-item">📰 Events</a>
            <a href="#" class="nav-item">👤 Profile</a>
            <a href="logout.php" class="nav-item nav-item--danger">🚪 Logout</a>
        </nav>
    </aside>

    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Global Markets</h1>
            <div class="header-info">
                <span class="game-day">Day <?php echo $gameDay; ?></span>
                <span class="tick-countdown"><?php echo $tickCountdown; ?></span>
            </div>
        </header>

        <div class="dashboard-main">
            <div class="center-panel">
                <section class="card">
                    <h2 class="card-title">Live Stock Exchange</h2>
                    <table class="market-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Price</th>
                                <th>Change</th>
                                <th>Trend</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stocks as $stock): 
                                $curPrice = (float)$stock['current_price'];
                                $prevPrice = (float)$stock['previous_price'];
                                $change = $curPrice - $prevPrice;
                            ?>
                            <tr>
                                <td>
                                    <div class="stock-symbol-box">
                                        <strong><?php echo htmlspecialchars($stock['short_name']); ?></strong>
                                        <span class="stock-full-name"><?php echo htmlspecialchars($stock['name']); ?></span>
                                    </div>
                                </td>
                                <td><strong><?php echo number_format($curPrice, 2); ?> $</strong></td>
                                <td class="<?php echo $change >= 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo ($change >= 0 ? '+' : '') . number_format($change, 2); ?>
                                </td>
                                <td><?php echo $stock['trend'] === 'rising' ? '📈' : '📉'; ?></td>
                                <td>
                                    <a href="#" class="btn-action btn-buy">BUY</a>
                                    <a href="#" class="btn-action btn-sell">SELL</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </div>

            <aside class="right-panel">
                <section class="card">
                    <div class="profile-top">
                        <div class="avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
                        <div>
                            <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="profile-role"><?php echo $roleName; ?></div>
                        </div>
                    </div>
                    <div class="finance-list">
                        <div class="finance-item">
                            <span>Liquid Cash</span>
                            <strong class="green"><?php echo number_format($money, 2); ?> $</strong>
                        </div>
                        <div class="finance-item">
                            <span>Market Value</span>
                            <strong><?php echo number_format($portfolioValue, 2); ?> $</strong>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <h2 class="card-title">💡 Trading Tip</h2>
                    <p style="color: var(--muted); font-size: 14px;">
                        Markets refresh every 6 hours. Watch for the 'rising' trend to maximize your profit.
                    </p>
                </section>
            </aside>
        </div>
    </main>
</div>

<div class="ticker">
    <div class="ticker-track">
        <?php foreach($stocks as $stock): 
            $c = (float)$stock['current_price'] - (float)$stock['previous_price']; ?>
            <span class="ticker-item">
                <?php echo $stock['short_name']; ?> 
                <span class="<?php echo $c >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo number_format((float)$stock['current_price'], 2); ?>
                </span>
            </span>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>