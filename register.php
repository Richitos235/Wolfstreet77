<?php

declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';

use App\Helpers\SessionHelper;

SessionHelper::start();

// Pokud je uživatel už přihlášen, pošleme ho do hry
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
    <title>Wolfstreet77 | Registrace nového hráče</title>
    
    <link rel="stylesheet" href="assets/css/base/reset.css">
    <link rel="stylesheet" href="assets/css/pages/dashboard.css">
    
    <style>
        body {
            background: radial-gradient(circle at center, #11314d 0%, #071018 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(16, 27, 43, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .auth-title {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: var(--green);
            margin-bottom: 10px;
        }

        .auth-subtitle {
            text-align: center;
            color: var(--muted);
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 600;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            background: #0d1827;
            border: 1px solid var(--border);
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            transition: 0.25s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 10px rgba(89, 255, 154, 0.2);
        }

        /* Role Selection Styling */
        .role-selection {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }

        .role-option {
            position: relative;
            text-align: center;
            cursor: pointer;
        }

        .role-option input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .role-box {
            background: #0d1827;
            border: 1px solid var(--border);
            padding: 15px 10px;
            border-radius: 12px;
            transition: 0.2s;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .role-box .icon { font-size: 24px; }
        .role-box .name { font-size: 12px; font-weight: 700; color: var(--muted); }

        .role-option input:checked + .role-box {
            border-color: var(--green);
            background: rgba(89, 255, 154, 0.05);
            box-shadow: 0 0 15px rgba(89, 255, 154, 0.1);
        }

        .role-option input:checked + .role-box .name {
            color: var(--green);
        }

        .btn-register {
            width: 100%;
            background: linear-gradient(90deg, var(--green2), #0f5132);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(41, 214, 127, 0.2);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(41, 214, 127, 0.3);
        }

        .auth-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }

        .auth-footer a {
            color: var(--green);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Wolfstreet77</h1>
        <p class="auth-subtitle">Založ si účet a ovládni trhy</p>

        <form action="register_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div class="form-group">
                <label class="form-label">Uživatelské jméno</label>
                <input type="text" name="username" class="form-input" placeholder="např. JordanB" required>
            </div>

            <div class="form-group">
                <label class="form-label">Emailová adresa</label>
                <input type="email" name="email" class="form-input" placeholder="vas@email.cz" required>
            </div>

            <div class="form-group">
                <label class="form-label">Heslo</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-label">Vyber si svou roli</div>
            <div class="role-selection">
                <label class="role-option">
                    <input type="radio" name="role_type" value="trader" checked>
                    <div class="role-box">
                        <span class="icon">🎩</span>
                        <span class="name">Trader</span>
                    </div>
                </label>
                <label class="role-option">
                    <input type="radio" name="role_type" value="gangster">
                    <div class="role-box">
                        <span class="icon">🔫</span>
                        <span class="name">Gangster</span>
                    </div>
                </label>
                <label class="role-option">
                    <input type="radio" name="role_type" value="pimp">
                    <div class="role-box">
                        <span class="icon">👑</span>
                        <span class="name">Boss</span>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn-register">Vytvořit účet</button>
        </form>

        <div class="auth-footer">
            Už máš účet? <a href="login.php">Přihlas se</a>
        </div>
    </div>
</div>

</body>
</html>