<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Helpers/SessionHelper.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Services/Service.php';
require_once __DIR__ . '/../app/Services/UserService.php';
require_once __DIR__ . '/../app/Services/MarketService.php';
require_once __DIR__ . '/../app/Helpers/TickHelper.php';

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
$portfolio = $userService->getUserPortfolio($userId);
$portfolioValue = $userService->getPortfolioValue($userId);
$totalAssets = $userService->getTotalAssets($userId);

$stocks = $marketService->getAllStocks();
$newsFeeds = $marketService->getNewsFeeds(5);
$activeEvents = $marketService->getActiveEvents();
$tickCountdown = $tickHelper->getTickCountdownFormatted();
$gameDay = $tickHelper->getGameDay();

$roleNames = ['trader' => 'Obchodník', 'gangster' => 'Gangster', 'pimp' => 'Pasák'];
$roleName = $roleNames[$user['role_type']] ?? $user['role_type'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/layout/grid.css" />
    <link rel="stylesheet" href="/assets/css/components/card.css" />
    <link rel="stylesheet" href="/assets/css/components/button.css" />
    <link rel="stylesheet" href="/assets/css/pages/dashboard.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/animations/transition.css" />
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar Menu -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Wolfstreet77</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="home.php" class="nav-item nav-item--active">📊 Dashboard</a>
                <a href="#" class="nav-item">🏪 Trh</a>
                <a href="#" class="nav-item">🏢 Budovy</a>
                <a href="#" class="nav-item">🤝 Syndikát</a>
                <a href="#" class="nav-item">⚔️ Armáda</a>
                <a href="#" class="nav-item">👑 Highscores</a>
                <a href="#" class="nav-item">👤 Profil</a>
                <a href="logout.php" class="nav-item nav-item--danger">🚪 Odhlášení</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <!-- Header -->
            <header class="dashboard-header">
                <h1>Dashboard</h1>
                <div class="header-info">
                    <span class="game-day">Den <?php echo $gameDay; ?></span>
                    <span class="tick-countdown" id="tickCountdown"><?php echo $tickCountdown; ?></span>
                </div>
            </header>

            <!-- Player Panel -->
            <section class="card player-panel">
                <h2 class="card-title">Můj Profil</h2>
                <div class="player-info">
                    <div class="player-field">
                        <span class="label">Jméno:</span>
                        <span class="value"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <div class="player-field">
                        <span class="label">Role:</span>
                        <span class="value role-badge"><?php echo $roleName; ?></span>
                    </div>
                    <div class="player-field">
                        <span class="label">Hotovost:</span>
                        <span class="value money"><?php echo number_format($user['money'], 2); ?> $</span>
                    </div>
                    <div class="player-field">
                        <span class="label">Banka:</span>
                        <span class="value"><?php echo number_format($user['bank_money'], 2); ?> $</span>
                    </div>
                </div>

                <div class="player-stats">
                    <div class="stat">
                        <span class="stat-name">Síla</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: <?php echo ($user['strength'] / 100) * 100; ?>%"></div>
                        </div>
                        <span class="stat-value"><?php echo $user['strength']; ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-name">Inteligence</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: <?php echo ($user['intelligence'] / 100) * 100; ?>%"></div>
                        </div>
                        <span class="stat-value"><?php echo $user['intelligence']; ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-name">Tolerance</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: <?php echo ($user['tolerance'] / 100) * 100; ?>%"></div>
                        </div>
                        <span class="stat-value"><?php echo $user['tolerance']; ?></span>
                    </div>
                </div>

                <div class="player-assets">
                    <div class="asset-item">
                        <span class="asset-label">Celkový majetek:</span>
                        <span class="asset-value"><?php echo number_format($totalAssets, 2); ?> $</span>
                    </div>
                    <div class="asset-item">
                        <span class="asset-label">Hodnota portfolia:</span>
                        <span class="asset-value"><?php echo number_format($portfolioValue, 2); ?> $</span>
                    </div>
                </div>
            </section>

            <!-- Main Grid -->
            <div class="dashboard-grid">
                <!-- Market Panel -->
                <section class="card market-panel">
                    <h2 class="card-title">📊 Trh akcií</h2>
                    <table class="market-table">
                        <thead>
                            <tr>
                                <th>Akcie</th>
                                <th>Cena</th>
                                <th>Změna</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stocks as $stock): ?>
                                <?php $change = $stock['current_price'] - $stock['previous_price']; ?>
                                <tr class="market-row">
                                    <td class="stock-name">
                                        <strong><?php echo htmlspecialchars($stock['short_name']); ?></strong>
                                    </td>
                                    <td class="stock-price">
                                        <?php echo number_format($stock['current_price'], 2); ?> $
                                    </td>
                                    <td class="stock-change <?php echo $change >= 0 ? 'positive' : 'negative'; ?>">
                                        <?php echo ($change >= 0 ? '+' : '') . number_format($change, 2); ?>
                                    </td>
                                    <td class="stock-trend">
                                        <?php
                                        $trendIcon = match($stock['trend']) {
                                            'rising' => '📈',
                                            'falling' => '📉',
                                            default => '➡️'
                                        };
                                        echo $trendIcon;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <!-- Portfolio Panel -->
                <section class="card portfolio-panel">
                    <h2 class="card-title">💼 Můj Portfolio</h2>
                    <?php if (empty($portfolio)): ?>
                        <p class="empty-state">Ještě nevlastníš žádné akcie</p>
                    <?php else: ?>
                        <div class="portfolio-list">
                            <?php foreach ($portfolio as $item): ?>
                                <div class="portfolio-item">
                                    <span class="portfolio-name"><?php echo htmlspecialchars($item['short_name']); ?></span>
                                    <span class="portfolio-qty">Počet: <?php echo $item['quantity']; ?></span>
                                    <span class="portfolio-value">
                                        <?php echo number_format($item['current_value'], 2); ?> $
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- News Panel -->
                <section class="card news-panel">
                    <h2 class="card-title">📰 Zprávy</h2>
                    <div class="news-list">
                        <?php foreach ($newsFeeds as $news): ?>
                            <div class="news-item">
                                <p class="news-title"><?php echo htmlspecialchars($news['title']); ?></p>
                                <p class="news-content"><?php echo htmlspecialchars(substr($news['content'], 0, 80)) . '...'; ?></p>
                                <span class="news-cat"><?php echo $news['category']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Events Panel -->
                <section class="card events-panel">
                    <h2 class="card-title">⚡ Aktivní Eventy</h2>
                    <?php if (empty($activeEvents)): ?>
                        <p class="empty-state">Žádné aktivní eventy</p>
                    <?php else: ?>
                        <div class="events-list">
                            <?php foreach ($activeEvents as $event): ?>
                                <div class="event-item event-<?php echo $event['effect_type']; ?>">
                                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>

    <!-- Notification Area -->
    <div id="notification-area"></div>

    <!-- Scripts -->
    <script type="module" src="/assets/js/core/app.js"></script>
    <script type="module" src="/assets/js/modules/tickdownModule.js"></script>
</body>
</html>
