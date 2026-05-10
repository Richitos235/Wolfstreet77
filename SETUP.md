# Wolfstreet77 - Průvodce nastavením a provozem

## Quick Start (2 minuty)

### 1. Databáze

Otevřete phpMyAdmin na `http://localhost/phpmyadmin` a:
1. Klikněte na Import
2. Vyberte `database/wolf.sql`
3. Klikněte Go

### 2. Spusťte aplikaci

```
http://localhost/wolfstreet77/public/index.php
```

### 3. Vytvořte účet nebo se přihlaste

- **Registrace**: http://localhost/wolfstreet77/public/register.php
- **Přihlášení**: http://localhost/wolfstreet77/public/login.php
- **Testovací účet**: `testuser / password`

---

## Detailní vysvětlení

### Login & Register Flow

```
1. Uživatel jde na /public/register.php
2. Vyplní formulář (username, email, password, role)
3. AuthService::register() validuje data
4. Password se hashuje `password_hash($password, PASSWORD_BCRYPT)`
5. Uživatel se uloží do DB s default statistikami
6. SessionHelper vytvoří session
7. Redirect na home.php
```

### AuthService

```php
// Registrace
$authService = new AuthService();
$result = $authService->register('username', 'email@test', 'password', 'trader');

// Přihlášení
$result = $authService->login('username', 'password');

// Odhlášení
$authService->logout(); // Destroy session
```

### Session Management

```php
SessionHelper::start();
SessionHelper::set('user_id', 123);
SessionHelper::get('user_id'); // 123
SessionHelper::isAuthenticated(); // true/false
SessionHelper::generateCSRFToken();
SessionHelper::verifyCSRFToken($token);
```

### Database Connection

```php
use App\Config\Database;

$db = Database::connect(); // PDO instance
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([1]);
$user = $stmt->fetch();
```

### Services

**AuthService** - Registrace, přihlášení, odhlášení

```php
public function register($username, $email, $password, $roleType)
public function login($username, $password)
public function logout()
```

**UserService** - Pracuje s hráčem a jeho daty

```php
public function getUserById($userId)
public function getUserPortfolio($userId)
public function getPortfolioValue($userId)
public function getTotalAssets($userId)
public function getRecentTransactions($userId, $limit)
```

**MarketService** - Trh, akcie, zprávy, eventy

```php
public function getAllStocks()
public function getStockById($stockId)
public function getStockPriceChange($stockId)
public function getStockHistory($stockId, $limit)
public function getNewsFeeds($limit)
public function getActiveEvents()
public function getGameState()
public function getTickCountdown()
```

### Frontend Architecture

```javascript
// App bootstrap
import { ApiClient } from './api/apiClient.js';
import { TickCountdown } from './modules/tickdownModule.js';

const apiClient = new ApiClient();
const tickCountdown = new TickCountdown();
tickCountdown.start();
```

### API Client

```javascript
const apiClient = new ApiClient();

// GET request
const data = await apiClient.get('/api/tick.php');

// POST request
const result = await apiClient.post('/api/trade.php', {
    action: 'buy',
    stock_id: 1,
    quantity: 10
});
```

---

## Database Schéma

### Users Table
```sql
id - primary key
username - unique
email - unique
password - hashed
role_type - enum(trader, gangster, pimp)
money - cash
bank_money - savings
strength, intelligence, tolerance - player stats
next_tick - timestamp dalšího ticku
```

### Market Stocks
```sql
id - primary key
name - company name
short_name - ticker
current_price - aktuální cena
previous_price - cena z minulého ticku
trend - rising/falling/stable
```

### Player Stocks (Portfolio)
```sql
user_id - foreign key
stock_id - foreign key
quantity - počet akcií
buy_price_total - celková cena nákupu
```

### News Feed
```sql
id - primary key
title - titulek
content - obsah
category - enum(market, politics, tech, crime, economy)
```

### Game Events
```sql
id - primary key
title - event titulek
description - popis
effect_type - enum(market, economy, social, danger)
```

---

## Game Tick System

### Jak funguje

```
1. Game začíná na Dnu 1, Ticku 0
2. Každý den trvá 6 hodin reálného času
3. Po 6 hodinách se spustí game tick
4. Tick provádí:
   - Změnu cen akcií
   - Vypočítání příjmů
   - Aktivaci eventů
   - Reset některých akcí
```

### TickHelper

```php
$tickHelper = new TickHelper();
$tickHelper->getTickCountdown(); // sekundy do dalšího ticku
$tickHelper->getTickCountdownFormatted(); // "05:30:45"
$tickHelper->getGameDay();
$tickHelper->getGameTick();
```

### Frontend Countdown

```javascript
const countdown = new TickCountdown('#tickCountdown');
countdown.start(); // Začne odpočítávat
```

---

## Zabezpečení

### SQL Injection ochrana
```php
// ✅ Bezpečné (prepared statement)
$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute([':id' => $userId]);

// ❌ Není bezpečné
$stmt = $db->query("SELECT * FROM users WHERE id = $userId");
```

### XSS Protection
```php
// ✅ Bezpečné
echo htmlspecialchars($user['username']);

// ❌ Není bezpečné
echo $user['username'];
```

### Password Security
```php
// ✅ Hashovací
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
password_verify($inputPassword, $hashedPassword);

// ❌ Není bezpečné
password_md5($password);
```

### CSRF Protection
```php
// Generuj token
$token = SessionHelper::generateCSRFToken();

// Ověř token
if (!SessionHelper::verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF token invalid');
}
```

---

## Vývoj nových features

### Přidej nový Service

```php
<?php
namespace App\Services;

class NewFeatureService extends Service
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function doSomething()
    {
        // Your logic
    }
}
```

### Přidej nový API Endpoint

```php
<?php
// public/api/newfeature.php

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Services/NewFeatureService.php';

$service = new NewFeatureService();
$result = $service->doSomething();

echo json_encode(['success' => true, 'data' => $result]);
```

### Přidej nový JS Module

```javascript
// public/assets/js/modules/newModule.js

export class NewModule {
    constructor(apiClient) {
        this.apiClient = apiClient;
    }

    async load() {
        const data = await this.apiClient.get('/api/newfeature.php');
        return data;
    }
}
```

---

## Troubleshooting

### "Database connection failed"
- Ujistěte se, že MySQL běží v XAMPP
- Zkontrolujte credentials v `app/Config/Database.php`
- Importujte `database/wolf.sql`

### "Session not saved"
- Zkontrolujte, že je `session_start()` na začátku PHP souborů
- Ujistěte se, že `storage/sessions/` existuje a je zapisovatelná

### "Page is blank"
- Zapněte debug mód: nastavte `APP_DEBUG=true` v `.env`
- Zkontrolujte PHP error logs
- Otevřete browser console (F12)

### "Login je pomalý"
- Zkontrolujte MySQL performance
- Přidejte indexy na tabulky
- Zvyšte `max_connections` v MySQL

---

## Performance Tips

1. **Caching**
   - Udržujte seznam akcií v cache
   - Cachujte game state

2. **Database**
   - Přidejte indexy na `users.username`, `market_stocks.updated_at`
   - Časté selecty optimalizujte

3. **Frontend**
   - Lazy-loadujte CSS/JS
   - Minimalizujte assets

---

## Roadmapa

- [ ] Obchodování akcií (buy/sell)
- [ ] Budovy a produkce
- [ ] Syndikáty
- [ ] PvP systém
- [ ] Leaderboards
- [ ] WebSocket real-time updates
- [ ] Mobile verze
- [ ] Analytics

---

**Poslední aktualizace:** 2026-05-10
