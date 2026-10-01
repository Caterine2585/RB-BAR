<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="pedido-page">

    <section class="menu-intro">
        <div class="container">

            <p class="eyebrow">Área del empleado</p>

            <h1>Mesas y pedidos</h1>

            <p>
                Administra las mesas, reservas y pedidos del bar.
            </p>

        </div>
    </section>


    <div class="container order-layout">

        <!-- =========================================
             MESAS
        ========================================== -->

        <section class="order-form">

            <h2>Mesas</h2>

            <p class="order-help">
                Selecciona una mesa para crear un pedido,
                consultar un pedido existente o gestionar una reserva.
            </p>


            <div id="table-options">

                <?php foreach ($tables as $table): ?>

                    <?php

                    $estado = strtoupper(
                        $table['status']
                    );

                    $claseEstado = match ($estado) {

                        'OCUPADA' =>
                            'is-occupied',

                        'RESERVADA' =>
                            'is-reserved',

                        'INACTIVA' =>
                            'is-inactive',

                        default =>
                            '',
                    };

                    $puedeSeleccionarse =
                        $estado !== 'INACTIVA';

                    ?>

                    <div
                        class="table-option <?= $claseEstado ?>"
                        data-table="<?= $table['number'] ?>"
                        data-capacity="<?= $table['capacity'] ?>"
                        data-status="<?= htmlspecialchars($estado) ?>"
                    >

                        <div class="table-option__header">

                            <div>

                                <span class="table-option__number">
                                    Mesa <?= $table['number'] ?>
                                </span>

                                <small>
                                    <?= $table['capacity'] ?>
                                    personas
                                </small>

                            </div>


                            <span class="table-status">
                                <?= htmlspecialchars($estado) ?>
                            </span>

                        </div>


                        <div
                            class="table-option__actions"
                            data-table-actions
                        >

                            <?php if ($estado === 'DISPONIBLE'): ?>

                                <button
                                    type="button"
                                    class="button button--primary button--small"
                                    data-action="create-order"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Crear pedido
                                </button>


                                <button
                                    type="button"
                                    class="button button--secondary button--small"
                                    data-action="reserve"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Reservar mesa
                                </button>


                            <?php elseif ($estado === 'OCUPADA'): ?>

                                <button
                                    type="button"
                                    class="button button--primary button--small"
                                    data-action="view-order"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Ver pedido
                                </button>


                                <button
                                    type="button"
                                    class="button button--secondary button--small"
                                    data-action="add-products"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Agregar productos
                                </button>


                            <?php elseif ($estado === 'RESERVADA'): ?>

                                <button
                                    type="button"
                                    class="button button--primary button--small"
                                    data-action="reservation"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Ver reserva
                                </button>


                                <button
                                    type="button"
                                    class="button button--secondary button--small"
                                    data-action="client-arrived"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Cliente llegó
                                </button>


                                <button
                                    type="button"
                                    class="button button--danger button--small"
                                    data-action="cancel-reservation"
                                    data-table="<?= $table['number'] ?>"
                                >
                                    Cancelar reserva
                                </button>


                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <p
                class="order-message"
                id="order-message"
                aria-live="polite"
            ></p>

        </section>


        <!-- =========================================
             RESUMEN DEL PEDIDO
        ========================================== -->

        <aside class="order-summary">

            <h2>Resumen del pedido</h2>


            <p
                class="selected-table"
                id="selected-table"
            >
                Ninguna mesa seleccionada
            </p>


            <div data-order-items>

                <p class="cart-empty">
                    Selecciona una mesa para comenzar.
                </p>

            </div>


            <p class="order-count">

                <span data-order-count>
                    0
                </span>

                productos

            </p>


            <div class="cart-total">

                <span>
                    Total
                </span>

                <strong data-order-total>
                    $0
                </strong>

            </div>


            <button
                class="button button--primary"
                type="button"
                id="confirm-order"
                style="display:none;"
            >
                Confirmar pedido
            </button>


            <button
                class="button button--secondary"
                type="button"
                id="close-order"
                style="display:none;"
            >
                Cerrar pedido
            </button>

        </aside>

    </div>


    <!-- =========================================
         MODAL RESERVA
    ========================================== -->

    <div
        id="reservation-modal"
        class="payment-modal"
        style="display:none;"
    >

        <div class="payment-modal__content">

            <h2>Reservar mesa</h2>

            <p id="reservation-table">
                Mesa
            </p>


            <label>
                Nombre del cliente

                <input
                    type="text"
                    id="reservation-name"
                    placeholder="Ej. María"
                >
            </label>


            <label>
                Fecha

                <input
                    type="date"
                    id="reservation-date"
                >
            </label>


            <label>
                Hora

                <input
                    type="time"
                    id="reservation-time"
                >
            </label>


            <label>
                Número de personas

                <input
                    type="number"
                    id="reservation-people"
                    min="1"
                    value="1"
                >
            </label>


            <label>
                Observaciones

                <textarea
                    id="reservation-notes"
                    rows="3"
                    placeholder="Ej. Mesa cerca de la ventana"
                ></textarea>

            </label>


            <div class="payment-actions">

                <button
                    type="button"
                    class="button button--primary"
                    id="confirm-reservation"
                >
                    Confirmar reserva
                </button>


                <button
                    type="button"
                    class="button button--secondary"
                    id="cancel-reservation-modal"
                >
                    Cancelar
                </button>

            </div>

        </div>

    </div>


    <!-- =========================================
         MODAL COBRO
    ========================================== -->

    <div
        id="payment-modal"
        class="payment-modal"
        style="display:none;"
    >

        <div class="payment-modal__content">

            <h2>Cerrar pedido</h2>

            <p id="payment-table">
                Mesa
            </p>


            <div class="payment-total">

                <span>
                    Total a pagar
                </span>

                <strong id="payment-total">
                    $0
                </strong>

            </div>


            <div class="payment-actions">

                <button
                    type="button"
                    class="button button--primary"
                    id="confirm-payment"
                >
                    Confirmar pago
                </button>


                <button
                    type="button"
                    class="button button--secondary"
                    id="cancel-payment"
                >
                    Cancelar
                </button>

            </div>

        </div>

    </div>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>