<?php
declare(strict_types=1);

// OPRAVA CEST: Protože máš vše v htdocs, nepoužíváme /../
require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/AuthService.php';

use App\Helpers\SessionHelper;
use App\Services\AuthService;

SessionHelper::start();

// Pokud už je přihlášen, šup na home
if (SessionHelper::isAuthenticated()) {
    header('Location: home.php');
    exit;
}

$error = '';
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Zadejte jméno a heslo.';
    } else {
        try {
            $authService = new AuthService();
            $result = $authService->login($username, $password);

            if ($result['success']) {
                SessionHelper::regenerate();
                header('Location: home.php');
                exit;
            } else {
                $error = $result['error'];
            }
        } catch (\Exception $e) {
            // Tohle ti vypíše skutečnou chybu, pokud databáze selže
            $error = "Chyba serveru: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wolfstreet77 | Login</title>
    
    <link rel="stylesheet" href="assets/css/base/reset.css" />
    <link rel="stylesheet" href="assets/css/pages/dashboard.css" />
    
    <style>
        body {
            background: radial-gradient(circle at center, #11314d 0%, #071018 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: sans-serif;
        }
        .auth-card {
            background: rgba(16, 27, 43, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid #1f3148;
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .auth-title { text-align: center; color: #59ff9a; font-size: 36px; margin-bottom: 10px; }
        .auth-subtitle { text-align: center; color: #8ea3b7; margin-bottom: 30px; }
        .alert { padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .alert--error { background: rgba(255, 94, 115, 0.2); color: #ff5e73; border: 1px solid #ff5e73; }
        .alert--success { background: rgba(89, 255, 154, 0.2); color: #59ff9a; border: 1px solid #59ff9a; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; color: #f3f7ff; margin-bottom: 8px; font-weight: bold; font-size: 13px; text-transform: uppercase; }
        .form-input { width: 100%; background: #071018; border: 1px solid #1f3148; padding: 14px; border-radius: 12px; color: white; box-sizing: border-box; }
        .btn-login { width: 100%; background: #29d67f; color: white; border: none; padding: 16px; border-radius: 12px; font-weight: bold; cursor: pointer; text-transform: uppercase; margin-top: 10px; }
        .auth-footer { margin-top: 25px; text-align: center; color: #8ea3b7; }
        .auth-footer a { color: #59ff9a; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1 class="auth-title">Wolfstreet77</h1>
        <p class="auth-subtitle">Vítej zpět, obchodníku.</p>

        <?php if ($error): ?>
            <div class="alert alert--error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success === 'registered'): ?>
            <div class="alert alert--success">Registrace proběhla úspěšně! Můžeš se přihlásit.</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Uživatelské jméno / Email</label>
                <input type="text" name="username" class="form-input" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Heslo</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn-login">Vstoupit do hry</button>
        </form>
        <div class="auth-footer">
            Nemáš ještě účet? <a href="register.php">Založit novou identitu</a>
        </div>
    </div>
</body>
</html>