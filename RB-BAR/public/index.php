<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Permitir que el servidor entregue directamente CSS, JS e imágenes.
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

$staticExtensions = [
    'css', 'js', 'png', 'jpg', 'jpeg', 'webp',
    'gif', 'svg', 'ico', 'woff', 'woff2'
];

if (
    in_array($extension, $staticExtensions, true)
    && is_file(__DIR__ . $path)
) {
    return false;
}

// Enrutar las páginas de RB-BAR.
$routes = require __DIR__ . '/../routes/routes.php';

$route = $routes[$path] ?? null;

if ($route === null) {
    http_response_code(404);
    exit('Página no encontrada.');
}

require_once __DIR__ . '/../app/controllers/' . $route[0] . '.php';

$controllerClass = $route[0];
$controller = new $controllerClass();
$controller->{$route[1]}();
