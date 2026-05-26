<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/UserService.php';
require_once __DIR__ . '/app/Services/MarketService.php';
require_once __DIR__ . '/app/Helpers/TickHelper.php';

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

$message = '';
$error = '';

// Handle Trading Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $stockId = (int)($_POST['stock_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($action === 'buy') {
        $result = $marketService->buyStock($userId, $stockId, $quantity);
        if ($result['success']) $message = "Úspěšně jste nakoupili {$quantity} akcií.";
        else $error = $result['error'];
    } elseif ($action === 'sell') {
        $result = $marketService->sellStock($userId, $stockId, $quantity);
        if ($result['success']) $message = "Úspěšně jste prodali {$quantity} akcií.";
        else $error = $result['error'];
    }
}

$user = $userService->getUserById($userId);
$money = (float)($user['money'] ?? 0);
$stocks = $marketService->getAllStocks() ?? [];
$portfolio = $userService->getUserPortfolio($userId);

// Map portfolio for easy lookup
$ownedQuantities = [];
foreach ($portfolio as $p) {
    $ownedQuantities[$p['stock_id']] = $p['quantity'];
}

$tickCountdown = $tickHelper->getTickCountdownFormatted();
$gameDayDisplay = $tickHelper->getGameDayDisplay();
$gameTime = $tickHelper->getGameTimeFormatted();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markets | Wolfstreet77</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/layout.css" />
    <style>
        .market-table {
            width: 100%;
            border-collapse: collapse;
        }
        .market-table th {
            text-align: left;
            padding: 16px;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
        }
        .market-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border-glass);
        }
        .positive { color: var(--accent-green); }
        .negative { color: var(--accent-red); }
        
        .stock-info {
            display: flex;
            flex-direction: column;
        }
        .stock-symbol {
            font-weight: 800;
            color: var(--accent-blue);
            font-family: 'Rajdhani';
            font-size: 18px;
        }
        .stock-full {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        
        .trade-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .trade-input {
            width: 80px;
            padding: 6px 10px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-glass);
            border-radius: 4px;
            color: white;
            font-size: 13px;
        }
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
            <a href="markets.php" class="nav-item active">📈 Markets</a>
            <a href="factories.php" class="nav-item">🏭 Factories</a>
            <a href="#" class="nav-item">🤝 Syndicate</a>
            <a href="#" class="nav-item">🏦 Bank</a>
            <a href="logout.php" class="nav-item danger" style="margin-top: auto;">🚪 Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h1 style="font-size: 32px; margin-bottom: 4px;">Global Markets</h1>
                <p style="color: var(--text-muted); font-size: 14px;">Trade assets and manipulate the economy.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="glass-panel" style="padding: 8px 16px; text-align: center;">
                    <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Available Cash</div>
                    <div style="font-weight: 700; font-family: 'Rajdhani'; color: var(--accent-green);">$<?php echo number_format($money, 2); ?></div>
                </div>
                <div class="glass-panel" style="padding: 8px 16px; text-align: center; border-color: var(--accent-blue);">
                    <div style="font-size: 10px; text-transform: uppercase; color: var(--accent-blue);">Next Tick</div>
                    <div style="font-weight: 700; font-family: 'Rajdhani'; color: var(--accent-blue);"><?php echo $tickCountdown; ?></div>
                </div>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="glass-panel">
            <table class="market-table">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Price</th>
                        <th>Change</th>
                        <th>Owned</th>
                        <th>Supply</th>
                        <th style="text-align: right;">Trade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $stock): 
                        $curPrice = (float)$stock['current_price'];
                        $prevPrice = (float)$stock['previous_price'];
                        $change = $curPrice - $prevPrice;
                        $owned = $ownedQuantities[$stock['id']] ?? 0;
                    ?>
                    <tr>
                        <td>
                            <div class="stock-info">
                                <span class="stock-symbol"><?php echo htmlspecialchars($stock['short_name']); ?></span>
                                <span class="stock-full"><?php echo htmlspecialchars($stock['name']); ?></span>
                            </div>
                        </td>
                        <td style="font-family: 'Rajdhani'; font-weight: 700; font-size: 18px;">$<?php echo number_format($curPrice, 2); ?></td>
                        <td class="<?php echo $change >= 0 ? 'positive' : 'negative'; ?>" style="font-weight: 600;">
                            <?php echo ($change >= 0 ? '+' : '') . number_format($change, 2); ?>
                        </td>
                        <td style="font-weight: 600; color: var(--text-bright);"><?php echo number_format($owned); ?></td>
                        <td style="font-size: 12px; color: var(--text-muted);"><?php echo number_format($stock['available_supply']); ?></td>
                        <td style="text-align: right;">
                            <form method="POST" class="trade-form">
                                <input type="hidden" name="stock_id" value="<?php echo $stock['id']; ?>">
                                <input type="number" name="quantity" class="trade-input" placeholder="Qty" min="1" required>
                                <button type="submit" name="action" value="buy" class="btn btn-primary" style="padding: 6px 12px; font-size: 11px;">Buy</button>
                                <button type="submit" name="action" value="sell" class="btn btn-secondary" style="padding: 6px 12px; font-size: 11px;" <?php echo $owned <= 0 ? 'disabled' : ''; ?>>Sell</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>