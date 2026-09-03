<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="RB-BAR: bebidas, música, eventos y buena compañía.">
    <title><?= htmlspecialchars($pageTitle ?? 'RB-BAR') ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/menu.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar container" aria-label="Navegación principal">
        <a class="brand" href="/" aria-label="RB-BAR, inicio">RB<span>—</span>BAR</a>
        <button class="nav-toggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="main-nav"><span></span><span></span><span></span></button>
        <div class="nav-links" id="main-nav"><a href="/">Inicio</a><a href="/menu">Menú</a><a href="/eventos">Eventos</a><a href="/promociones">Promociones</a><a href="#contacto">Contacto</a><a class="nav-cart" href="/pedido">Carrito <span data-cart-count>0</span></a><a class="nav-order" href="/menu">Ver menú</a></div>
    </nav>
</header>
