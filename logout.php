<?php

declare(strict_types=1);

// Načtení helperu pro práci se session
require_once __DIR__ . '/app/Helpers/SessionHelper.php';

use App\Helpers\SessionHelper;

// Inicializace session (aby bylo co ničit)
SessionHelper::start();

/**
 * Odhlášení uživatele
 * Metoda destroy by měla smazat $_SESSION data a ukončit relaci
 */
SessionHelper::destroy();

// Přesměrování na úvodní stránku nebo login s potvrzením
header('Location: index.php?logout=success');
exit;