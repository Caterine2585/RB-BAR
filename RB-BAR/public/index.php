<?php

declare(strict_types=1);

$routes = require __DIR__ . '/../routes/routes.php';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$route = $routes[$path] ?? null;

if ($route === null) {
    http_response_code(404);
    exit('Página no encontrada.');
}

require_once __DIR__ . '/../app/controllers/' . $route[0] . '.php';
$controllerClass = $route[0];
$controller = new $controllerClass();
$controller->{$route[1]}();
