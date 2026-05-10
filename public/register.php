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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $roleType = $_POST['role_type'] ?? '';

    if ($password !== $passwordConfirm) {
        $error = 'Hesla se neshodují';
    } elseif (empty($username) || empty($email) || empty($password) || empty($roleType)) {
        $error = 'Všechna pole jsou povinná';
    } else {
        $authService = new AuthService();
        $result = $authService->register($username, $email, $password, $roleType);

        if ($result['success']) {
            header('Location: home.php');
            exit;
        } else {
            $error = $result['error'] ?? 'Neznámá chyba';
        }
    }
}

$roleDescriptions = [
    'trader' => 'Obchodník - zaměřený na nákup a prodej akcií',
    'gangster' => 'Gangster - zaměřený na nebezpečné operace',
    'pimp' => 'Pasák - zaměřený na byznys se sexem',
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrace - Wolfstreet77</title>
    <link rel="stylesheet" href="/assets/css/base/reset.css" />
    <link rel="stylesheet" href="/assets/css/themes/dark.css" />
    <link rel="stylesheet" href="/assets/css/pages/auth.css" />
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">Wolfstreet77</h1>
            <p class="auth-subtitle">Vytvor si nový účet a vstup do hry</p>

            <?php if ($error): ?>
                <div class="alert alert--error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Uživatelské jméno</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="50" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" required minlength="6" />
                </div>

                <div class="form-group">
                    <label for="password_confirm">Potvrzení hesla</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6" />
                </div>

                <div class="form-group">
                    <label>Vyber svou roli</label>
                    <div class="role-selector">
                        <?php foreach ($roleDescriptions as $roleKey => $roleDesc): ?>
                            <div class="role-option">
                                <input type="radio" id="role_<?php echo $roleKey; ?>" name="role_type" 
                                       value="<?php echo $roleKey; ?>" 
                                       <?php echo (($_POST['role_type'] ?? '') === $roleKey) ? 'checked' : ''; ?> />
                                <label for="role_<?php echo $roleKey; ?>" class="role-label">
                                    <span class="role-name"><?php echo htmlspecialchars(ucfirst($roleKey)); ?></span>
                                    <span class="role-desc"><?php echo htmlspecialchars($roleDesc); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary btn--block">Vytvořit účet</button>
            </form>

            <p class="auth-footer">
                Už máš účet? <a href="login.php">Přihlásit se</a>
            </p>
        </div>
    </div>
</body>
</html>
