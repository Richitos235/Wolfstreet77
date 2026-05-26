<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Helpers/SessionHelper.php';
require_once __DIR__ . '/app/Config/Database.php';
require_once __DIR__ . '/app/Services/Service.php';
require_once __DIR__ . '/app/Services/AuthService.php';

use App\Helpers\SessionHelper;
use App\Services\AuthService;

SessionHelper::start();

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
                header('Location: home.php');
                exit;
            } else {
                $error = $result['error'];
            }
        } catch (\Exception $e) {
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css" />
    <link rel="stylesheet" href="/assets/css/components.css" />
    <link rel="stylesheet" href="/assets/css/layout.css" />
</head>
<body class="auth-layout">
    <div class="auth-card glass-panel glass-panel--heavy">
        <div style="text-align: center; margin-bottom: var(--space-lg);">
            <h1 style="font-size: 32px; color: var(--accent-blue); margin-bottom: 4px;">Wolfstreet77</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Vítej zpět, obchodníku.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success === 'registered'): ?>
            <div class="alert alert-success">Registrace proběhla úspěšně! Můžeš se přihlásit.</div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Uživatelské jméno / Email</label>
                <input type="text" name="username" class="input-field" required value="<?php echo htmlspecialchars($username ?? ''); ?>" placeholder="Zadejte své údaje">
            </div>
            <div class="form-group">
                <label>Heslo</label>
                <input type="password" name="password" class="input-field" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Vstoupit do hry</button>
        </form>
        
        <div style="text-align: center; margin-top: var(--space-md); font-size: 14px; color: var(--text-muted);">
            Nemáš ještě účet? <a href="register.php" style="color: var(--accent-blue); font-weight: 600;">Založit novou identitu</a>
        </div>
    </div>
</body>
</html>