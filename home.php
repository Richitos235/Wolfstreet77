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
$activeEvents = $marketService->getActiveEvents() ?: [];

$tickCountdown = $tickHelper->getTickCountdownFormatted();
$gameDay = $tickHelper->getGameDay();

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

    <title>Nextrade | Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/base/reset.css">
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css">

</head>

<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo-wrapper">

            <div class="logo-icon">N</div>

            <div>
                <div class="logo-title">NEXTRADE</div>
                <div class="logo-subtitle">MMO Trading Game</div>
            </div>

        </div>

        <nav class="sidebar-nav">

            <a href="home.php" class="nav-item active">
                <span>📊</span>
                Dashboard
            </a>

            <a href="markets.php" class="nav-item">
                <span>📈</span>
                Markets
            </a>

            <a href="factories.php" class="nav-item">
                <span>🏭</span>
                Factories
            </a>

            <a href="#" class="nav-item">
                <span>🛡️</span>
                Syndicate
            </a>

            <a href="#" class="nav-item">
                <span>⚔️</span>
                Wars
            </a>

            <a href="#" class="nav-item">
                <span>🏦</span>
                Bank
            </a>

            <a href="#" class="nav-item">
                <span>👑</span>
                Rankings
            </a>

            <a href="#" class="nav-item">
                <span>👤</span>
                Profile
            </a>

        </nav>

        <a href="logout.php" class="logout-btn">
            🚪 Logout
        </a>

    </aside>

    <!-- MAIN -->

    <main class="main-content">

        <!-- TOP STATUS -->

        <section class="hero-panel">

            <div class="hero-left">

                <div class="hero-label">
                    GLOBAL MARKET STATUS
                </div>

                <h1 class="hero-title green">
                    BULL MARKET ↗
                </h1>

                <div class="hero-stats">

                    <div class="hero-stat">
                        <span class="stat-label">Game Day</span>
                        <span class="stat-value"><?php echo $gameDay; ?></span>
                    </div>

                    <div class="hero-stat">
                        <span class="stat-label">Active Events</span>
                        <span class="stat-value green"><?php echo count($activeEvents); ?></span>
                    </div>

                    <div class="hero-stat">
                        <span class="stat-label">Tick</span>
                        <span class="stat-value blue" id="tickCountdown"><?php echo $tickCountdown; ?></span>
                    </div>

                </div>

            </div>

            <div class="hero-right">

                <div class="market-circle"></div>

            </div>

        </section>

        <!-- QUICK STATS -->

        <section class="stats-grid">

            <div class="stat-card">

                <div class="mini-label">TOTAL ASSETS</div>

                <div class="big-number">
                    $<?php echo number_format($totalAssets, 0); ?>
                </div>

            </div>

            <div class="stat-card">

                <div class="mini-label">PORTFOLIO VALUE</div>

                <div class="big-number blue">
                    $<?php echo number_format($portfolioValue, 0); ?>
                </div>

            </div>

            <div class="stat-card">

                <div class="mini-label">CASH</div>

                <div class="big-number green">
                    $<?php echo number_format($money, 0); ?>
                </div>

            </div>

            <div class="stat-card">

                <div class="mini-label">BANK</div>

                <div class="big-number">
                    $<?php echo number_format($bankMoney, 0); ?>
                </div>

            </div>

        </section>

        <!-- GRID -->

        <div class="content-grid">

            <!-- LEFT -->

            <div class="left-column">

                <!-- EVENTS -->

                <section class="card">

                    <div class="card-header">

                        <h2>⚡ LIVE EVENTS</h2>

                        <span class="card-badge">
                            REALTIME
                        </span>

                    </div>

                    <div class="events-feed">

                        <?php if(empty($newsFeeds)): ?>

                            <div class="empty-box">
                                No global events found.
                            </div>

                        <?php else: ?>

                            <?php foreach($newsFeeds as $news): ?>

                                <div class="event-card">

                                    <div class="event-top">

                                        <span class="event-category">
                                            <?php echo strtoupper($news['category']); ?>
                                        </span>

                                        <span class="event-time">
                                            LIVE
                                        </span>

                                    </div>

                                    <div class="event-title">
                                        <?php echo htmlspecialchars($news['title']); ?>
                                    </div>

                                    <div class="event-description">
                                        <?php echo htmlspecialchars($news['content']); ?>
                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                </section>

                <!-- MARKET -->

                <section class="card">

                    <div class="card-header">

                        <h2>📈 TOP MARKET MOVERS</h2>

                    </div>

                    <table class="market-table">

                        <thead>

                        <tr>
                            <th>ASSET</th>
                            <th>PRICE</th>
                            <th>CHANGE</th>
                            <th></th>
                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach(array_slice($stocks,0,7) as $stock):

                            $cur = (float)$stock['current_price'];
                            $prev = (float)$stock['previous_price'];

                            $diff = $cur - $prev;

                            $pct = ($prev > 0)
                                ? ($diff / $prev) * 100
                                : 0;

                        ?>

                            <tr>

                                <td>

                                    <div class="asset-name">
                                        <?php echo htmlspecialchars($stock['short_name']); ?>
                                    </div>

                                    <div class="asset-sub">
                                        <?php echo htmlspecialchars($stock['name']); ?>
                                    </div>

                                </td>

                                <td class="bold">
                                    $<?php echo number_format($cur,2); ?>
                                </td>

                                <td class="<?php echo $diff >= 0 ? 'green' : 'red'; ?> bold">

                                    <?php echo ($diff >= 0 ? '+' : '') . number_format($pct,2); ?>%

                                </td>

                                <td>

                                    <a href="markets.php?id=<?php echo $stock['id']; ?>" class="trade-btn">
                                        Trade
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </section>

            </div>

            <!-- RIGHT -->

            <aside class="right-column">

                <!-- PROFILE -->

                <section class="profile-card">

                    <div class="profile-top">

                        <div class="avatar">

                            <?php echo strtoupper(substr($user['username'],0,1)); ?>

                        </div>

                        <div>

                            <div class="profile-name">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </div>

                            <div class="profile-role">
                                <?php echo $roleName; ?>
                            </div>

                        </div>

                    </div>

                    <div class="profile-line"></div>

                    <div class="finance-list">

                        <div class="finance-item">

                            <span>Cash</span>

                            <strong class="green">
                                $<?php echo number_format($money,2); ?>
                            </strong>

                        </div>

                        <div class="finance-item">

                            <span>Bank</span>

                            <strong>
                                $<?php echo number_format($bankMoney,2); ?>
                            </strong>

                        </div>

                        <div class="finance-item">

                            <span>Portfolio</span>

                            <strong class="blue">
                                $<?php echo number_format($portfolioValue,2); ?>
                            </strong>

                        </div>

                    </div>

                </section>

                <!-- WATCHLIST -->

                <section class="card">

                    <div class="card-header">

                        <h2>👁 WATCHLIST</h2>

                    </div>

                    <div class="watchlist">

                        <?php foreach(array_slice($stocks,0,6) as $stock):

                            $diff = (float)$stock['current_price'] - (float)$stock['previous_price'];

                        ?>

                            <div class="watch-item">

                                <div>

                                    <div class="watch-symbol">
                                        <?php echo htmlspecialchars($stock['short_name']); ?>
                                    </div>

                                    <div class="watch-price">
                                        $<?php echo number_format((float)$stock['current_price'],2); ?>
                                    </div>

                                </div>

                                <div class="<?php echo $diff >= 0 ? 'green' : 'red'; ?> bold">

                                    <?php echo ($diff >= 0 ? '+' : '') . number_format($diff,2); ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </section>

                <!-- PORTFOLIO -->

                <section class="card">

                    <div class="card-header">

                        <h2>💼 PORTFOLIO</h2>

                    </div>

                    <?php if(empty($portfolio)): ?>

                        <div class="empty-box">
                            No owned stocks.
                        </div>

                    <?php else: ?>

                        <div class="portfolio-list">

                            <?php foreach($portfolio as $item): ?>

                                <div class="portfolio-item">

                                    <div>

                                        <div class="watch-symbol">
                                            <?php echo htmlspecialchars($item['short_name']); ?>
                                        </div>

                                        <div class="watch-price">
                                            Quantity: <?php echo $item['quantity']; ?>
                                        </div>

                                    </div>

                                    <div class="bold blue">

                                        $<?php echo number_format($item['current_value'],2); ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </section>

            </aside>

        </div>

    </main>

</div>

<script type="module" src="/assets/js/core/app.js"></script>
<script type="module" src="/assets/js/modules/tickdownModule.js"></script>

</body>
</html>