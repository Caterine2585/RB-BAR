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

    <title>Productos - RB-BAR</title>

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
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
        }

        .descripcion {
            margin: 0;
            color: #666;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #222;
            color: white;
        }

        .tabla-container {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f8f8;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /*
         * NOMBRE DEL PRODUCTO
         */

        .producto-nombre {
            font-weight: bold;
            color: #222;
            text-decoration: none;
            cursor: pointer;
        }

        .producto-nombre:hover {
            text-decoration: underline;
        }

        /*
         * DESCRIPCIÓN
         */

        .producto-descripcion {
            margin-top: 5px;
            color: #777;
            font-size: 13px;

            /*
             * Evita que una descripción larga
             * agrande demasiado la columna.
             */
            max-width: 220px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .precio {
            font-weight: bold;
        }

        /*
         * STOCK
         */

        .stock {
            font-weight: bold;
        }

        .stock-normal {
            color: #217a2b;
        }

        .stock-bajo {
            color: #b26a00;
        }

        .stock-agotado {
            color: #9a2929;
        }

        /*
         * ESTADO
         */

        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .estado:hover {
            opacity: 0.85;
        }

        .activo {
            background: #dff5e1;
            color: #217a2b;
        }

        .inactivo {
            background: #f5dddd;
            color: #9a2929;
        }

        /*
         * ELIMINAR
         *
         * Solo aparece cuando el producto
         * está inactivo.
         */

        .btn-eliminar {
            display: inline-block;
            margin-left: 8px;
            padding: 6px 10px;
            border-radius: 6px;
            background: #f4d8d8;
            color: #9a2929;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-eliminar:hover {
            background: #ecc4c4;
        }

        /*
         * ALERTA STOCK
         */

        .alerta {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #fff4d6;
            color: #7a5700;
        }

        /*
         * SIN PRODUCTOS
         */

        .sin-productos {
            padding: 30px;
            text-align: center;
            color: #666;
        }

        /*
         * RESPONSIVE
         */

        @media (max-width: 600px) {

            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .encabezado {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary {
                text-align: center;
            }

            th,
            td {
                padding: 12px 10px;
                font-size: 14px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <!-- ========================================= -->
    <!-- ENCABEZADO -->
    <!-- ========================================= -->

    <div class="encabezado">

        <div>

            <h1>Productos</h1>

            <p class="descripcion">
                Administra los productos disponibles en RB-BAR.
            </p>

        </div>

        <a
            href="/admin/productos/crear"
            class="btn btn-primary"
        >
            + Nuevo producto
        </a>

    </div>


    <!-- ========================================= -->
    <!-- CONTAR STOCK BAJO -->
    <!-- ========================================= -->

    <?php

    $productosConStockBajo = 0;

    foreach ($productos as $producto) {

        $cantidad = (int) ($producto['cantidad'] ?? 0);

        $stockMinimo = (int) ($producto['stock_minimo'] ?? 0);

        if ($cantidad <= $stockMinimo) {

            $productosConStockBajo++;

        }

    }

    ?>


    <!-- ========================================= -->
    <!-- ALERTA -->
    <!-- ========================================= -->

    <?php if ($productosConStockBajo > 0): ?>

        <div class="alerta">

            ⚠️ Hay

            <strong>
                <?php echo $productosConStockBajo; ?>
            </strong>

            producto(s) con stock bajo.

        </div>

    <?php endif; ?>


    <!-- ========================================= -->
    <!-- TABLA -->
    <!-- ========================================= -->

    <div class="tabla-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Producto</th>

                    <th>Categoría</th>

                    <th>Precio</th>

                    <th>Stock</th>

                    <th>Estado</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($productos)): ?>

                    <tr>

                        <td
                            colspan="6"
                            class="sin-productos"
                        >
                            No hay productos registrados.
                        </td>

                    </tr>

                <?php else: ?>


                    <?php foreach ($productos as $producto): ?>

                        <?php

                        // =====================================
                        // BUSCAR CATEGORÍA
                        // =====================================

                        $categoriaNombre = 'Sin categoría';

                        foreach ($categorias as $categoria) {

                            if (
                                (int) $categoria['id']
                                ===
                                (int) $producto['categoria_id']
                            ) {

                                $categoriaNombre = $categoria['nombre'];

                                break;

                            }

                        }


                        // =====================================
                        // STOCK
                        // =====================================

                        $cantidad = (int) ($producto['cantidad'] ?? 0);

                        $stockMinimo = (int) ($producto['stock_minimo'] ?? 0);


                        if ($cantidad <= 0) {

                            $claseStock = 'stock-agotado';

                            $textoStock = 'AGOTADO';

                        } elseif ($cantidad <= $stockMinimo) {

                            $claseStock = 'stock-bajo';

                            $textoStock = $cantidad . ' - STOCK BAJO';

                        } else {

                            $claseStock = 'stock-normal';

                            $textoStock = $cantidad;

                        }


                        // =====================================
                        // ESTADO
                        // =====================================

                        $estado = $producto['activo'] ?? false;

                        $estaActivo =
                            $estado === true ||
                            $estado === 1 ||
                            $estado === '1';


                        // =====================================
                        // DESCRIPCIÓN
                        // Máximo 20 caracteres
                        // =====================================

                        $descripcionOriginal = $producto['descripcion'] ?? '';

                        $descripcionMostrar = $descripcionOriginal;

                        /*
                         * Usamos una expresión regular para
                         * trabajar correctamente con UTF-8
                         * sin necesitar mb_strlen().
                         */

                        if (
                            preg_match(
                                '/^(.{0,20})/us',
                                $descripcionOriginal,
                                $coincidencia
                            )
                        ) {

                            $descripcionMostrar = $coincidencia[1];

                        }

                        /*
                         * Si la descripción original es más
                         * larga que lo mostrado, agregamos ...
                         */

                        if (
                            strlen($descripcionOriginal)
                            >
                            strlen($descripcionMostrar)
                        ) {

                            $descripcionMostrar .= '...';

                        }

                        ?>


                        <tr>

                            <!-- ================================= -->
                            <!-- ID -->
                            <!-- ================================= -->

                            <td>

                                <?php echo (int) $producto['id']; ?>

                            </td>


                            <!-- ================================= -->
                            <!-- PRODUCTO -->
                            <!-- ================================= -->

                            <td>

                                <a
                                    href="/admin/productos/editar?id=<?php echo (int) $producto['id']; ?>"
                                    class="producto-nombre"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </a>


                                <div class="producto-descripcion">

                                    <?php

                                    echo htmlspecialchars(
                                        $descripcionMostrar,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </div>

                            </td>


                            <!-- ================================= -->
                            <!-- CATEGORÍA -->
                            <!-- ================================= -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $categoriaNombre,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </td>


                            <!-- ================================= -->
                            <!-- PRECIO -->
                            <!-- ================================= -->

                            <td class="precio">

                                $

                                <?php

                                echo number_format(
                                    (float) $producto['precio'],
                                    0,
                                    ',',
                                    '.'
                                );

                                ?>

                            </td>


                            <!-- ================================= -->
                            <!-- STOCK -->
                            <!-- ================================= -->

                            <td>

                                <span
                                    class="stock <?php echo $claseStock; ?>"
                                >

                                    <?php echo $textoStock; ?>

                                </span>


                                <div class="producto-descripcion">

                                    Mínimo:
                                    <?php echo $stockMinimo; ?>

                                </div>

                            </td>


                            <!-- ================================= -->
                            <!-- ESTADO -->
                            <!-- ================================= -->

                            <td>

                                <?php if ($estaActivo): ?>

                                    <!--
                                        ACTIVO

                                        Al hacer clic cambia
                                        directamente a INACTIVO.
                                    -->

                                    <a
                                        href="/admin/productos/estado?id=<?php echo (int) $producto['id']; ?>"
                                        class="estado activo"
                                    >
                                        Activo
                                    </a>

                                <?php else: ?>

                                    <!--
                                        INACTIVO

                                        Al hacer clic cambia
                                        directamente a ACTIVO.
                                    -->

                                    <a
                                        href="/admin/productos/estado?id=<?php echo (int) $producto['id']; ?>"
                                        class="estado inactivo"
                                    >
                                        Inactivo
                                    </a>


                                    <!--
                                        ELIMINAR

                                        Solo aparece cuando
                                        el producto está inactivo.
                                    -->

                                    <a
                                        href="/admin/productos/eliminar?id=<?php echo (int) $producto['id']; ?>"
                                        class="btn-eliminar"
                                        onclick="return confirmarEliminacion('<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>');"
                                    >
                                        Eliminar
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>


                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<!-- ========================================= -->
<!-- JAVASCRIPT -->
<!-- ========================================= -->

<script>

function confirmarEliminacion(nombre) {

    return confirm(
        '¿Seguro que deseas eliminar el producto "' +
        nombre +
        '"?'
    );

}

</script>

</body>

</html>