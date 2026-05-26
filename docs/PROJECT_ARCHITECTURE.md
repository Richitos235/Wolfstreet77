PROJECT ARCHITECTURE — Wolfstreet77

Version: 1.0
Generated: 2026-05-26

OVERVIEW

Project: Wolfstreet77 — browser-based multiplayer trading/economy game.

Core purpose: Simulate a market economy, allow players to trade stocks, own production assets (factories), participate in events and syndicates, and progress across ticks (6-hour game ticks).

Core technologies
- Backend: PHP 8+ (strict_types used), PDO for MySQL, vlucas/phpdotenv for env.
- Frontend: Vanilla JS modules (ESM), CSS, server-rendered PHP views under `public/` and `resources/views/`.
- Database: MySQL (schema in `database/wolf.sql`).
- Architecture: Hybrid — traditional procedural PHP public pages + an emerging MVC/API provider in `app/Providers`.

Backend stack
- PSR-4 autoload configured in `composer.json` mapping `App\` -> `app/` but many public pages still `require_once` files manually.
- Config/DB: `config/database.php` provides `App\Config\Database::connect()` returning PDO.
- Services: `app/Services/*` contain business logic (`AuthService`, `UserService`, `MarketService`).
- Event system: `app/Events/EventBus` and `EventDispatcher` implement an in-memory pub/sub.
- Game tick: `app/Game/TickManager` orchestrates tick phases via events.

Frontend stack
- ESM modules in `public/assets/js/`:
  - `api/apiClient.js` — fetch wrapper with credentials: 'include'.
  - `core/app.js` — bootstraps `ApiClient` and `TickManager`.
  - `game/tickManager.js`, `modules/tickdownModule.js`, `modules/marketModule.js`, `modules/profileModule.js`.
- Pages include server-rendered HTML with embedded data; JS modules perform periodic polling (health/tick) and expect additional REST endpoints (some not implemented).

AJAX usage
- `ApiClient` expects JSON REST endpoints under `/api/*` (e.g., `/api/tick`, `/api/market`, `/api/profile`).
- Present implemented endpoint: `public/api/tick.php` (checks session and returns countdown + game state).
- Many JS modules expect endpoints that are currently unimplemented (documented below).

Database usage
- Schema: `database/wolf.sql` — canonical source. Important tables: `users`, `market_stocks`, `market_history`, `player_stocks`, `transactions`, `news_feed`, `game_events`, `game_state`.
- Services use PDO prepared statements. Some legacy procedural files use an outdated `Database::getInstance()` pattern — inconsistent.

Session / Auth architecture
- `app/Helpers/SessionHelper.php` is the central session API: start/destroy/set/get/CSRF token generation & verification.
- `AuthService` handles registration/login/logout and sets session variables: `user_id`, `username`, `user`.
- Session regeneration is referenced but `SessionHelper::regenerate()` is missing — a known inconsistency.
- CSRF protection exists (token generated and verified in registration flow) but global CSRF middleware is a placeholder.

---

FULL FOLDER STRUCTURE (important folders)

- `public/` — Public entry pages and `assets/`.
  - `public/index.php`, `public/login.php`, `public/register.php`, `public/home.php`, `public/markets.php`, `public/api/tick.php`.
  - `public/assets/js/` — frontend JS modules.
  - `public/assets/css/` — styles.

- `app/` — Application core (PSR-4 namespace App\)
  - `app/Config/` — `AppConfig.php` (paths), `config` contains `database.php` for DB connect.
  - `app/Providers/` — `Application.php`, `RouteProvider.php` (lightweight router; loads `routes/*.php`).
  - `app/Controllers/` — base `Controller.php` and `HealthController.php` (JSON endpoint).
  - `app/Services/` — `AuthService.php`, `UserService.php`, `MarketService.php`, `Service.php` (base). Business logic resides here.
  - `app/Helpers/` — `SessionHelper.php`, `TickHelper.php`, `ResponseHelper.php`, `Logger.php`.
  - `app/Game/` — `TickManager.php` and `Processors/` (EconomyProcessor, DailyResetProcessor placeholders).
  - `app/Events/` — `EventBus.php`, `EventDispatcher.php`.
  - `app/Middleware/` — `AuthMiddleware.php`, `CsrfMiddleware.php` (placeholders).
  - `app/Repositories/` — base `Repository.php`.

- `resources/views/` — server side templates referenced by `Controller::render()`.

- `routes/` — `routes/web.php`, `routes/api.php` — used by `RouteProvider`. Currently minimal (health endpoint only).

- `database/` — `wolf.sql` (full schema + seeds).

- `docs/` — (this file will live here) technical docs.

---

FILE RELATIONSHIPS (high level)

- Public page (e.g., `public/home.php`) lifecycle:
  - `require_once` `app/Helpers/SessionHelper.php` -> start session.
  - `require_once` relevant services (e.g., `app/Services/UserService.php`, `app/Services/MarketService.php`).
  - Services call `App\Config\Database::connect()` -> PDO connection -> query DB.
  - Page renders HTML with `htmlspecialchars()` for most user strings.
  - JS modules may perform polling to `/api/*` endpoints.

- Router/API approach (not fully used): `bootstrap/app.php` creates `Application` which loads `RouteProvider` and `routes/*.php` -> `Application::run()` matches request -> instantiates controller and invokes action -> `Controller::jsonResponse()` or `render()`.

- Event flow: `TickManager` dispatches events via `EventDispatcher` -> `EventBus` publishes to subscribed listeners (in-memory callbacks).

EXECUTION FLOW (detailed)

1) Login flow (procedural):
   - User submits `login.php` POST.
   - `AuthService::login()` queries `users` by username/email with prepared stmt, verifies password via `password_verify()`.
   - On success: `SessionHelper::start()` -> `SessionHelper::set('user_id', id)` and store `user` array.
   - `SessionHelper::regenerate()` is called in `login.php` but function is missing (bug).
   - Redirect to `home.php`.

2) Page load (authenticated page):
   - `home.php` requires `SessionHelper` and checks `SessionHelper::isAuthenticated()`.
   - Services (`UserService`, `MarketService`) are instantiated and used to fetch portfolio, market data, news, events.
   - The page renders the HTML and includes `assets/js/core/app.js` and module scripts.

3) AJAX requests flow:
   - Frontend `ApiClient.get('/api/whatever')` calls server endpoints. `api/tick.php` exists and validates session server-side.
   - JS `TickManager.pollGameTick()` calls `/api/health` (route provided via `routes/api.php`) for periodic health checks.
   - Many expected endpoints (`/api/market`, `/api/profile`) are not implemented yet.

4) Sessions handling:
   - Session starts with `SessionHelper::start()`.
   - CSRF tokens generated by `SessionHelper::generateCSRFToken()` and checked with `verifyCSRFToken()` where used (registration).
   - No explicit session cookie flags set (Secure, HttpOnly, SameSite) — recommended fixes in `docs/SECURITY_AUDIT.md`.

5) Gameplay actions processing:
   - Intended model: server tick (single leader) executes `TickManager->runTick()` (dispatch phases) and processors mutate DB (stock prices, incomes, events).
   - Presently `Processors` are placeholders; persistent tick runner is not present — `game_state` table stores `next_tick_timestamp` and is used by `TickHelper`.

6) Database updates:
   - Services perform updates via prepared statements.
   - Transaction logging occurs in `transactions` table (used by `UserService` but write operations not fully present in UI).

CORE SYSTEMS (brief)
- Authentication: `app/Services/AuthService.php`, `app/Helpers/SessionHelper.php`.
- Market/Economy: `app/Services/MarketService.php`, DB tables `market_stocks`, `market_history`, `market_history`.
- Player services: `app/Services/UserService.php`, `player_stocks`, `transactions`.
- Game Tick & Events: `app/Game/TickManager.php`, `app/Events/*`, `game_state`, `game_events`.
- UI modules: `public/assets/js/*`.

SECURITY OVERVIEW
- See `docs/SECURITY_AUDIT.md` for a detailed list of vulnerabilities and remediation steps. High-level:
  - Prepared statements largely used in services.
  - CSRF token exists but enforcement is inconsistent.
  - Session hardening missing.
  - Some files echo raw error messages in catch blocks (exposes internals).

PERFORMANCE NOTES
- Hot queries: portfolio value aggregation and repeated market queries on page load.
- Frontend polling/intervals must be tuned to avoid redundant calls.
- EventBus is in-memory; for scale use a shared queue.

KNOWN TECHNICAL DEBT
- Mixed architecture (procedural + MVC) causes duplication.
- Missing `FactoryService.php` though referenced in UI files.
- Inconsistent DB API usage (`Database::connect()` vs legacy `Database::getInstance()`).
- Missing session regeneration helper.
- Placeholders in middleware and processors.

---

END OF `docs/PROJECT_ARCHITECTURE.md` (refer to other docs for deeper maps and system specifics)
