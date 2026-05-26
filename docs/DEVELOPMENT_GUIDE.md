DEVELOPMENT GUIDE — Wolfstreet77

Generated: 2026-05-26

Purpose
This guide explains how to safely extend the codebase, conventions used, architecture patterns, and recommended practices for adding features and endpoints.

Repository patterns & conventions
- Namespace: PSR-4 `App\` mapped to `app/` (see `composer.json`). Use `App\` namespace for PHP classes.
- Strict typing: PHP files use `declare(strict_types=1);`. Maintain strict types where possible.
- Services: Business logic belongs in `app/Services/*`. Keep services stateless where possible and inject dependencies.
- Helpers: Utility functions belong in `app/Helpers/*` (session, tick, logger).
- Controllers: Use `app/Controllers/*` for API endpoints when migrating to provider router. For now many pages are procedural in `public/`.
- Events: Use `app/Events/EventBus` + `EventDispatcher` for in-process eventing; subscribe in processor classes.

How to add a safe API endpoint
1. Decide controller vs procedural file:
   - Short-term: add `public/api/myendpoint.php` and `require_once` necessary services and `SessionHelper`.
   - Mid-term: add a route in `routes/api.php` and implement `App\Controllers\MyController::action` then call `Controller::jsonResponse()`.
2. Authentication: always call `SessionHelper::start()` and verify `SessionHelper::isAuthenticated()` for endpoints that require login.
3. Input validation & sanitization: validate inputs server-side using filters and explicit checks; never trust client data.
4. DB operations: use prepared statements via `App\Config\Database::connect()` returned PDO. For multi-step operations use DB transactions (`BEGIN/COMMIT/ROLLBACK`).
5. CSRF: for state-changing endpoints, require CSRF token verification via `SessionHelper::verifyCSRFToken()` for form-based flows.
6. Output: always return JSON for API endpoints with `Content-Type: application/json; charset=utf-8` and use `json_encode()` with `JSON_UNESCAPED_UNICODE`.

Coding conventions
- Files must begin with `declare(strict_types=1);`.
- Use namespaces matching directory structure (e.g., `namespace App\Services;`).
- Use typed properties and parameter/return type hints where applicable.
- Use prepared statements and parameter binding. Avoid string concatenation for SQL.
- Use `htmlspecialchars()` on user data when rendering in HTML templates.
- Keep functions small and single-responsibility.

How to add a new Service
- Create file `app/Services/MyNewService.php`.
- Namespace `App\Services` and extend `Service` if useful.
- Obtain PDO with `App\Config\Database::connect()`.
- Add unit tests in `tests/` folder.

How to add frontend module
- Create ESM module in `public/assets/js/modules/` following existing pattern.
- Use `ApiClient` for server communication; always send credentials (`credentials: 'include'`).
- Add minimal DOM updates using `DomHelper` and avoid direct global state.

Naming conventions
- Classes: PascalCase (e.g., `MarketService`).
- Files: match class name exactly (PSR-4). PHP file path should map to namespace.
- JS modules: camelCase or moduleName.js (existing modules follow `marketModule.js`, `profileModule.js`).
- DB tables: snake_case plural (existing pattern).

Testing & local dev
- `composer install` to ensure autoload and dependencies (phpdotenv).
- Start a local PHP server for quick testing (composer script: `php -S localhost:8000 -t public`).
- Use `test.php` in repo for DB connectivity check.

Migration path to a clean architecture (recommended)
1. Add Composer autoload include to all `public/*.php` pages: `require __DIR__ . '/../vendor/autoload.php';` and remove manual `require_once` chains.
2. Implement `app/Providers` run path and gradually move endpoints to controllers in `app/Controllers/`.
3. Implement `AuthMiddleware` and `CsrfMiddleware` and use `RouteProvider` to attach middleware to API routes.
4. Implement a CLI worker to run `TickManager->runTick()` on schedule (cron or supervisor). Persist logs and use locking.
5. Introduce a cache layer (Redis) for hot reads: market lists, news feeds.
6. Add unit tests for services and integration tests for API endpoints.

Reusable utilities
- `SessionHelper` — central session + CSRF token manager.
- `TickHelper` — reads game state and provides countdown helpers.
- `ApiClient` (frontend) — for fetch wrapper.
- `EventBus`/`EventDispatcher` — in-process eventing for tick phases.

Database query guidelines
- For read-heavy queries (market lists) use `LIMIT` and indexes.
- For aggregations (portfolio value) prefer single optimized queries as in `UserService::getPortfolioValue`.
- Wrap multi-step money operations in DB transaction and write to `transactions` ledger.

Deployment note
- Ensure MySQL configured with correct charset `utf8mb4` as schema expects.
- Enable HTTPS in production and set `session.cookie_secure`.

END OF `docs/DEVELOPMENT_GUIDE.md`