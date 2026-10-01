<?php

$id = $categoria['id'] ?? '';
$nombre = $categoria['nombre'] ?? '';
$activo = $categoria['activo'] ?? true;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar categoría - RB-BAR</title>

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
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
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

        <h1>Editar categoría</h1>

        <p>
            Modifica la información de esta categoría.
        </p>

        <form
            method="POST"
            action="/admin/categorias/editar?id=<?php echo $id; ?>"
        >

            <div class="campo">

                <label for="nombre">
                    Nombre de la categoría
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>"
                    required
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

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar cambios
                </button>

                <a
                    href="/admin/categorias"
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