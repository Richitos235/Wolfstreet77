# 🚀 Wolfstreet77 - Quick Start (5 minut)

## Krok 1: Importuj SQL databázi

1. Otevři **phpMyAdmin**: http://localhost/phpmyadmin
2. Klikni na **Import** v horní liště
3. Vyber soubor: `database/wolf.sql`
4. Klikni **Go**

✅ Databáze je nyní připravena!

---

## Krok 2: Spusť aplikaci

**Přes XAMPP:**
```
http://localhost/wolfstreet77/public/index.php
```

**Přes PHP server:**
```bash
cd C:\xampp\htdocs\wolfstreet77\public
php -S localhost:8000
# Pak otevři: http://localhost:8000
```

✅ Měl bys vidět landing page s hero sekcí!

---

## Krok 3: Vytvoř si účet

1. Klikni na **"Registrace"**
2. Vyplň formulář:
   - **Uživatelské jméno**: `testgamer`
   - **Email**: `test@wolf.local`
   - **Heslo**: `password123`
   - **Role**: Vyber si `Obchodník` 🎩
3. Klikni **"Vytvořit účet"**

✅ Automaticky se přihlásíš a vejdeš na Dashboard!

---

## Krok 4: Prozkoumanej Dashboard

Vidíš:
- 📊 **Trh akcií** - 5 demo akcií s cenami a trendy
- 💼 **Můj Portfolio** - Tvoje akcie (máš 10x WOLF a 5x NEON)
- 📰 **Zprávy** - Ekonomické novinky
- ⚡ **Eventy** - Aktivní herní eventy
- ⏱️ **Countdown** - Odpočítávání do dalšího game ticku

✅ Vše funguje!

---

## Alternativa: Testovací účet

Pokud chceš přeskočit registraci, máš předdefinovaný účet:

**Login stránka:**
```
http://localhost/wolfstreet77/public/login.php
```

**Přihlašovací údaje:**
```
Uživatelské jméno: testuser
Heslo: password
```

---

## Co dál?

### 📖 Plná dokumentace
- `README.md` - Overview projektu
- `SETUP.md` - Detailní nastavení
- `docs/ARCHITECTURE.md` - Technické detaily

### 🛠️ Vývoj
- Backend: `app/Services/` - Přidej novou logiku
- Frontend: `public/assets/js/modules/` - Vytvoř nový JS modul
- CSS: `public/assets/css/` - Styluj komponenty

### 🎮 Příští kroky
- [ ] Implementuj nákup/prodej akcií
- [ ] Přidej budovy a produkci
- [ ] Vytvoř syndikát systém
- [ ] Postav PvP combat

---

## Troubleshooting

### ❌ "Database connection failed"
**Řešení:**
1. Ujistěte se, že MySQL běží v XAMPP
2. Zkontrolujte, že jste importovali `wolf.sql`
3. Zkontrolujte přístupové údaje v `app/Config/Database.php`

### ❌ "Blank white page"
**Řešení:**
1. Zkontrolujte PHP error log
2. Otevřete browser console (F12)
3. Zkuste `php -l public/index.php` pro syntax check

### ❌ "Session lost na home.php"
**Řešení:**
1. Ujistěte se, že `session_start()` je voláno
2. Zkontrolujte `storage/sessions/` permissions

---

## Struktura souborů

```
public/
├── index.php          ← Landing page
├── register.php       ← Registrace
├── login.php          ← Přihlášení
├── logout.php         ← Odhlášení
├── home.php           ← Dashboard (protected)
├── api/
│   └── tick.php       ← Game tick API
└── assets/
    ├── js/
    │   ├── core/app.js
    │   ├── api/apiClient.js
    │   └── modules/
    ├── css/
    │   ├── pages/
    │   ├── components/
    │   └── themes/
    └── images/
```

---

## Security (Implementováno)

✅ **SQL Injection ochrana** - Prepared statements  
✅ **XSS ochrana** - htmlspecialchars()  
✅ **Password hashing** - BCrypt  
✅ **Session security** - SessionHelper  
✅ **CSRF tokens** - Připraveno  

---

## API Reference

### Tick System
```
GET /public/api/tick.php
Response: {
    "success": true,
    "countdown": 7200,
    "formatted": "02:00:00",
    "day": 1,
    "tick": 0
}
```

---

**🎮 Hotovo! Nyní hrej Wolfstreet77!**

Máš-li jakékoli otázky, podívej se do dokumentace nebo kontaktuj team.

*Poslední update: 2026-05-10*
