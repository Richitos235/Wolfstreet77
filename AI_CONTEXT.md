AI_CONTEXT — Wolfstreet77 (Project memory for AI agents)

Generated: 2026-05-26

Purpose
This file is a concise, high-utility reference for AI agents and developers to quickly understand the project, its important files, dangerous areas, and how to act safely when making changes.

ARCHITECTURE SUMMARY
- Hybrid architecture: Procedural `public/*.php` pages render server-side HTML and require service classes directly. A parallel, incomplete MVC/API provider exists under `app/Providers` with `routes/*.php` mapping to `app/Controllers`.
- Service layer: `app/Services/*` houses the majority of business logic (AuthService, UserService, MarketService).
- DB: Canonical schema in `database/wolf.sql`.
- Event/Tick: `app/Game/TickManager.php` dispatches phases through `app/Events/EventDispatcher` -> `EventBus` (in-memory).
- Frontend: ESM modules in `public/assets/js/` rely on REST endpoints and use `ApiClient` with `credentials: 'include'`.

IMPORTANT FILES (quick list)
- `app/Helpers/SessionHelper.php` — central session and CSRF API.
- `app/Config/database.php` — `Database::connect()` for PDO.
- `app/Services/AuthService.php` — register/login logic.
- `app/Services/UserService.php` — portfolio and asset computations.
- `app/Services/MarketService.php` — market data and game_state access.
- `app/Game/TickManager.php` — orchestrates tick phases.
- `app/Events/EventBus.php` / `EventDispatcher.php` — publish/subscribe.
- `public/home.php`, `public/markets.php`, `public/index.php`, `public/api/tick.php` — key public pages and tick endpoint.
- `database/wolf.sql` — DB schema & seeding.

DANGEROUS AREAS & TECHNICAL DEBT (must warn future agents)
- Missing `FactoryService.php` though referenced in UI files (`factories.php`, `home.php`). Do not assume factory persistence exists.
- `register_process.php` uses `Database::getInstance()` (legacy) — replacing or updating required before trusting it.
- `SessionHelper::regenerate()` is called in `login.php` but not defined — implement to avoid session fixation.
- Middleware stubs (`AuthMiddleware`, `CsrfMiddleware`) are placeholders — do not rely on them for security.
- Many frontend modules expect APIs that are not implemented; adding them requires careful auth and validation.

NAMING & CODING PATTERNS
- PSR-4 `App\` namespace for `app/`.
- PHP files start with `declare(strict_types=1);`.
- Services are stateless classes under `App\Services`.
- JS modules use ESM and follow `moduleName.js` naming (e.g., `marketModule.js`).

BACKEND / FRONTEND FLOW (concise)
- Server-rendered pages load initial data using services -> DB -> render HTML with embedded values.
- Frontend JS bootstraps `ApiClient` and `TickManager` to poll health endpoints; modules call `/api/*` endpoints expected to return JSON.
- Session is cookie-based and controlled by `SessionHelper`.

DB RELATIONSHIPS (concise)
- `users` primary entity: referenced by `player_stocks`, `transactions`.
- `market_stocks` referenced by `market_history`, `player_stocks`, and `transactions.related_stock_id`.
- `game_state` stores tick schedule; `game_events` store active events.

KNOWN BUGS / INCOMPLETE SYSTEMS
- Tick processors are placeholders; no automated tick runner exists.
- Trading endpoints are missing.
- Factory subsystem referenced in UI is incomplete.

RECOMMENDED SAFE ACTIONS FOR AI AGENTS
- Do NOT implement new API endpoints without adding server-side authentication checks and CSRF protection for state changes.
- When changing DB access, prefer `App\Config\Database::connect()` and update legacy files accordingly.
- Implement `SessionHelper::regenerate()` early to fix session fixation risk.
- Add unit tests for services before refactoring public page includes.

PROJECT PHILOSOPHY
- Keep services small and testable.
- Prefer explicit prepared statements and typed method signatures.
- Migrate gradually from procedural pages to controller-driven endpoints.

END OF AI_CONTEXT.md
