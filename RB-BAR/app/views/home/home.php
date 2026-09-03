<?php require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero" id="inicio">
        <div class="hero__content container">
            <p class="eyebrow">Tu punto de encuentro</p>
            <h1>RB-BAR</h1>
            <p class="hero__lead">Donde empieza la noche.</p>
            <p class="hero__text">Bebidas, comida, música y momentos que merecen repetirse.</p>
            <div class="hero__actions">
                <a class="button button--primary" href="/menu">Ver menú</a>
                <a class="button button--ghost" href="/eventos">Conoce nuestros eventos</a>
            </div>
        </div>
        <a class="hero__scroll" href="#experiencia" aria-label="Ir a la experiencia RB-BAR">Descubre RB-BAR <span>↓</span></a>
    </section>

    <section class="section container" id="experiencia">
        <div class="section-heading">
            <p class="eyebrow">Mucho más que una salida</p>
            <h2>La experiencia RB-BAR</h2>
            <p>Un ambiente para llegar, quedarse y volver a vivir.</p>
        </div>
        <div class="experience-grid">
            <?php foreach ($experiences as $experience): ?>
                <article class="experience-item">
                    <span class="experience-item__icon" aria-hidden="true"><?= htmlspecialchars($experience['icon']) ?></span>
                    <h3><?= htmlspecialchars($experience['title']) ?></h3>
                    <p><?= htmlspecialchars($experience['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section section--surface" id="menu">
        <div class="container">
            <div class="section-heading section-heading--row">
                <div><p class="eyebrow">Elige tu favorito</p><h2>Lo que tenemos para ti</h2></div>
                <a class="text-link" href="/menu">Ver todo el menú <span>→</span></a>
            </div>
            <div class="category-grid">
                <?php foreach ($categories as $index => $category): ?>
                    <article class="category-card">
                        <!-- Espacio visual preparado para una imagen local de public/images/. -->
                        <div class="category-card__visual category-card__visual--<?= $index + 1 ?>"><span><?= htmlspecialchars($category['mark']) ?></span></div>
                        <div class="category-card__body"><h3><?= htmlspecialchars($category['name']) ?></h3><p><?= htmlspecialchars($category['description']) ?></p><a href="/menu">Explorar <span>→</span></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section container" id="planes">
        <div class="plan-panel">
            <div><p class="eyebrow">La noche es tuya</p><h2>¿Qué plan tienes hoy?</h2><p id="plan-recommendation" class="plan-recommendation" aria-live="polite">Elige un plan y encontramos el ambiente perfecto.</p></div>
            <div class="plan-options" role="group" aria-label="Selecciona tu plan">
                <button type="button" data-plan="Cita" data-message="Una noche tranquila, buenos cócteles y el ambiente perfecto.">Cita</button>
                <button type="button" data-plan="Amigos" data-message="Comparte algo para tomar, comer y pasarla bien.">Amigos</button>
                <button type="button" data-plan="Fiesta" data-message="Prepárate para una noche con música y energía.">Fiesta</button>
                <button type="button" data-plan="Música" data-message="Buen ambiente, buena música y algo para brindar.">Música</button>
                <button type="button" data-plan="Relax" data-message="Desconecta, toma algo y disfruta el momento.">Relax</button>
            </div>
        </div>
    </section>

    <section class="section section--surface" id="eventos">
        <div class="container"><div class="section-heading"><p class="eyebrow">Siempre hay algo por vivir</p><h2>Próximamente</h2></div>
            <div class="spotlight-grid">
                <?php foreach ($spotlights as $index => $spotlight): ?>
                    <article class="spotlight-card spotlight-card--<?= $index + 1 ?>"><div class="spotlight-card__visual"><span><?= htmlspecialchars($spotlight['type']) ?></span></div><div class="spotlight-card__body"><p class="eyebrow"><?= htmlspecialchars($spotlight['meta']) ?></p><h3><?= htmlspecialchars($spotlight['title']) ?></h3><p><?= htmlspecialchars($spotlight['text']) ?></p><a class="text-link" href="<?= htmlspecialchars($spotlight['link']) ?>">Ver <?= strtolower(htmlspecialchars($spotlight['type'])) ?> <span>→</span></a></div></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta"><div class="container"><p class="eyebrow">Haz tu plan</p><h2>¿Listo para vivir la noche?</h2><p>Descubre nuestro menú y encuentra tu próximo favorito.</p><a class="button button--primary" href="/menu">Explorar menú</a></div></section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
