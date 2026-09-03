<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $pageTitle = 'RB-BAR | Donde empieza la noche';
        $pageScript = 'home.js';
        $experiences = [
            ['icon' => '✦', 'title' => 'Bebidas', 'text' => 'Coctelería, cervezas y sabores para brindar.'],
            ['icon' => '♫', 'title' => 'Música', 'text' => 'El ritmo que transforma cada noche.'],
            ['icon' => '◆', 'title' => 'Eventos', 'text' => 'Planes para celebrar y compartir.'],
            ['icon' => '○', 'title' => 'Comida', 'text' => 'Opciones perfectas para acompañar el momento.'],
        ];

        $categories = [
            ['name' => 'Cócteles', 'description' => 'Clásicos, autor y mezclas de la casa.', 'mark' => 'CO'],
            ['name' => 'Cervezas', 'description' => 'Una selección fría para cada plan.', 'mark' => 'CE'],
            ['name' => 'Licores', 'description' => 'Destilados para tomar sin prisa.', 'mark' => 'LI'],
            ['name' => 'Vinos', 'description' => 'Copas y botellas para brindar.', 'mark' => 'VI'],
            ['name' => 'Sin alcohol', 'description' => 'Sabor, frescura y buena compañía.', 'mark' => 'SA'],
            ['name' => 'Comida', 'description' => 'Para compartir y seguir la noche.', 'mark' => 'CM'],
        ];

        $spotlights = [
            ['type' => 'Eventos', 'title' => 'Noches que se quedan contigo', 'meta' => 'Próximamente', 'text' => 'Conoce las fechas y planes que estamos preparando.', 'link' => '/eventos'],
            ['type' => 'Promociones', 'title' => 'Motivos para brindar', 'meta' => 'Próximamente', 'text' => 'Descubre novedades y favoritos de la casa.', 'link' => '/promociones'],
        ];

        require __DIR__ . '/../views/home/home.php';
    }
}
