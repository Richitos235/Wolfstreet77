# Wolfstreet77

Profesionální browserová multiplayer hra o obchodování, ekonomice, produkci a správě majetku.

## Technologický stack

- **Backend**: PHP 8+ (OOP architecture)
- **Frontend**: Vanilla JavaScript (modulární)
- **Databáze**: MySQL
- **Vývoj**: XAMPP
- **Design**: Dark theme s glassmorphism a neon highlights

## Instalace a spuštění

### 1. Příprava XAMPP

```bash
# Ujistěte se, že máte XAMPP nainstalovaný
# Spusťte Apache a MySQL v XAMPP Control Panelu
```

### 2. Klonování projektu

```bash
# Klonujte projekt do XAMPP htdocs adresáře
cd C:\xampp\htdocs
git clone <repo-url> wolfstreet77
cd wolfstreet77
```

### 3. Nastavení databáze

**Možnost A: Přímý import SQL souboru**

1. Otevřete phpMyAdmin: `http://localhost/phpmyadmin`
2. Klikněte na "Import"
3. Vyberte soubor `database/wolf.sql`
4. Klikněte "Go"

**Možnost B: Příkazový řádek**

```bash
mysql -u root < database/wolf.sql
```

### 4. Spuštění aplikace

**Přes XAMPP:**
```
http://localhost/wolfstreet77/public/index.php
```

**Přes PHP server:**
```bash
cd public
php -S localhost:8000
# Pak otevřete http://localhost:8000
```

## Struktura projektu

```
wolfstreet77/
├── app/                          # Backend aplikace
│   ├── Config/                   # Konfigurace
│   ├── Controllers/              # HTTP kontrolery
│   ├── Services/                 # Business logika
│   │   ├── AuthService.php       # Autentizace
│   │   ├── UserService.php       # Správa uživatelů
│   │   └── MarketService.php     # Trh a data
│   ├── Repositories/             # Datový přístup
│   ├── Events/                   # Event system
│   ├── Game/                     # Game engine
│   ├── Helpers/                  # Utility funkce
│   └── Middleware/               # Zabezpečení
├── public/                       # Webroot
│   ├── index.php                 # Landing page
│   ├── register.php              # Registrace
│   ├── login.php                 # Přihlášení
│   ├── logout.php                # Odhlášení
│   ├── home.php                  # Dashboard
│   ├── api/                      # API endpointy
│   └── assets/                   # CSS, JS, obrázky
├── database/
│   ├── wolf.sql                  # SQL schéma a demo data
│   ├── migrations/               # Budoucí migrace
│   └── seeds/                    # Budoucí seeders
├── config/                       # Konfigurace
└── bootstrap/                    # Bootstrap soubory
```

## Autentizace

### Registrace

1. Jděte na `http://localhost/wolfstreet77/public/register.php`
2. Vyplňte formulář:
   - Uživatelské jméno (3-50 znaků)
   - Email
   - Heslo (min. 6 znaků)
   - Vyberte roli: **Obchodník**, **Gangster** nebo **Pasák**

### Přihlášení

1. Jděte na `http://localhost/wolfstreet77/public/login.php`
2. Zadejte uživatelské jméno/email a heslo

### Testovací účet

Pokud jste importovali SQL soubor, máte dostupný testovací účet:

```
Uživatelské jméno: testuser
Heslo: password
Email: test@wolf.local
Role: Obchodník
```

## Herní systémy

### 1. Market systém
- Dynamické ceny akcií
- 5 demo akcií (Wolf Industries, Black Syndicate, Neon Tech, Shadow Corp, Red Market)
- Trendy (vzestup/pokles/stabilní)
- Historie cen

### 2. Player Dashboard
- Základní informace (jméno, role, majetek)
- Statistiky (Síla, Inteligence, Tolerance)
- Portfolio s akciemi
- Celkový majetek

### 3. News & Events
- Zpravodajský feed s ekonomickými zprávami
- Aktivní eventy s efekty na ekonomiku
- Kategorie: market, politics, tech, crime, economy

### 4. Game Tick System
- Herna se dělí na dny
- Každý den trvá 6 hodin reálného času
- Countdown do dalšího ticku
- Strukturovaný pro budoucí automatizaci

## Bezpečnost

✅ **Implementováno:**
- `password_hash()` a `password_verify()` pro hesla
- Prepared statements pro SQL injection ochranu
- `htmlspecialchars()` pro XSS ochranu
- CSRF token helpers
- Session management

## API Endpointy

### Tick System
```
GET /api/tick.php
Response: { countdown, formatted, day, tick, next_tick }
```

## Doporučené příští kroky

1. **Obchodování akcií**
   - Vytvoření `BuyStockService` a `SellStockService`
   - Validace peněž a cen
   - Aktualizace portfolia

2. **Automatizace ticku**
   - Cron job nebo background process
   - Změna cen akcií
   - Vypočítání příjmů
   - Reset akcí

3. **Budovy a produkce**
   - Model pro budovy
   - Systém produkce
   - Pasivní příjmy

4. **Syndikáty**
   - Struktura syndikátu
   - Role v syndikátu
   - Syndikátní banku
   - Upgrades

5. **PvP systém**
   - Attack/Defense logika
   - Cooldown systém
   - Battle logs

6. **Frontend SPA upgrade**
   - Přechod na React/Vue
   - Realtime aktualizace
   - WebSocket podpora

## Modularita

### Backend
Všechny služby dědí z `Service` třídy, což zajišťuje:
- Konsistenci
- Testovatelnost
- Snadnou rozšiřitelnost

### Frontend
Moduly v `public/assets/js/`:
- `core/app.js` - Inicializace
- `api/apiClient.js` - REST wrapper
- `modules/` - Feature moduly
- `ui/` - UI komponenty
- `utils/` - Helpery

## Kódovací standardy

- PSR-12 pro PHP
- ES6+ pro JavaScript
- Čisté názvy
- SRP princip
- Enterprise architecture

## Správa chyb

Všechny chyby jsou se zachází s `htmlspecialchars()` pro ochranu před XSS.

## Budoucí rozšíření

- Multiplayer PvP
- Real-time websockety
- Syndikátní systém
- Leaderboards
- Mobile verze
- Analytics dashboard

---

**Verze:** 0.1.0  
**Poslední aktualizace:** 2026-05-10

