<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Helpers/SessionHelper.php';

use App\Helpers\SessionHelper;

SessionHelper::start();
SessionHelper::logout();

header('Location: index.php');
exit;
