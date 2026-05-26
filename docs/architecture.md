# Wolfstreet77 - Kompletní Architektura

## Přehled

Wolfstreet77 je profesionální **enterprise-grade** browserová ekonomická hra s modulární architekturou, čistým kódem a bezpečností.

---

## Backend Architektura

### 1. Vrstvená struktura

```
┌─────────────────────────────────────┐
│       Prezentační vrstva            │
│   (Controllers, HTTP Response)      │
├─────────────────────────────────────┤
│       Business Logic vrstva         │
│   (Services, Game Logic)            │
├─────────────────────────────────────┤
│       Data Access vrstva            │
│   (Repositories, Database)          │
├─────────────────────────────────────┤
│       Datová vrstva                 │
│   (MySQL Database)                  │
└─────────────────────────────────────┘
```

### 2. Komponenty

**Config**
- `Database.php` - PDO connection singleton
- `AppConfig.php` - Základní konfigurace

**Controllers**
- `Controller.php` - Base class
- `HealthController.php` - Status checks

**Services**
- `AuthService.php` - Autentizace (register, login, logout)
- `UserService.php` - Správa hráče a portfolia
- `MarketService.php` - Trh, akcie, zprávy

**Helpers**
- `SessionHelper.php` - Session management
- `TickHelper.php` - Game tick management
- `ResponseHelper.php` - JSON responses
- `Logger.php` - Audit logs

**Events**
- `EventBus.php` - Event publishing/subscribing
- `EventDispatcher.php` - Event orchestration

**Game**
- `TickManager.php` - Game loop manager
- `Processors/` - Economy, Reset, Production processors

**Middleware**
- `AuthMiddleware.php` - Ověření přihlášení
- `CsrfMiddleware.php` - CSRF ochrana

---

## Data Flow

### Registrace

```
1. User POST /public/register.php
   ├─ Validace formuláře
   ├─ AuthService::register()
   │  ├─ Validace dat
   │  ├─ Kontrola existujícího uživatele
   │  ├─ Hash hesla
   │  ├─ INSERT do users
   │  └─ Vrácení user_id
   ├─ SessionHelper::set() - Uložení do session
   └─ Redirect /public/home.php
```

### Home Dashboard Load

```
1. User GET /public/home.php
   ├─ SessionHelper::isAuthenticated() ✓
   ├─ UserService::getUserById() → User data
   ├─ UserService::getUserPortfolio() → Stocks
   ├─ MarketService::getAllStocks() → Market overview
   ├─ MarketService::getNewsFeeds() → News
   ├─ TickHelper::getTickCountdown() → Countdown
   └─ Render home.php s daty
```

---

## Frontend Architektura

### Modulární struktura

```
public/assets/
├── js/
│   ├── core/
│   │   └── app.js                  # Bootstrap
│   ├── api/
│   │   └── apiClient.js            # REST wrapper
│   ├── modules/
│   │   ├── marketModule.js
│   │   ├── tickdownModule.js
│   │   └── ...
│   └── utils/
│       ├── storage.js
│       └── dom.js
├── css/
│   ├── pages/
│   │   ├── landing.css
│   │   ├── auth.css
│   │   └── dashboard.css
│   └── themes/
│       └── dark.css
```

---

## Game Tick System

### Mechanika

```
game.tick.start
    ↓
game.tick.economy (změna cen)
    ↓
game.tick.production (výroba)
    ↓
game.tick.events (random events)
    ↓
game.tick.reset (cleanup)
    ↓
game.tick.end
```

---

## Bezpečnost

✅ Implemented:
- `password_hash()` pro hesla
- Prepared statements
- `htmlspecialchars()` XSS ochrana
- CSRF token helpers
- Session management
