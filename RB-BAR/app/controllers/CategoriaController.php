<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Controller.php';

class CategoriaController extends Controller
{
    private string $archivoCategorias;

    public function __construct()
    {
        $this->archivoCategorias = __DIR__ . '/../../storage/categorias.json';
    }

    public function index(): void
    {
        $categorias = $this->leerCategorias();

        $this->view('categorias/index', [
            'categorias' => $categorias,
        ]);
    }

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $activo = ($_POST['activo'] ?? '1') === '1';

            if ($nombre === '') {
                exit('El nombre de la categoría es obligatorio.');
            }

            $categorias = $this->leerCategorias();

            $nuevoId = 1;

            if (!empty($categorias)) {
                $ids = array_column($categorias, 'id');
                $nuevoId = max($ids) + 1;
            }

            $categorias[] = [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'activo' => $activo,
            ];

            $this->guardarCategorias($categorias);

            $this->redirect('/admin/categorias');
        }

        $this->view('categorias/crear');
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('ID de categoría no válido.');
        }

        $categorias = $this->leerCategorias();

        $categoriaEncontrada = null;

        foreach ($categorias as $categoria) {

            if ((int) $categoria['id'] === $id) {

                $categoriaEncontrada = $categoria;

                break;
            }
        }

        if ($categoriaEncontrada === null) {

            http_response_code(404);

            exit('Categoría no encontrada.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = trim($_POST['nombre'] ?? '');
            $activo = ($_POST['activo'] ?? '1') === '1';

            if ($nombre === '') {
                exit('El nombre de la categoría es obligatorio.');
            }

            foreach ($categorias as &$categoria) {

                if ((int) $categoria['id'] === $id) {

                    $categoria['nombre'] = $nombre;
                    $categoria['activo'] = $activo;

                    break;
                }
            }

            unset($categoria);

            $this->guardarCategorias($categorias);

            $this->redirect('/admin/categorias');
        }

        $this->view('categorias/editar', [
            'categoria' => $categoriaEncontrada,
        ]);
    }

    public function cambiarEstado(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('ID de categoría no válido.');
        }

        $categorias = $this->leerCategorias();

        $categoriaEncontrada = false;

        foreach ($categorias as &$categoria) {

            if ((int) $categoria['id'] === $id) {

                $categoria['activo'] = !$categoria['activo'];

                $categoriaEncontrada = true;

                break;
            }
        }

        unset($categoria);

        if (!$categoriaEncontrada) {

            http_response_code(404);

            exit('Categoría no encontrada.');
        }

        $this->guardarCategorias($categorias);

        $this->redirect('/admin/categorias');
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('ID de categoría no válido.');
        }

        $categorias = $this->leerCategorias();

        $categoriaEncontrada = false;

        $categoriasFiltradas = [];

        foreach ($categorias as $categoria) {

            if ((int) $categoria['id'] === $id) {

                $categoriaEncontrada = true;

                continue;
            }

            $categoriasFiltradas[] = $categoria;
        }

        if (!$categoriaEncontrada) {

            http_response_code(404);

            exit('Categoría no encontrada.');
        }

        $this->guardarCategorias($categoriasFiltradas);

        $this->redirect('/admin/categorias');
    }

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

    private function guardarCategorias(array $categorias): void
    {
        $contenido = json_encode(
            $categorias,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if ($contenido === false) {
            exit('No se pudieron convertir las categorías a JSON.');
        }

        $resultado = file_put_contents(
            $this->archivoCategorias,
            $contenido
        );

        if ($resultado === false) {
            exit('No se pudieron guardar las categorías.');
        }
    }
}