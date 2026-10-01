<?php

declare(strict_types=1);

return [
    '/' => ['HomeController', 'index'],

    '/menu' => ['MenuController', 'index'],

    '/pedido' => ['PedidoController', 'index'],

    '/admin/categorias' => ['CategoriaController', 'index'],

    '/admin/categorias/crear' => ['CategoriaController', 'crear'],

    '/admin/categorias/editar' => ['CategoriaController', 'editar'],

    '/admin/categorias/estado' => ['CategoriaController', 'cambiarEstado'],

    '/admin/categorias/eliminar' => ['CategoriaController', 'eliminar'],

    
    


     '/admin/productos' => ['ProductoController', 'index'],

    '/admin/productos/crear' => ['ProductoController', 'crear'],

    '/admin/productos/editar' => ['ProductoController', 'editar'],

    '/admin/productos/estado' => ['ProductoController', 'cambiarEstado'],

    '/admin/productos/eliminar' => ['ProductoController', 'eliminar'],
];