<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/MarketService.php';
require_once __DIR__ . '/app/Helpers/TickHelper.php';
require_once __DIR__ . '/app/Game/GameTimeManager.php';

use App\Helpers\SessionHelper;
use App\Services\MarketService;
use App\Helpers\TickHelper;

SessionHelper::start();

$marketService = new MarketService();
$tickHelper = new TickHelper();
$stocks = $marketService->getAllStocks() ?: [];
$newsFeeds = $marketService->getNewsFeeds(3) ?: [];

$gameDayDisplay = $tickHelper->getGameDayDisplay();
$gameTime = $tickHelper->getGameTimeFormatted();

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wolfstreet77 | The Underworld Exchange</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/pages/landing.css" />
</head>
<body class="cyber-theme">
    <!-- Market Ticker -->
    <div class="market-ticker">
        <div class="ticker-content">
            <div class="ticker-item" style="border-right: 1px solid var(--border-glass); margin-right: 20px;">
                <span style="color: var(--accent-blue); font-weight: 800;"><?php echo $gameDayDisplay; ?></span>
                <span style="color: var(--text-bright); margin-left: 10px;"><?php echo $gameTime; ?></span>
            </div>
            <?php foreach (array_merge($stocks, $stocks) as $stock): ?>
                <div class="ticker-item">
                    <span class="ticker-symbol"><?php echo htmlspecialchars($stock['short_name']); ?></span>
                    <span class="ticker-price">$<?php echo number_format((float)$stock['current_price'], 2); ?></span>
                    <span class="ticker-trend <?php echo $stock['trend']; ?>">
                        <?php echo $stock['trend'] === 'rising' ? '▲' : ($stock['trend'] === 'falling' ? '▼' : '■'); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="nav-container">
            <div class="brand">
                <div class="brand-logo" style="background: var(--accent-blue); color: var(--bg-darker); font-weight: 900; padding: 4px 8px; border-radius: 4px;">W77</div>
                <span class="brand-name" style="font-family: 'Rajdhani'; font-size: 24px; font-weight: 700;">WOLFSTREET<span style="color: var(--accent-blue);">77</span></span>
            </div>
            <div class="nav-links">
                <?php if (SessionHelper::isAuthenticated()): ?>
                    <a href="home.php" class="btn btn-primary">Enter Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link" style="color: var(--text-muted); font-weight: 600; margin-right: 20px;">Login</a>
                    <a href="register.php" class="btn btn-primary">Join the Syndicate</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-v2">
        <div class="hero-bg">
            <div class="grid-overlay"></div>
            <div class="glow-orb"></div>
        </div>
        <div class="hero-content">
            <div class="hero-tag">EST. 2026 // GLOBAL ECONOMY SIMULATOR</div>
            <h1 class="hero-title">
                MASTER THE <span style="color: var(--accent-blue); text-shadow: 0 0 20px var(--accent-blue-glow);">MARKET</span><br>
                RULE THE <span style="color: var(--accent-red); text-shadow: 0 0 20px rgba(248, 81, 73, 0.5);">STREETS</span>
            </h1>
            <p class="hero-lead">
                A high-stakes multiplayer economy where every trade matters. Build factories, manipulate stocks, and climb the underworld hierarchy in a persistent 6-hour tick cycle.
            </p>
            <div class="hero-actions">
                <a href="register.php" class="btn btn-primary" style="padding: 16px 32px; font-size: 18px;">Start Your Empire</a>
                <a href="#market-data" class="btn btn-secondary" style="padding: 16px 32px; font-size: 18px;">View Live Data</a>
            </div>
        </div>
    </header>

    <!-- Market Data Section -->
    <section id="market-data" class="market-section">
        <div class="section-container">
            <div class="section-header">
                <h2 class="section-title">Live Exchange</h2>
                <p class="section-subtitle">Real-time asset valuation across the Wolfstreet network.</p>
            </div>
            
            <div class="market-grid">
                <?php foreach (array_slice($stocks, 0, 4) as $stock): ?>
                    <div class="market-card glass-panel">
                        <div class="card-header">
                            <span class="stock-ticker" style="color: var(--accent-blue); font-weight: 800;"><?php echo htmlspecialchars($stock['short_name']); ?></span>
                            <span class="stock-trend-icon <?php echo $stock['trend']; ?>" style="color: <?php echo $stock['trend'] === 'rising' ? 'var(--accent-green)' : 'var(--accent-red)'; ?>;">
                                <?php echo $stock['trend'] === 'rising' ? '↗' : '↘'; ?>
                            </span>
                        </div>
                        <div class="stock-name" style="color: var(--text-muted); font-size: 14px; margin-bottom: 8px;"><?php echo htmlspecialchars($stock['name']); ?></div>
                        <div class="stock-price" style="font-size: 32px; font-weight: 800; margin-bottom: 24px;">$<?php echo number_format((float)$stock['current_price'], 2); ?></div>
                        <div class="stock-stats" style="display: flex; justify-content: space-between; padding-top: 16px; border-top: 1px solid var(--border-glass);">
                            <div class="stat">
                                <span class="stat-label" style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Volatility</span>
                                <span class="stat-value" style="font-weight: 700;"><?php echo $stock['volatility']; ?>%</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label" style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Status</span>
                                <span class="stat-value" style="font-weight: 700; color: <?php echo $stock['trend'] === 'rising' ? 'var(--accent-green)' : 'var(--accent-red)'; ?>;"><?php echo strtoupper($stock['trend']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="main-footer" style="padding: 80px 0 40px; border-top: 1px solid var(--border-glass);">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="brand">
                    <div class="brand-logo" style="background: var(--accent-blue); color: var(--bg-darker); font-weight: 900; padding: 4px 8px; border-radius: 4px;">W77</div>
                    <span class="brand-name" style="font-family: 'Rajdhani'; font-size: 24px; font-weight: 700;">WOLFSTREET<span style="color: var(--accent-blue);">77</span></span>
                </div>
                <p style="color: var(--text-muted); margin-top: 16px;">The ultimate browser-based economy simulator.</p>
            </div>
            <div class="footer-bottom" style="display: flex; justify-content: space-between; align-items: center; padding-top: 32px; border-top: 1px solid var(--border-glass); color: var(--text-muted); font-size: 14px; margin-top: 40px;">
                <p>&copy; 2026 Wolfstreet77. All systems operational.</p>
                <div class="footer-links" style="display: flex; gap: 24px;">
                    <a href="#">Terms</a>
                    <a href="#">Privacy</a>
                    <a href="#">Discord</a>
                </div>
            </div>
        </div>
    </footer>

    <script type="module" src="/assets/js/core/app.js"></script>
</body>
</html>