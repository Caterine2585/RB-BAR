<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Nuevo producto - RB-BAR</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .formulario {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        h1 {
            margin-top: 0;
        }

        .descripcion {
            color: #666;
            margin-bottom: 25px;
        }

        .campo {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #222;
            color: white;
        }

        .btn-cancelar {
            background: #ddd;
            color: #222;
        }

        @media (max-width: 600px) {

            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .formulario {
                padding: 20px;
            }

            .fila {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .acciones {
                flex-direction: column;
            }

            .acciones .btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="formulario">

        <h1>Nuevo producto</h1>

        <p class="descripcion">
            Registra un nuevo producto para RB-BAR.
        </p>

        <form
            method="POST"
            action="/admin/productos/crear"
        >

            <div class="campo">

                <label for="nombre">
                    Nombre del producto
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    required
                    maxlength="150"
                    placeholder="Ejemplo: Mojito"
                >

            </div>

            <div class="campo">

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    placeholder="Describe el producto..."
                ></textarea>

            </div>

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

                        <?php if (!empty($categoria['activo'])): ?>

                            <option
                                value="<?php echo $categoria['id']; ?>"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $categoria['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </option>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="fila">

                <div class="campo">

                    <label for="precio">
                        Precio
                    </label>

                    <input
                        type="number"
                        id="precio"
                        name="precio"
                        min="0"
                        step="1"
                        required
                        placeholder="18000"
                    >

                </div>

                <div class="campo">

                    <label for="cantidad">
                        Cantidad inicial
                    </label>

                    <input
                        type="number"
                        id="cantidad"
                        name="cantidad"
                        min="0"
                        step="1"
                        required
                        value="0"
                    >

                </div>

            </div>

            <div class="campo">

                <label for="stock_minimo">
                    Stock mínimo
                </label>

                <input
                    type="number"
                    id="stock_minimo"
                    name="stock_minimo"
                    min="0"
                    step="1"
                    required
                    value="4"
                >

            </div>

            <div class="campo">

                <label for="activo">
                    Estado
                </label>

                <select
                    id="activo"
                    name="activo"
                >

                    <option value="1">
                        Activo
                    </option>

                    <option value="0">
                        Inactivo
                    </option>

                </select>

            </div>

            <div class="acciones">

                <a
                    href="/admin/productos"
                    class="btn btn-cancelar"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar producto
                </button>

            </div>

        </form>

    </div>

</div>

</body>

</html>