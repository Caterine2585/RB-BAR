<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class MenuController extends Controller
{
    private string $archivoProductos;
    private string $archivoCategorias;

    public function __construct()
    {
        $this->archivoProductos = __DIR__ . '/../../storage/productos.json';
        $this->archivoCategorias = __DIR__ . '/../../storage/categorias.json';
    }

    public function index(): void
    {
        $pageTitle = 'Menú | RB-BAR';
        $pageScript = 'menu.js';

        $categoriasJson = $this->leerJson($this->archivoCategorias);
        $productosJson = $this->leerJson($this->archivoProductos);

        /*
         * Categorías
         */
        $categories = ['Todos'];

        foreach ($categoriasJson as $categoria) {

            if (
                isset($categoria['nombre']) &&
                ($categoria['activo'] ?? true)
            ) {
                $categories[] = $categoria['nombre'];
            }
        }

        /*
         * Productos
         */
        $products = [];

        foreach ($productosJson as $producto) {

            // Los productos inactivos no aparecen en el menú.
            if (!($producto['activo'] ?? false)) {
                continue;
            }

            $categoriaNombre = 'Sin categoría';

            foreach ($categoriasJson as $categoria) {

                if (
                    (int) ($categoria['id'] ?? 0)
                    ===
                    (int) ($producto['categoria_id'] ?? 0)
                ) {
                    $categoriaNombre = $categoria['nombre'] ?? 'Sin categoría';
                    break;
                }
            }

            $cantidad = (int) ($producto['cantidad'] ?? 0);

            $products[] = [
                'id' => (int) ($producto['id'] ?? 0),
                'category' => $categoriaNombre,
                'name' => $producto['nombre'] ?? '',
                'description' => $producto['descripcion'] ?? '',
                'price' => (float) ($producto['precio'] ?? 0),
                'image' => '',
                'available' => $cantidad > 0,
                'stock' => $cantidad,
            ];
        }

        require __DIR__ . '/../views/menu/menu.php';
    }

    /**
     * Leer un archivo JSON.
     */
    private function leerJson(string $archivo): array
    {
        if (!file_exists($archivo)) {
            return [];
        }

        $contenido = file_get_contents($archivo);

        if ($contenido === false || trim($contenido) === '') {
            return [];
        }

        $datos = json_decode($contenido, true);

        if (!is_array($datos)) {
            return [];
        }

        return $datos;
    }
}