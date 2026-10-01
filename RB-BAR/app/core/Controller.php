<?php

declare(strict_types=1);

abstract class Controller
{
    /**
     * Carga una vista.
     *
     * @param string $view Ruta de la vista dentro de app/views
     * @param array<string, mixed> $data Datos que recibirá la vista
     */
    protected function view(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            exit('Vista no encontrada: ' . $view);
        }

        extract($data);

        require $viewPath;
    }

    /**
     * Redirige a una ruta de la aplicación.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}