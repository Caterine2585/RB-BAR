<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class ProductoController extends Controller
{
    private string $archivoProductos;
    private string $archivoCategorias;

    public function __construct()
    {
        $this->archivoProductos = __DIR__ . '/../../storage/productos.json';
        $this->archivoCategorias = __DIR__ . '/../../storage/categorias.json';
    }

    // =========================================================
    // LISTAR PRODUCTOS
    // =========================================================

    public function index(): void
    {
        $productos = $this->leerProductos();
        $categorias = $this->leerCategorias();

        $this->view('productos/index', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    // =========================================================
    // CREAR PRODUCTO
    // =========================================================

    public function crear(): void
    {
        $categorias = $this->leerCategorias();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
            $precio = (float) ($_POST['precio'] ?? 0);
            $cantidad = (int) ($_POST['cantidad'] ?? 0);
            $stockMinimo = (int) ($_POST['stock_minimo'] ?? 0);
            $activo = ($_POST['activo'] ?? '1') === '1';

            // -------------------------
            // VALIDACIONES
            // -------------------------

            if ($nombre === '') {
                exit('El nombre del producto es obligatorio.');
            }

            if ($categoriaId <= 0) {
                exit('Debes seleccionar una categoría.');
            }

            if ($precio < 0) {
                exit('El precio no puede ser negativo.');
            }

            if ($cantidad < 0) {
                exit('La cantidad no puede ser negativa.');
            }

            if ($stockMinimo < 0) {
                exit('El stock mínimo no puede ser negativo.');
            }

            // -------------------------
            // VALIDAR CATEGORÍA
            // -------------------------

            $categoriaExiste = false;

            foreach ($categorias as $categoria) {

                if ((int) $categoria['id'] === $categoriaId) {

                    $categoriaExiste = true;

                    break;
                }
            }

            if (!$categoriaExiste) {
                exit('La categoría seleccionada no existe.');
            }

            // -------------------------
            // OBTENER PRODUCTOS
            // -------------------------

            $productos = $this->leerProductos();

            // -------------------------
            // GENERAR ID
            // -------------------------

            $nuevoId = 1;

            if (!empty($productos)) {

                $ids = array_column($productos, 'id');

                $nuevoId = max($ids) + 1;
            }

            // -------------------------
            // CREAR PRODUCTO
            // -------------------------

            $productos[] = [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'categoria_id' => $categoriaId,
                'precio' => $precio,
                'cantidad' => $cantidad,
                'stock_minimo' => $stockMinimo,
                'activo' => $activo,
            ];

            // -------------------------
            // GUARDAR
            // -------------------------

            $this->guardarProductos($productos);

            $this->redirect('/admin/productos');
        }

        $this->view('productos/crear', [
            'categorias' => $categorias,
        ]);
    }

    // =========================================================
    // EDITAR PRODUCTO
    // =========================================================

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('Producto no válido.');
        }

        $productos = $this->leerProductos();
        $categorias = $this->leerCategorias();

        $productoEncontrado = null;

        foreach ($productos as $producto) {

            if ((int) $producto['id'] === $id) {

                $productoEncontrado = $producto;

                break;
            }
        }

        if ($productoEncontrado === null) {
            exit('El producto no existe.');
        }

        // =====================================================
        // GUARDAR CAMBIOS
        // =====================================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
            $precio = (float) ($_POST['precio'] ?? 0);
            $cantidad = (int) ($_POST['cantidad'] ?? 0);
            $stockMinimo = (int) ($_POST['stock_minimo'] ?? 0);

            // -------------------------
            // VALIDACIONES
            // -------------------------

            if ($nombre === '') {
                exit('El nombre del producto es obligatorio.');
            }

            if ($categoriaId <= 0) {
                exit('Debes seleccionar una categoría.');
            }

            if ($precio < 0) {
                exit('El precio no puede ser negativo.');
            }

            if ($cantidad < 0) {
                exit('La cantidad no puede ser negativa.');
            }

            if ($stockMinimo < 0) {
                exit('El stock mínimo no puede ser negativo.');
            }

            // -------------------------
            // VALIDAR CATEGORÍA
            // -------------------------

            $categoriaExiste = false;

            foreach ($categorias as $categoria) {

                if ((int) $categoria['id'] === $categoriaId) {

                    $categoriaExiste = true;

                    break;
                }
            }

            if (!$categoriaExiste) {
                exit('La categoría seleccionada no existe.');
            }

            // -------------------------
            // ACTUALIZAR PRODUCTO
            // -------------------------

            foreach ($productos as &$producto) {

                if ((int) $producto['id'] === $id) {

                    $producto['nombre'] = $nombre;
                    $producto['descripcion'] = $descripcion;
                    $producto['categoria_id'] = $categoriaId;
                    $producto['precio'] = $precio;
                    $producto['cantidad'] = $cantidad;
                    $producto['stock_minimo'] = $stockMinimo;

                    break;
                }
            }

            unset($producto);

            // -------------------------
            // GUARDAR CAMBIOS
            // -------------------------

            $this->guardarProductos($productos);

            $this->redirect('/admin/productos');
        }

        // =====================================================
        // MOSTRAR FORMULARIO
        // =====================================================

        $this->view('productos/editar', [
            'producto' => $productoEncontrado,
            'categorias' => $categorias,
        ]);
    }

    // =========================================================
    // CAMBIAR ESTADO
    // =========================================================

    public function cambiarEstado(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('Producto no válido.');
        }

        $productos = $this->leerProductos();

        $productoEncontrado = false;

        foreach ($productos as &$producto) {

            if ((int) $producto['id'] === $id) {

                $estadoActual = $producto['activo'] ?? false;

                // Cambiar:
                // Activo -> Inactivo
                // Inactivo -> Activo

                if (
                    $estadoActual === true ||
                    $estadoActual === 1 ||
                    $estadoActual === '1'
                ) {
                    $producto['activo'] = false;
                } else {
                    $producto['activo'] = true;
                }

                $productoEncontrado = true;

                break;
            }
        }

        unset($producto);

        if (!$productoEncontrado) {
            exit('El producto no existe.');
        }

        $this->guardarProductos($productos);

        // Volver a la lista de productos

        $this->redirect('/admin/productos');
    }

    // =========================================================
    // ELIMINAR PRODUCTO
    // =========================================================

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('Producto no válido.');
        }

        $productos = $this->leerProductos();

        $productoEncontrado = false;

        foreach ($productos as $producto) {

            if ((int) $producto['id'] === $id) {

                $productoEncontrado = true;

                // Solo se puede eliminar si está INACTIVO

                $estado = $producto['activo'] ?? false;

                if (
                    $estado === true ||
                    $estado === 1 ||
                    $estado === '1'
                ) {
                    exit('No puedes eliminar un producto activo.');
                }

                break;
            }
        }

        if (!$productoEncontrado) {
            exit('El producto no existe.');
        }

        // -------------------------
        // ELIMINAR
        // -------------------------

        $productos = array_values(
            array_filter(
                $productos,
                function ($producto) use ($id) {
                    return (int) $producto['id'] !== $id;
                }
            )
        );

        $this->guardarProductos($productos);

        $this->redirect('/admin/productos');
    }

    // =========================================================
    // LEER PRODUCTOS
    // =========================================================

    private function leerProductos(): array
    {
        if (!file_exists($this->archivoProductos)) {
            return [];
        }

        $contenido = file_get_contents($this->archivoProductos);

        if ($contenido === false || trim($contenido) === '') {
            return [];
        }

        $productos = json_decode($contenido, true);

        if (!is_array($productos)) {
            return [];
        }

        return $productos;
    }

    // =========================================================
    // GUARDAR PRODUCTOS
    // =========================================================

    private function guardarProductos(array $productos): void
    {
        $contenido = json_encode(
            $productos,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if ($contenido === false) {
            exit('No se pudieron convertir los productos a JSON.');
        }

        $resultado = file_put_contents(
            $this->archivoProductos,
            $contenido
        );

        if ($resultado === false) {
            exit('No se pudieron guardar los productos.');
        }
    }

    // =========================================================
    // LEER CATEGORÍAS
    // =========================================================

    private function leerCategorias(): array
    {
        if (!file_exists($this->archivoCategorias)) {
            return [];
        }

        $contenido = file_get_contents($this->archivoCategorias);

        if ($contenido === false || trim($contenido) === '') {
            return [];
        }

        $categorias = json_decode($contenido, true);

        if (!is_array($categorias)) {
            return [];
        }

        return $categorias;
    }
}