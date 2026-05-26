<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wolfstreet77 | Staň se králem trhu</title>
    
    <!-- CSS linky z tvého repozitáře -->
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/pages/landing.css" />
    
    <!-- Google Fonts pro lepší herní vzhled -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="theme-dark">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container navbar-container">
            <div class="navbar-brand">
                <span class="logo-icon">🐺</span>
                <span class="logo-text">Wolfstreet77</span>
            </div>
            <div class="navbar-actions">
                <a href="login.php" class="btn btn--text">Přihlášení</a>
                <a href="register.php" class="btn btn--primary">Vytvořit účet</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="hero-title">Vládni ulicím <br><span class="text-gradient">Wolfstreet77</span></h1>
            <p class="hero-subtitle">MMORPG ekonomická simulace z prostředí vysokých financí i temného podsvětí.</p>
            
            <div class="hero-cta">
                <a href="register.php" class="btn btn--primary btn--large">Začít kariéru</a>
                <a href="#market" class="btn btn--secondary btn--large">Sledovat trh</a>
            </div>
        </div>
    </header>

    <!-- Market Preview Section -->
    <section id="market" class="market-preview">
        <div class="container">
            <div class="section-header">
                <h2>Živý kurz akcií</h2>
                <p>Aktualizováno každých 6 hodin</p>
            </div>
            
            <div class="table-responsive">
                <table class="market-preview-table">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Název společnosti</th>
                            <th>Aktuální cena</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stocks)): ?>
                            <tr><td colspan="4">Trh je momentálně uzavřen.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($stocks, 0, 5) as $stock): ?>
                                <tr>
                                    <td class="stock-symbol"><?php echo htmlspecialchars($stock['short_name']); ?></td>
                                    <td><?php echo htmlspecialchars($stock['name']); ?></td>
                                    <td class="stock-price"><?php echo number_format((float)$stock['current_price'], 2, ',', ' '); ?> $</td>
                                    <td class="trend-<?php echo $stock['trend']; ?>">
                                        <span class="trend-badge">
                                            <?php 
                                            echo match($stock['trend']) {
                                                'rising' => '📈 Vzestup',
                                                'falling' => '📉 Pokles',
                                                default => '➡️ Stabilní'
                                            };
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Herní Mechaniky</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Burza</h3>
                    <p>Spekuluj na ceny akcií a komodit. Využij insider informace dřív než ostatní.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏢</div>
                    <h3>Holding</h3>
                    <p>Stavěj továrny a legální firmy, které ti zajistí stabilní cashflow.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤜</div>
                    <h3>Podsvětí</h3>
                    <p>Založ syndikát, verbuj lidi a ovládni čtvrtě pomocí síly a strachu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="roles section-dark">
        <div class="container">
            <div class="section-header">
                <h2>Zvol si svůj osud</h2>
            </div>
            <div class="roles-grid">
                <article class="role-card role-trader">
                    <div class="role-image">🎩</div>
                    <h3>Obchodník</h3>
                    <p>Mozek operace. Manipuluje trhy a hromadí majetek skrze legální kličky.</p>
                </article>
                <article class="role-card role-gangster">
                    <div class="role-image">🔫</div>
                    <h3>Gangster</h3>
                    <p>Síla ulice. Peníze získává vydíráním, loupežemi a kontrolou území.</p>
                </article>
                <article class="role-card role-pimp">
                    <div class="role-image">🎭</div>
                    <h3>Pasák</h3>
                    <p>Mistr intrik. Ovládá noční život a profituje z lidských slabostí.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news-preview">
        <div class="container">
            <div class="section-header">
                <h2>Zprávy z Wolfstreet</h2>
            </div>
            <div class="news-preview-grid">
                <?php foreach ($newsFeeds as $news): ?>
                    <article class="news-preview-card">
                        <div class="news-meta">
                            <span class="news-category tag"><?php echo htmlspecialchars($news['category']); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p><?php echo htmlspecialchars(mb_strimwidth($news['content'], 0, 110, "...")); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta">
        <div class="container">
            <h2>Jsi připraven ovládnout trh?</h2>
            <a href="register.php" class="btn btn--primary btn--large">Vstoupit do hry</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date("Y"); ?> Wolfstreet77. Všechna práva vyhrazena.</p>
                <div class="footer-links">
                    <a href="#">Pravidla</a>
                    <a href="#">Podpora</a>
                    <a href="#">Discord</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>