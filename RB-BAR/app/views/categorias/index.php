<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Categorías - RB-BAR</title>

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
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0;
        }

        .btn-nuevo {
            display: inline-block;
            padding: 11px 18px;
            background: #222;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .lista {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        .categoria {
            display: grid;
            grid-template-columns: 80px 1fr 150px 180px;
            align-items: center;
            gap: 15px;
            padding: 18px 20px;
            border-bottom: 1px solid #eee;
        }

        .categoria:last-child {
            border-bottom: none;
        }

        .id {
            color: #777;
            font-size: 14px;
        }

        .nombre {
            color: #222;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .nombre:hover {
            text-decoration: underline;
        }

        /*
         * ESTADO CLICKEABLE
         */

        .estado {
            display: inline-block;
            width: fit-content;
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .estado.activo {
            background: #dff3e3;
            color: #218739;
        }

        .estado.activo:hover {
            background: #c9ebd0;
        }

        .estado.inactivo {
            background: #f8dede;
            color: #a62929;
        }

        .estado.inactivo:hover {
            background: #efcaca;
        }

        /*
         * ACCIONES
         */

        .acciones {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 7px;
            background: #e9e9e9;
            color: #222;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
        }

        .btn:hover {
            background: #ddd;
        }

        .btn-eliminar {
            background: #f4d8d8;
            color: #a62929;
        }

        .btn-eliminar:hover {
            background: #ecc4c4;
        }

        .sin-categorias {
            padding: 30px;
            text-align: center;
            color: #777;
        }

        @media (max-width: 750px) {

            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .encabezado {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-nuevo {
                text-align: center;
            }

            .categoria {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 18px;
            }

            .acciones {
                flex-wrap: wrap;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="encabezado">

        <h1>Categorías</h1>

        <a
            href="/admin/categorias/crear"
            class="btn-nuevo"
        >
            + Nueva categoría
        </a>

    </div>


    <div class="lista">

        <?php if (empty($categorias)): ?>

            <div class="sin-categorias">
                No hay categorías registradas.
            </div>

        <?php else: ?>

            <?php foreach ($categorias as $categoria): ?>

                <?php

                /*
                 * Comprobamos el estado actual.
                 */

                $estado = $categoria['activo'] ?? false;

                $estaActiva =
                    $estado === true ||
                    $estado === 1 ||
                    $estado === '1';

                ?>

                <div class="categoria">

                    <!-- ID -->

                    <div class="id">

                        #<?php echo (int) $categoria['id']; ?>

                    </div>


                    <!-- NOMBRE -->

                    <a
                        href="/admin/categorias/editar?id=<?php echo (int) $categoria['id']; ?>"
                        class="nombre"
                    >

                        <?php

                        echo htmlspecialchars(
                            $categoria['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </a>


                    <!-- ESTADO CLICKEABLE -->

                    <div>

                        <a
                            href="/admin/categorias/estado?id=<?php echo (int) $categoria['id']; ?>"
                            class="estado <?php echo $estaActiva ? 'activo' : 'inactivo'; ?>"
                        >

                            <?php if ($estaActiva): ?>

                                Activo

                            <?php else: ?>

                                Inactivo

                            <?php endif; ?>

                        </a>

                    </div>


                    <!-- ACCIONES -->

                    <div class="acciones">

                        <?php if (!$estaActiva): ?>

                            <!--
                                ELIMINAR SOLO APARECE
                                CUANDO LA CATEGORÍA ESTÁ INACTIVA.
                            -->

                            <a
                                href="/admin/categorias/eliminar?id=<?php echo (int) $categoria['id']; ?>"
                                class="btn btn-eliminar"
                                onclick="return confirmarEliminacion('<?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?>');"
                            >
                                Eliminar
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>


<script>

function confirmarEliminacion(nombre) {

    return confirm(
        '¿Seguro que deseas eliminar la categoría "' +
        nombre +
        '"?'
    );

}

</script>

</body>

</html>
