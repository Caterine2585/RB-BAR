<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class PedidoController extends Controller
{
    public function index(): void
    {
        $pageTitle = 'Preparar pedido | RB-BAR';
        $pageScript = 'pedido.js';
        $tables = [
            ['number'=>1,'capacity'=>2,'status'=>'Disponible'], ['number'=>2,'capacity'=>4,'status'=>'Disponible'],
            ['number'=>3,'capacity'=>4,'status'=>'Ocupada'], ['number'=>4,'capacity'=>6,'status'=>'Disponible'],
            ['number'=>5,'capacity'=>8,'status'=>'Reservada'],
        ];
        require __DIR__ . '/../views/pedidos/pedido.php';
    }
}
