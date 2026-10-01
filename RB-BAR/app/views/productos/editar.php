<?php

$id = $producto['id'] ?? '';
$nombre = $producto['nombre'] ?? '';
$descripcion = $producto['descripcion'] ?? '';
$categoriaId = $producto['categoria_id'] ?? '';
$precio = $producto['precio'] ?? '';
$cantidad = $producto['cantidad'] ?? '';
$stockMinimo = $producto['stock_minimo'] ?? '';
$activo = $producto['activo'] ?? true;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar producto - RB-BAR</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        h1 {
            margin-top: 0;
        }

        .campo {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-primary {
            background: #222;
            color: white;
        }

        .btn-secondary {
            background: #ddd;
            color: #222;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>Editar producto</h1>

        <p>
            Modifica la información de este producto.
        </p>

        <form
            method="POST"
            action="/admin/productos/editar?id=<?php echo (int) $id; ?>"
        >

            <!-- NOMBRE -->

            <div class="campo">

                <label for="nombre">
                    Nombre del producto
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                    required
                >

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="campo">

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                ><?php echo htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'); ?></textarea>

            </div>


            <!-- CATEGORÍA -->

            <div class="campo">

                <label for="categoria_id">
                    Categoría
                </label>

                <select
                    id="categoria_id"
                    name="categoria_id"
                    required
                >

                    <option value="">
                        Selecciona una categoría
                    </option>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?php echo (int) $categoria['id']; ?>"
                            <?php echo ((int) $categoria['id'] === (int) $categoriaId) ? 'selected' : ''; ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $categoria['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- PRECIO -->

            <div class="campo">

                <label for="precio">
                    Precio
                </label>

                <input
                    type="number"
                    id="precio"
                    name="precio"
                    value="<?php echo htmlspecialchars((string) $precio, ENT_QUOTES, 'UTF-8'); ?>"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <!-- CANTIDAD -->

            <div class="campo">

                <label for="cantidad">
                    Cantidad en stock
                </label>

                <input
                    type="number"
                    id="cantidad"
                    name="cantidad"
                    value="<?php echo htmlspecialchars((string) $cantidad, ENT_QUOTES, 'UTF-8'); ?>"
                    min="0"
                    required
                >

            </div>


            <!-- STOCK MÍNIMO -->

            <div class="campo">

                <label for="stock_minimo">
                    Stock mínimo
                </label>

                <input
                    type="number"
                    id="stock_minimo"
                    name="stock_minimo"
                    value="<?php echo htmlspecialchars((string) $stockMinimo, ENT_QUOTES, 'UTF-8'); ?>"
                    min="0"
                    required
                >

            </div>


            <!-- ESTADO -->

            <div class="campo">

                <label for="activo">
                    Estado
                </label>

                <select
                    id="activo"
                    name="activo"
                >

                    <option
                        value="1"
                        <?php echo $activo ? 'selected' : ''; ?>
                    >
                        Activo
                    </option>

                    <option
                        value="0"
                        <?php echo !$activo ? 'selected' : ''; ?>
                    >
                        Inactivo
                    </option>

                </select>

            </div>


            <!-- BOTONES -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar cambios
                </button>

                <a
                    href="/admin/productos"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

</body>

</html>