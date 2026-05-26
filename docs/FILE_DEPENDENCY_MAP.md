FILE DEPENDENCY MAP — Wolfstreet77

Generated: 2026-05-26

This document shows explicit include/import trees, endpoint maps, and JS dependency chains derived from repository files.

Top-level entry pages and their dependencies

public/index.php
├── app/Helpers/SessionHelper.php
├── app/Config/Database.php
├── app/Services/Service.php
├── app/Services/MarketService.php
└── (renders) HTML using `$stocks` and `$newsFeeds`

public/home.php
├── app/Helpers/SessionHelper.php
├── app/Config/Database.php
├── app/Services/Service.php
├── app/Services/UserService.php
├── app/Services/MarketService.php
├── app/Helpers/TickHelper.php
├── app/Services/FactoryService.php (required but file missing)
└── includes JS: /assets/js/core/app.js, /assets/js/modules/tickdownModule.js

public/markets.php
├── app/Helpers/SessionHelper.php
├── app/Config/Database.php
├── app/Services/Service.php
├── app/Services/UserService.php
├── app/Services/MarketService.php
├── app/Helpers/TickHelper.php
└── app/Services/FactoryService.php (required but missing)

public/register.php
├── app/Helpers/SessionHelper.php
└── uses `SessionHelper::generateCSRFToken()` and POSTs to `register_process.php`

register_process.php
├── app/Helpers/SessionHelper.php
├── app/Config/Database.php
└── uses direct PDO access via legacy `Database::getInstance()->getConnection()` (incompatible with current `Database::connect()`)

public/api/tick.php
├── app/Helpers/SessionHelper.php
├── app/Config/Database.php
├── app/Services/Service.php
├── app/Services/MarketService.php
├── app/Helpers/TickHelper.php
└── returns JSON with `tick` and `day` after checking `SessionHelper::isAuthenticated()`

app/Providers/Application.php
├── AppConfig (bootstrap/app.php)
├── RouteProvider
└── Instantiates controllers defined in `routes/*.php` and applies middleware

routes/web.php
└── maps `GET /` to `App\Controllers\HealthController::status`

routes/api.php
└── maps `GET /api/health` to `App\Controllers\HealthController::status`

app/Services dependency graph

App\Services\AuthService
├── App\Config\Database::connect()
├── App\Helpers\SessionHelper
└── `users` table

App\Services\UserService
├── App\Config\Database::connect()
├── `player_stocks`, `market_stocks`, `transactions`, `users` tables

App\Services\MarketService
├── App\Config\Database::connect()
├── `market_stocks`, `market_history`, `news_feed`, `game_events`, `game_state`
└── used by `TickHelper`

Event & Tick graph

app/Game/TickManager.php
├── dispatches events via App\Events\EventDispatcher
└── event names: 'game.tick.start', 'game.tick.economy', 'game.tick.production', 'game.tick.events', 'game.tick.reset', 'game.tick.end'

App\Events\EventDispatcher
└── uses App\Events\EventBus (in-memory listeners). No persistent queue.

Frontend JS dependency map

/public/assets/js/core/app.js
├── imports `../api/apiClient.js` (ApiClient)
├── imports `../game/tickManager.js` (client TickManager)
└── on DOMContentLoaded calls `tickManager.initialize()`

/public/assets/js/game/tickManager.js
├── uses apiClient.get('/api/health') periodically

/public/assets/js/modules/tickdownModule.js
├── queries `#tickCountdown` DOM element
├── uses an internal `getCountdown()` placeholder (currently returns 0)

/public/assets/js/api/apiClient.js
├── fetch wrapper using `credentials: 'include'`
├── used by MarketModule, ProfileModule, SyndicateModule (which call `/api/market`, `/api/profile`, `/api/syndicate`)

AJAX endpoint map (existing vs expected)

Implemented endpoints
- `public/api/tick.php` — returns tick countdown JSON (requires session).
- `routes/api.php` -> `GET /api/health` (controller based) -> `HealthController::status()` JSON.

Frontend-expected but NOT implemented (documented by JS modules)
- `GET /api/market` (expected by MarketModule)
- `GET /api/profile` (expected by ProfileModule)
- `GET /api/syndicate` (expected by SyndicateModule)
- `POST /api/trade` or similar buy/sell endpoints (UI actions show buy/sell but no backing API)

Include / require chains (examples)

`public/home.php` include sequence (simplified):
1. require_once `app/Helpers/SessionHelper.php`
2. require_once `app/Config/Database.php`
3. require_once `app/Services/Service.php`
4. require_once `app/Services/UserService.php`
5. require_once `app/Services/MarketService.php`
6. require_once `app/Helpers/TickHelper.php`
7. require_once `app/Services/FactoryService.php` (missing)

`app/Services/UserService.php` -> calls `App\Config\Database::connect()` -> PDO -> queries `player_stocks`, `market_stocks`.

Active vs placeholder modules

Active/implemented:
- `AuthService` (register/login)
- `UserService` (reads portfolio, computes values)
- `MarketService` (reads market data, history)
- `SessionHelper` (session + CSRF token)
- `TickHelper` (reads game_state via MarketService)
- `EventBus` / `EventDispatcher` (in-memory dispatch)

Placeholder / missing or partial:
- `FactoryService.php` referenced but absent.
- `app/Middleware/*` (AuthMiddleware, CsrfMiddleware) — placeholders not enforcing rules.
- `app/Game/Processors/*` — placeholder implementations.
- Many `/api/*` endpoints expected by frontend are not present.

Notes & recommendations
- Replace manual `require_once` chains with Composer autoload (`vendor/autoload.php`) to avoid duplication and inconsistent includes.
- Implement missing endpoints or update frontend to only call implemented endpoints.

END OF `docs/FILE_DEPENDENCY_MAP.md`