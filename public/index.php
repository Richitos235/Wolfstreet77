<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Helpers/SessionHelper.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Services/Service.php';
require_once __DIR__ . '/../app/Services/MarketService.php';

use App\Helpers\SessionHelper;
use App\Services\MarketService;

SessionHelper::start();

if (SessionHelper::isAuthenticated()) {
    header('Location: home.php');
    exit;
}

$marketService = new MarketService();
$stocks = $marketService->getAllStocks();
$newsFeeds = $marketService->getNewsFeeds(4);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wolfstreet77 - Browserová ekonomická hra</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/pages/landing.css" />
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-brand">
            <h1>🐺 Wolfstreet77</h1>
        </div>
        <div class="navbar-actions">
            <a href="login.php" class="btn btn--secondary">Přihlášení</a>
            <a href="register.php" class="btn btn--primary">Registrace</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Wolfstreet77</h1>
            <p class="hero-subtitle">Browser-based multiplayer ekonomická hra</p>
            <p class="hero-description">
                Staň se obchodníkem, gangsterem nebo pasákem. Obchoduj akciemi, vybuduj si impérium, 
                vytvoř syndikát a bojuj s ostatními hráči.
            </p>
            <div class="hero-cta">
                <a href="register.php" class="btn btn--primary btn--large">Začít hru</a>
                <a href="login.php" class="btn btn--secondary btn--large">Mám již účet</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Herní Systémy</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Obchodování akcií</h3>
                <p>Nakupuj a prodávej akcie s dynamickými cenami, které se mění každých 6 hodin</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏢</div>
                <h3>Budovy a produkce</h3>
                <p>Vyrábí produkty a generuj pasivní příjmy ze svých budov</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Syndikáty</h3>
                <p>Vytvoř nebo připoj se k syndikátu s ostatními hráči stejné role</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚔️</div>
                <h3>PvP systém</h3>
                <p>Bojuj s ostatními hráči o moc a vliv v Wolfstreet77</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👑</div>
                <h3>Highscores</h3>
                <p>Soutěž s ostatními hráči v globálních i syndikátních žebříčcích</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Game Events</h3>
                <p>Náhodné ekonomické eventy mění průběh hry a vytváří nové příležitosti</p>
            </div>
        </div>
    </section>

    <!-- Market Preview Section -->
    <section class="market-preview">
        <h2>Aktuální Trh</h2>
        <table class="market-preview-table">
            <thead>
                <tr>
                    <th>Společnost</th>
                    <th>Cena</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($stocks, 0, 5) as $stock): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stock['short_name']); ?> - <?php echo htmlspecialchars($stock['name']); ?></td>
                        <td><?php echo number_format($stock['current_price'], 2); ?> $</td>
                        <td class="trend-<?php echo $stock['trend']; ?>">
                            <?php 
                            echo match($stock['trend']) {
                                'rising' => '📈 Vzestup',
                                'falling' => '📉 Pokles',
                                default => '➡️ Stabilní'
                            };
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- News Preview Section -->
    <section class="news-preview">
        <h2>Poslední Zprávy</h2>
        <div class="news-preview-grid">
            <?php foreach ($newsFeeds as $news): ?>
                <div class="news-preview-card">
                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($news['content'], 0, 100)) . '...'; ?></p>
                    <span class="news-category"><?php echo htmlspecialchars($news['category']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="roles">
        <h2>Vyber si svou roli</h2>
        <div class="roles-grid">
            <div class="role-card role-card--trader">
                <h3>🎩 Obchodník</h3>
                <p>Zaměř se na nákup a prodej akcií. Vybuduj si bohaté impérium.</p>
            </div>
            <div class="role-card role-card--gangster">
                <h3>🔫 Gangster</h3>
                <p>Vezmi kontrolu nad černým trhem. Vytvoř si kriminální syndikát.</p>
            </div>
            <div class="role-card role-card--pimp">
                <h3>🎭 Pasák</h3>
                <p>Vybuduj si byznys se sexem. Maximalizuj zisky a vliv.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Wolfstreet77. Všechna práva vyhrazena.</p>
    </footer>
</body>
</html>
