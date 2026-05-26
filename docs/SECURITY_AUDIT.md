SECURITY AUDIT — Wolfstreet77

Generated: 2026-05-26

This security audit summarizes vulnerabilities discovered in the codebase, the affected files, severity classification, explanation, and recommended fixes (short-term and long-term).

Summary of findings (high level)
- Prepared statements are used in service layer but some legacy files use inconsistent DB access.
- CSRF tokens are generated and checked in places, but enforcement is inconsistent across forms and endpoints.
- Session hardening is missing (no cookie flags, missing session_regenerate_id implementation reference).
- Middleware placeholders (`AuthMiddleware`, `CsrfMiddleware`) are not active enforcement points.
- Error handling sometimes exposes exception messages to users (information disclosure risk).
- Some API endpoints expected by frontend do not exist — danger if implemented hastily without proper validation.

Vulnerability list

1) Inconsistent Database API usage (High)
- Affected files: `register_process.php` (uses `Database::getInstance()->getConnection()`), `config/database.php` defines `App\Config\Database::connect()`.
- Why dangerous: runtime errors may bypass validation, lead to untested code paths, and create opportunity for insecure direct DB usage.
- Short-term fix: update `register_process.php` to use `App\Config\Database::connect()` and PDO prepared statements already in use.
- Long-term fix: standardize on Composer autoload and single Database API across project; remove legacy patterns.

2) Missing `SessionHelper::regenerate()` (Medium-High)
- Affected files: `login.php` (calls `SessionHelper::regenerate()`), `app/Helpers/SessionHelper.php` (no `regenerate()` defined).
- Why dangerous: session fixation risk if session IDs are not regenerated on login. Also calling undefined method causes process errors.
- Short-term fix: Implement `SessionHelper::regenerate()` wrapper calling `session_regenerate_id(true)` and reset session cookie params.
- Long-term fix: implement centralized session policy (cookie params, expiration, SameSite, Secure), rotate session at privilege changes.

3) CSRF enforcement inconsistent (Medium)
- Affected files: `register.php` uses CSRF token and `register_process.php` verifies; other state-changing actions (e.g., trade, factory purchase) do not have CSRF tokens implemented because endpoints are missing.
- Why dangerous: when new endpoints are added without CSRF checks, attackers can forge requests.
- Short-term fix: Add middleware or a small helper to check CSRF tokens for all POST endpoints; ensure forms include tokens.
- Long-term fix: Implement `CsrfMiddleware` and have `RouteProvider` apply CSRF middleware to state-changing API routes.

4) Session cookie flags not set (Medium)
- Affected files: `app/Helpers/SessionHelper.php` (start method does not set `session_set_cookie_params`).
- Why dangerous: missing `HttpOnly`, `Secure`, and `SameSite` increases risk of session theft via XSS and insecure transports.
- Short-term fix: Modify `SessionHelper::start()` to call `session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>isset($_SERVER['HTTPS'])]);` before `session_start()`.
- Long-term fix: Use a session handler (Redis) and strict cookie policies; enforce HTTPS in deployment and set `session.cookie_secure` in php.ini.

5) Middleware placeholders (Medium)
- Affected files: `app/Middleware/AuthMiddleware.php`, `app/Middleware/CsrfMiddleware.php`.
- Why dangerous: if route-based controller flow relies on middleware to protect endpoints, placeholder implementations mean endpoints could be public.
- Short-term fix: Ensure procedural public pages continue to validate `SessionHelper::isAuthenticated()` until middleware implemented.
- Long-term fix: Implement middleware logic and migrate public pages to use provider routing with enforced middleware or central request bootstrap.

6) Error disclosure (Medium)
- Affected files: `login.php` catches exceptions and echoes `$e->getMessage()` to the user.
- Why dangerous: reveals internal DB errors, stack traces, or configuration values to attackers.
- Short-term fix: remove exception messages from user-facing output; log details to server log via `error_log()`.
- Long-term fix: centralize error handling, mask production errors, and use environment-aware debug settings.

7) Missing rate-limits / brute-force protections (Medium)
- Affected files: `login.php` and `AuthService::login()`.
- Why dangerous: accounts might be brute-forced.
- Short-term fix: implement simple lockout logic (count failed attempts in session or DB for IP/user) and slow down responses.
- Long-term fix: use a hardened rate-limiting service (Redis, API gateway), CAPTCHAs for suspicious activity.

8) Output encoding inconsistencies (Low-Medium)
- Observations: Many templates use `htmlspecialchars()` but some paths render fields without escapes.
- Why dangerous: potential XSS if untrusted content is output without escaping.
- Fix: audit all templates and ensure `htmlspecialchars()` for any user-sourced string.

9) API exposure risk (Medium)
- Observations: frontend expects `/api/market`, `/api/profile`, `/api/syndicate`. Implementing these without authentication and validation creates data leak and trust issues.
- Recommendation: Any new API endpoint must validate session, enforce permissions, sanitize inputs, and apply CSRF protection for state-changing endpoints.

Risk severity summary
- High: DB API inconsistency (causes errors), missing session regeneration (session fixation), missing central session hardening.
- Medium: CSRF inconsistencies, middleware placeholders, error disclosure, API exposure risks.
- Low: template escaping omissions (fixable via audit).

Appendix: quick remediation checklist (practical order)
1. Implement `SessionHelper::regenerate()` and set secure cookie params in `SessionHelper::start()`.
2. Standardize DB access: replace `Database::getInstance()` usage with `App\Config\Database::connect()` in `register_process.php` and other legacy files.
3. Remove or silence exception messages in public pages; log to server logs.
4. Add CSRF verification wrapper and include tokens in all POST forms; gradually implement `CsrfMiddleware` and apply to API routes.
5. Implement basic rate-limiting on `AuthService::login()` (failed attempts counter in DB or cache).
6. Audit templates for missing `htmlspecialchars()` and ensure encoding.
7. Implement authentication checks for all API endpoints and avoid exposing internal endpoints without validation.

END OF `docs/SECURITY_AUDIT.md`