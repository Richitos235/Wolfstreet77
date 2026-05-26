GAME SYSTEMS — Wolfstreet77

Overview
This document lists gameplay systems discovered in the repository, their purpose, files involved, DB tables, UI points, AJAX usage, execution flows, known gaps, and scalability notes.

1) Stocks / Market

Purpose
- Provide tradable assets whose prices change over time (game ticks) and form the core economy.

Important files
- `app/Services/MarketService.php`
- `public/markets.php`, `public/index.php`, `public/home.php`
- `public/assets/js/modules/marketModule.js` (frontend caller)
- `database/wolf.sql` (tables and seed data)

DB tables
- `market_stocks` — current_price, previous_price, trend, volatility.
- `market_history` — historical price per `game_tick`.
- `transactions` — references to `related_stock_id` when trades occur.

UI rendering
- Server-rendered tables in `public/home.php` and `public/markets.php` show stocks.
- Buy/Sell buttons present in UI, but associated API endpoints are missing.

AJAX/API usage
- Frontend `MarketModule` expects `GET /api/market` (not implemented).
- Polling: `TickManager` calls `/api/health` frequently; `tick.php` provides countdown.

Execution flow
- Intended: Tick processor updates `market_stocks`, records to `market_history`, adjusts player portfolios/transactions.
- Actual: `EconomyProcessor` is placeholder; no automated price updates exist in codebase; database seed contains demo prices.

Security concerns
- Trade endpoints not implemented; when added, must validate user funds and use DB transactions.

Missing/Incomplete
- Buy/Sell APIs and server-side trade processing.

Scalability notes
- `market_history` can grow large; partitioning or retention policies recommended.


2) Economy Processor & Game Tick

Purpose
- Perform scheduled game updates (price changes, incomes, event resolution) every 6 hours.

Important files
- `app/Game/TickManager.php`
- `app/Game/Processors/EconomyProcessor.php`
- `app/Game/Processors/DailyResetProcessor.php`
- `app/Helpers/TickHelper.php`
- `public/api/tick.php`

DB tables
- `game_state` — single-row state storing `next_tick_timestamp`, `current_tick`, `current_day`.

Execution flow
- Architectural plan: `TickManager->runTick()` dispatches ordered events (`game.tick.start`, `game.tick.economy`, `game.tick.production`, `game.tick.events`, `game.tick.reset`, `game.tick.end`) via `EventDispatcher`/`EventBus`.
- Processors subscribe to events and perform DB updates.

Actual status
- Processors are placeholders and do not modify DB; no scheduled runner (cron/worker) exists in repo.

Security & concurrency
- Tick must be executed by a single leader; otherwise race conditions in `game_state` and market updates will occur. Current design assumes single-process execution.

Missing/Incomplete
- A CLI worker or cron job to run tick logic.
- Processor implementations.

Scalability notes
- For multi-instance deployment, use a distributed lock (Redis) or external scheduler.


3) Player Portfolios & Transactions

Purpose
- Track player-owned stocks, quantities, cost basis, and ledger of transactions.

Important files
- `app/Services/UserService.php`
- `database/wolf.sql` tables: `player_stocks`, `transactions`, `users`

Execution flow
- UserService calculates current portfolio value with joins between `player_stocks` and `market_stocks`.
- Transactions table intended to record buy/sell events (write operations not present in current codebase).

Security concerns
- Ensure DB transactions for buy/sell to maintain ledger integrity.

Missing/Incomplete
- Trade processing endpoints.


4) Factories / Production (partial)

Purpose
- Passive income generation from owned production buildings.

Important files
- `factories.php` (UI prototype)
- `factories.php` references `FactoryService` which is missing from `app/Services`.

DB tables
- Not present — factories currently prototyped in UI arrays.

Execution flow
- Intended: `FactoryService` manages owned factories and production per tick.

Missing/Incomplete
- `FactoryService.php` implementation and DB tables for owned factories.


5) News / Events

Purpose
- Provide narrative and game events that affect market or player risk.

Important files
- `app/Services/MarketService::getNewsFeeds()`
- `app/Services/MarketService::getActiveEvents()`
- `database/wolf.sql` tables: `news_feed`, `game_events`

Execution flow
- Events can be seeded and have `expires_at`.
- Intended tick processors should apply `game_events` effects to economy.

Missing/Incomplete
- Processors that apply event effects.


6) Authentication & Session

Purpose
- Provide user authentication, session management, CSRF tokens.

Important files
- `app/Services/AuthService.php`
- `app/Helpers/SessionHelper.php`
- `register.php`, `register_process.php`, `login.php`, `logout.php`

Execution flow
- Registration flow uses `AuthService::register()` which inserts new `users` and sets session.
- Login uses `AuthService::login()` and sets `user_id` and `user` in session.

Security concerns
- `register_process.php` uses legacy DB API call `Database::getInstance()` and will error.
- `SessionHelper::regenerate()` missing (invoked in `login.php`).


7) Syndicates / Social systems (scaffold)

Purpose
- Guilds/factions for player cooperation/competition.

Important files
- Frontend: `public/assets/js/modules/syndicateModule.js` expects `/api/syndicate`.

Status
- Not implemented server-side. No DB tables discovered for syndicates.


8) Stats / Player Progression

Purpose
- Player stats stored on `users` table (`strength`, `intelligence`, `tolerance`).

Files & UI
- Stats displayed via `home.php` profile area. No dedicated stats service existed beyond `UserService`.


9) Banking

Purpose
- `bank_money` field in `users` stores saved funds.

Files
- `users.bank_money` referenced in `UserService` and `home.php` UI.

Missing/Incomplete
- No separate banking service for deposits/withdrawals or interest logic.


END OF `docs/GAME_SYSTEMS.md`