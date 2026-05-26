<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';

use App\Helpers\SessionHelper;

SessionHelper::start();

if (SessionHelper::isAuthenticated()) {
    header('Location: home.php');
    exit;
}

$csrfToken = SessionHelper::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wolfstreet77 | Registrace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/layout.css" />
    <style>
        .role-selector {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 8px;
        }
        .role-option {
            position: relative;
            cursor: pointer;
        }
        .role-option input {
            position: absolute;
            opacity: 0;
        }
        .role-card {
            padding: 12px;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            background: rgba(0, 0, 0, 0.2);
            transition: var(--transition);
        }
        .role-option input:checked + .role-card {
            border-color: var(--accent-blue);
            background: rgba(88, 166, 255, 0.1);
        }
        .role-name {
            display: block;
            font-weight: 700;
            color: var(--accent-blue);
            font-family: 'Rajdhani';
            text-transform: uppercase;
        }
        .role-desc {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
        }
    </style>
</head>
<body class="auth-layout">
    <div class="auth-card glass-panel glass-panel--heavy">
        <div style="text-align: center; margin-bottom: var(--space-lg);">
            <h1 style="font-size: 32px; color: var(--accent-blue); margin-bottom: 4px;">Wolfstreet77</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Založ si účet a ovládni trhy</p>
        </div>

        <form action="register_process.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div class="form-group">
                <label>Uživatelské jméno</label>
                <input type="text" name="username" class="input-field" placeholder="např. JordanB" required>
            </div>

            <div class="form-group">
                <label>Emailová adresa</label>
                <input type="email" name="email" class="input-field" placeholder="vas@email.cz" required>
            </div>

            <div class="form-group">
                <label>Heslo</label>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label>Vyber si svou roli</label>
                <div class="role-selector">
                    <label class="role-option">
                        <input type="radio" name="role_type" value="trader" checked>
                        <div class="role-card">
                            <span class="role-name">Trader</span>
                            <span class="role-desc">Master of the markets</span>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role_type" value="gangster">
                        <div class="role-card">
                            <span class="role-name">Gangster</span>
                            <span class="role-desc">Power of the streets</span>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role_type" value="pimp">
                        <div class="role-card">
                            <span class="role-name">Boss</span>
                            <span class="role-desc">King of the underworld</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Vytvořit účet</button>
        </form>

        <div style="text-align: center; margin-top: var(--space-md); font-size: 14px; color: var(--text-muted);">
            Už máš účet? <a href="login.php" style="color: var(--accent-blue); font-weight: 600;">Přihlas se</a>
        </div>
    </div>
</body>
</html>