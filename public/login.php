<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Helpers/SessionHelper.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Services/Service.php';
require_once __DIR__ . '/../app/Services/AuthService.php';

use App\Helpers\SessionHelper;
use App\Services\AuthService;

SessionHelper::start();

if (SessionHelper::isAuthenticated()) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Uživatelské jméno a heslo jsou povinné';
    } else {
        $authService = new AuthService();
        $result = $authService->login($username, $password);

        if ($result['success']) {
            header('Location: home.php');
            exit;
        } else {
            $error = $result['error'] ?? 'Chyba při přihlášení';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Přihlášení - Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/pages/auth.css" />
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">Wolfstreet77</h1>
            <p class="auth-subtitle">Přihlaš se do hry</p>

            <?php if ($error): ?>
                <div class="alert alert--error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Uživatelské jméno nebo email</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" required />
                </div>

                <button type="submit" class="btn btn--primary btn--block">Přihlásit se</button>
            </form>

            <p class="auth-footer">
                Nemáš ještě účet? <a href="register.php">Zaregistruj se</a>
            </p>
        </div>
    </div>
</body>
</html>
