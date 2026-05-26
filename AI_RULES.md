# AI Rules & Tech Stack — Wolfstreet77

## Tech Stack
- **Backend**: PHP 8.0+ with strict typing (`declare(strict_types=1);`) and PSR-4 autoloading.
- **Architecture**: Hybrid Procedural/MVC. Core logic resides in `app/Services/`, while entry points are either procedural pages in `public/` or controller-based routes.
- **Database**: MySQL using PDO with prepared statements. Schema is managed via `database/wolf.sql` and `database/database_fix.sql`.
- **Frontend**: Vanilla JavaScript (ESM) modules for client-side logic, organized in `public/assets/js/`.
- **Styling**: Custom CSS with a focus on dark themes, glassmorphism, and neon highlights, located in `public/assets/css/`.
- **Session & Security**: Centralized `SessionHelper` for authentication and CSRF protection.
- **Game Logic**: Event-driven tick system orchestrated by `TickManager` and `EventDispatcher`.
- **API**: RESTful JSON endpoints located in `public/api/` or handled via `App\Controllers`.

## Coding Rules & Library Usage

### Backend (PHP)
- **Namespacing**: All classes in `app/` must use the `App\` namespace.
- **Business Logic**: Must be placed in `app/Services/`. Services should be stateless and injected where needed.
- **Database Access**: Always use `App\Config\Database::connect()` to get a PDO instance. Never use legacy `Database::getInstance()` patterns.
- **Security**: 
    - Always use prepared statements for SQL queries.
    - Use `htmlspecialchars()` when rendering user-provided data in HTML.
    - Verify CSRF tokens for all state-changing (POST/PUT/DELETE) requests using `SessionHelper`.
- **Error Handling**: Do not expose raw exception messages to the user. Log errors and show generic messages.

### Frontend (JavaScript)
- **Modularity**: Use ESM `import`/`export`. New features should be added as modules in `public/assets/js/modules/`.
- **API Communication**: Always use the `ApiClient` class for server requests to ensure consistent header and credential handling.
- **DOM Manipulation**: Use `DomHelper` for querying and modifying the DOM to keep code clean.
- **State Management**: Keep frontend state minimal and synchronized with the backend via periodic polling or explicit API calls.

### Styling (CSS)
- **Organization**: Follow the existing structure: `base/` for resets, `components/` for reusable UI, and `pages/` for page-specific styles.
- **Design Language**: Maintain the "Nextrade" aesthetic: dark backgrounds (`#0d1117`), blue/green accents, and rounded corners (`12px`-`24px`).

### General
- **File Headers**: Every PHP file must start with `<?php declare(strict_types=1);`.
- **Documentation**: Update `AI_CONTEXT.md` or relevant documentation when introducing major architectural changes.