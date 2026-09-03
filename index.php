<?php

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once BASE_PATH . '/controllers/HomeController.php';

$controller = new HomeController();
$controller->index();