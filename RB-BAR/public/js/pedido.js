(() => {
    'use strict';

    /* =========================================================
       CLAVES LOCALSTORAGE
    ========================================================= */

    const CART_KEY = 'rbbar_cart';
    const ORDERS_KEY = 'rbbar_table_orders';
    const RESERVATIONS_KEY = 'rbbar_reservations';
    const HISTORY_KEY = 'rbbar_order_history';


    /* =========================================================
       VARIABLES
    ========================================================= */

    let mesaActual = null;
    let mesaReservaActual = null;
    let modoPedido = null;


    /*
        NUEVO
        AGREGAR
        VER
        EDITAR
    */


    /* =========================================================
       ELEMENTOS
    ========================================================= */

    const message =
        document.querySelector('#order-message');

    const selectedTable =
        document.querySelector('#selected-table');

    const orderItems =
        document.querySelector('[data-order-items]');

    const orderCount =
        document.querySelector('[data-order-count]');

    const orderTotal =
        document.querySelector('[data-order-total]');

    const confirmOrderButton =
        document.querySelector('#confirm-order');

    const closeOrderButton =
        document.querySelector('#close-order');

    const reservationModal =
        document.querySelector('#reservation-modal');

    const paymentModal =
        document.querySelector('#payment-modal');


    /* =========================================================
       MENSAJE
    ========================================================= */

    function mostrarMensaje(texto) {

        if (!message) {
            return;
        }

        message.textContent = texto;

        setTimeout(() => {

            if (message.textContent === texto) {
                message.textContent = '';
            }

        }, 4000);
    }


    /* =========================================================
       DINERO
    ========================================================= */

    function dinero(valor) {

        return new Intl.NumberFormat(
            'es-CO',
            {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0
            }
        ).format(Number(valor) || 0);
    }


    /* =========================================================
       CARRITO
    ========================================================= */

    function obtenerCarrito() {

        try {

            return JSON.parse(
                localStorage.getItem(CART_KEY) || '[]'
            );

        } catch {

            return [];
        }
    }


    function guardarCarrito(carrito) {

        localStorage.setItem(
            CART_KEY,
            JSON.stringify(carrito)
        );
    }


    function limpiarCarrito() {

        guardarCarrito([]);
    }


    /* =========================================================
       PEDIDOS
    ========================================================= */

    function obtenerPedidos() {

        try {

            return JSON.parse(
                localStorage.getItem(ORDERS_KEY) || '{}'
            );

        } catch {

            return {};
        }
    }


    function guardarPedidos(pedidos) {

        localStorage.setItem(
            ORDERS_KEY,
            JSON.stringify(pedidos)
        );
    }


    function obtenerPedidoMesa(numeroMesa) {

        const pedidos = obtenerPedidos();

        return pedidos[String(numeroMesa)] || null;
    }


    /* =========================================================
       RESERVAS
    ========================================================= */

    function obtenerReservas() {

        try {

            return JSON.parse(
                localStorage.getItem(
                    RESERVATIONS_KEY
                ) || '{}'
            );

        } catch {

            return {};
        }
    }


    function guardarReservas(reservas) {

        localStorage.setItem(
            RESERVATIONS_KEY,
            JSON.stringify(reservas)
        );
    }


    function obtenerReservaMesa(numeroMesa) {

        const reservas = obtenerReservas();

        return reservas[String(numeroMesa)] || null;
    }


    /* =========================================================
       HISTORIAL
    ========================================================= */

    function obtenerHistorial() {

        try {

            return JSON.parse(
                localStorage.getItem(
                    HISTORY_KEY
                ) || '[]'
            );

        } catch {

            return [];
        }
    }


    function guardarHistorial(historial) {

        localStorage.setItem(
            HISTORY_KEY,
            JSON.stringify(historial)
        );
    }


    function guardarPedidoEnHistorial(pedido) {

        const historial =
            obtenerHistorial();


        historial.push({

            mesa: pedido.mesa,

            nombreCliente:
                pedido.nombreCliente || '',

            cantidadPersonas:
                pedido.cantidadPersonas || 1,

            productos:
                (pedido.productos || []).map(
                    producto => ({
                        ...producto
                    })
                ),

            total:
                calcularTotal(
                    pedido.productos || []
                ),

            fechaPedido:
                pedido.fecha ||
                new Date().toISOString(),

            fechaCierre:
                new Date().toISOString(),

            motivo: 'PAGO'
        });


        guardarHistorial(historial);
    }


    /* =========================================================
       CÁLCULOS
    ========================================================= */

    function calcularTotal(productos) {

        return productos.reduce(
            (total, producto) => {

                const precio =
                    Number(
                        producto.price ??
                        producto.precio ??
                        0
                    );

                const cantidad =
                    Number(
                        producto.quantity ??
                        producto.cantidad ??
                        0
                    );

                return total +
                    precio * cantidad;

            },
            0
        );
    }


    function calcularCantidad(productos) {

        return productos.reduce(
            (total, producto) => {

                return total +
                    Number(
                        producto.quantity ??
                        producto.cantidad ??
                        0
                    );

            },
            0
        );
    }


    function actualizarTotales(productos) {

        if (orderCount) {

            orderCount.textContent =
                calcularCantidad(productos);
        }


        if (orderTotal) {

            orderTotal.textContent =
                dinero(
                    calcularTotal(productos)
                );
        }
    }


    /* =========================================================
       MOSTRAR PRODUCTOS EDITABLES
    ========================================================= */

    function pintarProductosEditables(
        productos,
        numeroMesa,
        textoBoton
    ) {

        if (selectedTable) {

            selectedTable.textContent =
                `Mesa ${numeroMesa}`;
        }


        if (!productos.length) {

            orderItems.innerHTML = `
                <p class="cart-empty">
                    No hay productos seleccionados.
                </p>
            `;

        } else {

            orderItems.innerHTML =
                productos.map(producto => {

                    const nombre =
                        producto.name ||
                        producto.nombre ||
                        'Producto';

                    const precio =
                        Number(
                            producto.price ??
                            producto.precio ??
                            0
                        );

                    const cantidad =
                        Number(
                            producto.quantity ??
                            producto.cantidad ??
                            0
                        );


                    return `
                        <div class="cart-item">

                            <div>

                                <strong>
                                    ${nombre}
                                </strong>

                                <p>
                                    ${cantidad}
                                    ×
                                    ${dinero(precio)}
                                </p>


                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:5px;
                                        margin-top:6px;
                                    "
                                >

                                    <button
                                        type="button"
                                        data-product-action="decrease"
                                        data-product-id="${producto.id}"
                                        style="
                                            width:26px;
                                            height:26px;
                                            padding:0;
                                            margin:0;
                                            border:1px solid var(--line);
                                            background:transparent;
                                            color:var(--text);
                                            cursor:pointer;
                                        "
                                    >
                                        −
                                    </button>


                                    <span
                                        style="
                                            min-width:25px;
                                            text-align:center;
                                        "
                                    >
                                        ${cantidad}
                                    </span>


                                    <button
                                        type="button"
                                        data-product-action="increase"
                                        data-product-id="${producto.id}"
                                        style="
                                            width:26px;
                                            height:26px;
                                            padding:0;
                                            margin:0;
                                            border:1px solid var(--line);
                                            background:transparent;
                                            color:var(--text);
                                            cursor:pointer;
                                        "
                                    >
                                        +
                                    </button>


                                    <button
                                        type="button"
                                        data-product-action="remove"
                                        data-product-id="${producto.id}"
                                        style="
                                            width:26px;
                                            height:26px;
                                            padding:0;
                                            margin:0;
                                            border:0;
                                            background:transparent;
                                            color:#a94a4a;
                                            cursor:pointer;
                                        "
                                        title="Eliminar producto"
                                    >
                                        ×
                                    </button>

                                </div>

                            </div>


                            <strong>
                                ${dinero(
                                    precio * cantidad
                                )}
                            </strong>

                        </div>
                    `;

                }).join('');
        }


        actualizarTotales(productos);


        if (confirmOrderButton) {

            confirmOrderButton.textContent =
                textoBoton;

            confirmOrderButton.style.display =
                'block';
        }


        /*
         * IMPORTANTE:
         * el botón cerrar SIEMPRE queda
         * disponible cuando existe una mesa
         * con pedido.
         */

        if (
            closeOrderButton &&
            mesaActual
        ) {

            closeOrderButton.style.display =
                'block';
        }
    }


    /* =========================================================
       MOSTRAR PEDIDO
    ========================================================= */

    function mostrarPedido(numeroMesa) {

        const pedido =
            obtenerPedidoMesa(numeroMesa);


        if (!pedido) {

            mostrarMensaje(
                'Esta mesa no tiene un pedido abierto.'
            );

            return;
        }


        mesaActual =
            Number(numeroMesa);

        modoPedido =
            'VER';


        const productos =
            pedido.productos || [];


        if (selectedTable) {

            selectedTable.textContent =
                `Mesa ${numeroMesa}`;
        }


        if (!productos.length) {

            orderItems.innerHTML = `
                <p class="cart-empty">
                    Este pedido todavía no tiene productos.
                </p>
            `;

        } else {

            orderItems.innerHTML =
                productos.map(producto => {

                    const nombre =
                        producto.name ||
                        producto.nombre ||
                        'Producto';

                    const precio =
                        Number(
                            producto.price ??
                            producto.precio ??
                            0
                        );

                    const cantidad =
                        Number(
                            producto.quantity ??
                            producto.cantidad ??
                            0
                        );


                    return `
                        <div class="cart-item">

                            <div>

                                <strong>
                                    ${nombre}
                                </strong>

                                <p>
                                    ${cantidad}
                                    ×
                                    ${dinero(precio)}
                                </p>

                            </div>


                            <strong>
                                ${dinero(
                                    precio * cantidad
                                )}
                            </strong>

                        </div>
                    `;

                }).join('');
        }


        actualizarTotales(productos);


        /*
         * BOTÓN PARA EDITAR
         */

        if (confirmOrderButton) {

            confirmOrderButton.textContent =
                'Editar pedido';

            confirmOrderButton.style.display =
                'block';
        }


        /*
         * BOTÓN PARA CERRAR
         */

        if (closeOrderButton) {

            closeOrderButton.textContent =
                'Cerrar pedido';

            closeOrderButton.style.display =
                'block';
        }
    }


    /* =========================================================
       CREAR PEDIDO
    ========================================================= */

    function seleccionarMesaNueva(numeroMesa) {

        mesaActual =
            Number(numeroMesa);

        modoPedido =
            'NUEVO';


        const carrito =
            obtenerCarrito();


        pintarProductosEditables(
            carrito,
            numeroMesa,
            'Confirmar pedido'
        );


        /*
         * Para una mesa nueva todavía no
         * tiene sentido cerrar.
         */

        if (closeOrderButton) {

            closeOrderButton.style.display =
                'none';
        }


        mostrarMensaje(
            `Mesa ${numeroMesa} seleccionada.`
        );
    }


    /* =========================================================
       AGREGAR PRODUCTOS
    ========================================================= */

    function agregarProductosMesa(numeroMesa) {

        mesaActual =
            Number(numeroMesa);

        modoPedido =
            'AGREGAR';


        const carrito =
            obtenerCarrito();


        if (!carrito.length) {

            if (selectedTable) {

                selectedTable.textContent =
                    `Mesa ${numeroMesa}`;
            }


            orderItems.innerHTML = `
                <p class="cart-empty">
                    No tienes productos adicionales
                    en el carrito.
                </p>
            `;


            actualizarTotales([]);

        } else {

            pintarProductosEditables(
                carrito,
                numeroMesa,
                'Agregar al pedido'
            );
        }


        if (closeOrderButton) {

            closeOrderButton.style.display =
                'block';
        }


        mostrarMensaje(
            `Agrega productos adicionales a la Mesa ${numeroMesa}.`
        );
    }


    /* =========================================================
       EDITAR PEDIDO
    ========================================================= */

    function editarPedido() {

        if (!mesaActual) {
            return;
        }


        const pedido =
            obtenerPedidoMesa(
                mesaActual
            );


        if (!pedido) {

            mostrarMensaje(
                'No existe un pedido para editar.'
            );

            return;
        }


        modoPedido =
            'EDITAR';


        const productos =
            pedido.productos || [];


        pintarProductosEditables(
            productos,
            mesaActual,
            'Guardar cambios'
        );


        /*
         * CERRAR PEDIDO SIGUE DISPONIBLE.
         */

        if (closeOrderButton) {

            closeOrderButton.textContent =
                'Cerrar pedido';

            closeOrderButton.style.display =
                'block';
        }


        mostrarMensaje(
            'Puedes corregir el pedido.'
        );
    }


    /* =========================================================
       MODIFICAR PRODUCTOS
    ========================================================= */

    function modificarProductoEnEdicion(
        productoId,
        accion
    ) {

        if (!mesaActual) {
            return;
        }


        let productos;


        if (
            modoPedido ===
            'EDITAR'
        ) {

            const pedido =
                obtenerPedidoMesa(
                    mesaActual
                );


            if (!pedido) {
                return;
            }


            productos =
                pedido.productos || [];

        } else {

            productos =
                obtenerCarrito();
        }


        const indice =
            productos.findIndex(
                producto =>
                    String(producto.id) ===
                    String(productoId)
            );


        if (indice === -1) {
            return;
        }


        const producto =
            productos[indice];


        let cantidad =
            Number(
                producto.quantity || 0
            );


        /*
         * AUMENTAR
         */

        if (
            accion ===
            'increase'
        ) {

            const stock =
                Number(
                    producto.stock ??
                    Infinity
                );


            if (
                stock !== Infinity &&
                cantidad >= stock
            ) {

                mostrarMensaje(
                    'No hay más unidades disponibles.'
                );

                return;
            }


            producto.quantity =
                cantidad + 1;
        }


        /*
         * DISMINUIR
         */

        if (
            accion ===
            'decrease'
        ) {

            cantidad -= 1;


            if (cantidad <= 0) {

                productos.splice(
                    indice,
                    1
                );

            } else {

                producto.quantity =
                    cantidad;
            }
        }


        /*
         * ELIMINAR COMPLETAMENTE
         */

        if (
            accion ===
            'remove'
        ) {

            productos.splice(
                indice,
                1
            );
        }


        /* =====================================================
           EDITANDO PEDIDO EXISTENTE
        ===================================================== */

        if (
            modoPedido ===
            'EDITAR'
        ) {

            const pedidos =
                obtenerPedidos();


            const clave =
                String(mesaActual);

            if (!pedidos[clave]) {
                return;
            }

            pedidos[clave].productos =
                productos;

            pedidos[clave].total =
                calcularTotal(productos);

            guardarPedidos(pedidos);


            /*
             * SI SE ELIMINARON TODOS LOS PRODUCTOS,
             * LA MESA QUEDA DISPONIBLE.
             */

            if (!productos.length) {

                delete pedidos[clave];

                guardarPedidos(pedidos);

                liberarMesaVisual(
                    mesaActual
                );

                limpiarResumen();

                mostrarMensaje(
                    `Mesa ${mesaActual} disponible porque no tiene productos.`
                );

                return;
            }


            pintarProductosEditables(
                productos,
                mesaActual,
                'Guardar cambios'
            );

            mostrarMensaje(
                'Cambio actualizado.'
            );

            return;
        }


        /* =====================================================
           EDITANDO CARRITO
        ===================================================== */

        guardarCarrito(productos);

        pintarProductosEditables(
            productos,
            mesaActual,
            modoPedido === 'AGREGAR'
                ? 'Agregar al pedido'
                : 'Confirmar pedido'
        );
    }


    /* =========================================================
       GUARDAR CAMBIOS DEL PEDIDO
    ========================================================= */

    function guardarCambiosPedido() {

        if (!mesaActual) {
            return;
        }


        const pedidos =
            obtenerPedidos();

        const clave =
            String(mesaActual);


        if (!pedidos[clave]) {

            mostrarMensaje(
                'No existe el pedido.'
            );

            return;
        }


        const productos =
            pedidos[clave].productos ||
            [];


        if (!productos.length) {

            delete pedidos[clave];

            guardarPedidos(pedidos);

            liberarMesaVisual(
                mesaActual
            );

            limpiarResumen();

            mostrarMensaje(
                `Mesa ${mesaActual} disponible.`
            );

            return;
        }


        pedidos[clave].productos =
            productos;

        pedidos[clave].total =
            calcularTotal(productos);


        guardarPedidos(pedidos);


        modoPedido =
            'VER';


        mostrarPedido(
            mesaActual
        );


        mostrarMensaje(
            'Pedido actualizado correctamente.'
        );
    }


    /* =========================================================
       CONFIRMAR PEDIDO NUEVO
    ========================================================= */

    function confirmarPedidoNuevo() {

        if (!mesaActual) {

            mostrarMensaje(
                'Primero selecciona una mesa.'
            );

            return;
        }


        const carrito =
            obtenerCarrito();


        if (!carrito.length) {

            mostrarMensaje(
                'Agrega al menos un producto.'
            );

            return;
        }


        const pedidoExistente =
            obtenerPedidoMesa(
                mesaActual
            );


        if (pedidoExistente) {

            mostrarMensaje(
                'La mesa ya tiene un pedido abierto.'
            );

            return;
        }


        const productos =
            carrito.map(
                producto => ({
                    ...producto,
                    quantity:
                        Number(
                            producto.quantity || 0
                        )
                })
            );


        const pedido = {

            mesa:
                mesaActual,

            nombreCliente:
                '',

            cantidadPersonas:
                1,

            productos,

            total:
                calcularTotal(productos),

            estado:
                'PENDIENTE',

            fecha:
                new Date().toISOString()
        };


        const pedidos =
            obtenerPedidos();


        pedidos[String(mesaActual)] =
            pedido;


        guardarPedidos(
            pedidos
        );


        /*
         * El carrito general se limpia
         * porque los productos ya fueron
         * asignados a la mesa.
         */

        limpiarCarrito();


        marcarMesaOcupada(
            mesaActual
        );


        modoPedido =
            'VER';


        mostrarPedido(
            mesaActual
        );


        mostrarMensaje(
            `Pedido creado para la Mesa ${mesaActual}.`
        );
    }


    /* =========================================================
       AGREGAR PRODUCTOS A PEDIDO EXISTENTE
    ========================================================= */

    function confirmarAgregarProductos() {

        if (!mesaActual) {
            return;
        }


        const carrito =
            obtenerCarrito();


        if (!carrito.length) {

            mostrarMensaje(
                'No hay productos para agregar.'
            );

            return;
        }


        const pedidos =
            obtenerPedidos();

        const clave =
            String(mesaActual);


        if (!pedidos[clave]) {

            mostrarMensaje(
                'La mesa no tiene un pedido abierto.'
            );

            return;
        }


        const pedido =
            pedidos[clave];


        const productosActuales =
            pedido.productos || [];


        carrito.forEach(
            productoCarrito => {

                const existente =
                    productosActuales.find(
                        producto =>
                            String(producto.id) ===
                            String(productoCarrito.id)
                    );


                if (existente) {

                    existente.quantity =
                        Number(
                            existente.quantity || 0
                        ) +
                        Number(
                            productoCarrito.quantity || 0
                        );

                } else {

                    productosActuales.push({
                        ...productoCarrito
                    });
                }
            }
        );


        pedido.productos =
            productosActuales;

        pedido.total =
            calcularTotal(
                productosActuales
            );


        guardarPedidos(
            pedidos
        );


        /*
         * Los productos ya fueron
         * asignados a la mesa.
         */

        limpiarCarrito();


        marcarMesaOcupada(
            mesaActual
        );


        modoPedido =
            'VER';


        mostrarPedido(
            mesaActual
        );


        mostrarMensaje(
            `Productos agregados a la Mesa ${mesaActual}.`
        );
    }


    /* =========================================================
       ESTADO VISUAL DE MESAS
    ========================================================= */

    function obtenerMesaElemento(numeroMesa) {

        return document.querySelector(
            `.table-option[data-table="${numeroMesa}"]`
        );
    }


    function actualizarEstadoMesa(
        numeroMesa,
        estado
    ) {

        const mesa =
            obtenerMesaElemento(
                numeroMesa
            );


        if (!mesa) {
            return;
        }


        mesa.dataset.status =
            estado;


        const estadoTexto =
            mesa.querySelector(
                '.table-status'
            );


        if (estadoTexto) {

            estadoTexto.textContent =
                estado;
        }


        mesa.classList.remove(
            'is-occupied',
            'is-reserved',
            'is-inactive'
        );


        if (
            estado ===
            'OCUPADA'
        ) {

            mesa.classList.add(
                'is-occupied'
            );

        } else if (
            estado ===
            'RESERVADA'
        ) {

            mesa.classList.add(
                'is-reserved'
            );

        } else if (
            estado ===
            'INACTIVA'
        ) {

            mesa.classList.add(
                'is-inactive'
            );
        }


        actualizarBotonesMesa(
            mesa,
            numeroMesa,
            estado
        );
    }


    function actualizarBotonesMesa(
        mesa,
        numeroMesa,
        estado
    ) {

        const actions =
            mesa.querySelector(
                '[data-table-actions]'
            );


        if (!actions) {
            return;
        }


        /*
         * MESA DISPONIBLE
         */

        if (
            estado ===
            'DISPONIBLE'
        ) {

            actions.innerHTML = `

                <button
                    type="button"
                    class="button button--primary button--small"
                    data-action="create-order"
                    data-table="${numeroMesa}"
                >
                    Crear pedido
                </button>

                <button
                    type="button"
                    class="button button--secondary button--small"
                    data-action="reserve"
                    data-table="${numeroMesa}"
                >
                    Reservar mesa
                </button>

            `;

            return;
        }


        /*
         * MESA OCUPADA
         */

        if (
            estado ===
            'OCUPADA'
        ) {

            actions.innerHTML = `

                <button
                    type="button"
                    class="button button--primary button--small"
                    data-action="view-order"
                    data-table="${numeroMesa}"
                >
                    Ver pedido
                </button>

                <button
                    type="button"
                    class="button button--secondary button--small"
                    data-action="add-products"
                    data-table="${numeroMesa}"
                >
                    Agregar productos
                </button>

            `;

            return;
        }


        /*
         * MESA RESERVADA
         */

        if (
            estado ===
            'RESERVADA'
        ) {

            actions.innerHTML = `

                <button
                    type="button"
                    class="button button--primary button--small"
                    data-action="reservation"
                    data-table="${numeroMesa}"
                >
                    Ver reserva
                </button>

                <button
                    type="button"
                    class="button button--secondary button--small"
                    data-action="client-arrived"
                    data-table="${numeroMesa}"
                >
                    Cliente llegó
                </button>

                <button
                    type="button"
                    class="button button--danger button--small"
                    data-action="cancel-reservation"
                    data-table="${numeroMesa}"
                >
                    Cancelar reserva
                </button>

            `;

            return;
        }


        /*
         * MESA INACTIVA
         */

        if (
            estado ===
            'INACTIVA'
        ) {

            actions.innerHTML = '';

            return;
        }
    }


    function marcarMesaOcupada(
        numeroMesa
    ) {

        actualizarEstadoMesa(
            numeroMesa,
            'OCUPADA'
        );
    }


    function liberarMesaVisual(
        numeroMesa
    ) {

        actualizarEstadoMesa(
            numeroMesa,
            'DISPONIBLE'
        );
    }


    function marcarMesaReservada(
        numeroMesa
    ) {

        actualizarEstadoMesa(
            numeroMesa,
            'RESERVADA'
        );
    }


    /* =========================================================
       SINCRONIZAR MESAS
    ========================================================= */

    function sincronizarMesas() {

        const mesas =
            document.querySelectorAll(
                '.table-option'
            );


        const pedidos =
            obtenerPedidos();

        const reservas =
            obtenerReservas();


        mesas.forEach(mesa => {

            const numeroMesa =
                mesa.dataset.table;

            const estadoInicial =
                (
                    mesa.dataset.status ||
                    ''
                ).toUpperCase();


            /*
             * INACTIVA permanece inactiva.
             */

            if (
                estadoInicial ===
                'INACTIVA'
            ) {

                actualizarEstadoMesa(
                    numeroMesa,
                    'INACTIVA'
                );

                return;
            }


            /*
             * PEDIDO ABIERTO
             */

            if (
                pedidos[numeroMesa] &&
                Array.isArray(
                    pedidos[numeroMesa].productos
                ) &&
                pedidos[numeroMesa].productos.length
            ) {

                actualizarEstadoMesa(
                    numeroMesa,
                    'OCUPADA'
                );

                return;
            }


            /*
             * RESERVA
             */

            if (
                reservas[numeroMesa]
            ) {

                actualizarEstadoMesa(
                    numeroMesa,
                    'RESERVADA'
                );

                return;
            }


            /*
             * SIN PEDIDO Y SIN RESERVA
             */

            actualizarEstadoMesa(
                numeroMesa,
                'DISPONIBLE'
            );

        });
    }


    /* =========================================================
       RESERVACIONES
    ========================================================= */

    function abrirReserva(numeroMesa) {

        mesaReservaActual =
            Number(numeroMesa);


        if (!reservationModal) {
            return;
        }


        const titulo =
            reservationModal.querySelector(
                '#reservation-table'
            );


        if (titulo) {

            titulo.textContent =
                `Mesa ${numeroMesa}`;
        }


        const name =
            reservationModal.querySelector(
                '#reservation-name'
            );

        const date =
            reservationModal.querySelector(
                '#reservation-date'
            );

        const time =
            reservationModal.querySelector(
                '#reservation-time'
            );

        const people =
            reservationModal.querySelector(
                '#reservation-people'
            );

        const notes =
            reservationModal.querySelector(
                '#reservation-notes'
            );


        const reserva =
            obtenerReservaMesa(
                numeroMesa
            );


        if (reserva) {

            if (name) {
                name.value =
                    reserva.nombreCliente || '';
            }

            if (date) {
                date.value =
                    reserva.fecha || '';
            }

            if (time) {
                time.value =
                    reserva.hora || '';
            }

            if (people) {
                people.value =
                    reserva.cantidadPersonas || 1;
            }

            if (notes) {
                notes.value =
                    reserva.observaciones || '';
            }

        } else {

            if (name) {
                name.value = '';
            }

            if (date) {
                date.value = '';
            }

            if (time) {
                time.value = '';
            }

            if (people) {
                people.value = 1;
            }

            if (notes) {
                notes.value = '';
            }
        }


        reservationModal.style.display =
            'flex';
    }


    function cerrarReservaModal() {

        if (!reservationModal) {
            return;
        }

        reservationModal.style.display =
            'none';

        mesaReservaActual =
            null;
    }


    function confirmarReserva() {

        if (!mesaReservaActual) {

            mostrarMensaje(
                'No se seleccionó una mesa.'
            );

            return;
        }


        const name =
            document.querySelector(
                '#reservation-name'
            );

        const date =
            document.querySelector(
                '#reservation-date'
            );

        const time =
            document.querySelector(
                '#reservation-time'
            );

        const people =
            document.querySelector(
                '#reservation-people'
            );

        const notes =
            document.querySelector(
                '#reservation-notes'
            );


        const nombreCliente =
            name
                ? name.value.trim()
                : '';

        const fecha =
            date
                ? date.value
                : '';

        const hora =
            time
                ? time.value
                : '';

        const cantidadPersonas =
            people
                ? Number(people.value)
                : 1;

        const observaciones =
            notes
                ? notes.value.trim()
                : '';


        if (!nombreCliente) {

            mostrarMensaje(
                'Escribe el nombre del cliente.'
            );

            return;
        }


        if (!fecha) {

            mostrarMensaje(
                'Selecciona la fecha.'
            );

            return;
        }


        if (!hora) {

            mostrarMensaje(
                'Selecciona la hora.'
            );

            return;
        }


        if (
            !cantidadPersonas ||
            cantidadPersonas < 1
        ) {

            mostrarMensaje(
                'La cantidad de personas no es válida.'
            );

            return;
        }


        const reservas =
            obtenerReservas();


        reservas[String(mesaReservaActual)] = {

            mesa:
                mesaReservaActual,

            nombreCliente,

            fecha,

            hora,

            cantidadPersonas,

            observaciones,

            estado:
                'PENDIENTE'
        };


        guardarReservas(
            reservas
        );


        marcarMesaReservada(
            mesaReservaActual
        );


        cerrarReservaModal();


        mostrarMensaje(
            `Mesa ${mesaReservaActual} reservada correctamente.`
        );
    }


    /* =========================================================
       VER RESERVA
    ========================================================= */

    function verReserva(numeroMesa) {

        const reserva =
            obtenerReservaMesa(
                numeroMesa
            );


        if (!reserva) {

            mostrarMensaje(
                'No existe una reserva para esta mesa.'
            );

            return;
        }


        mesaReservaActual =
            Number(numeroMesa);


        const fecha =
            reserva.fecha || '';

        const hora =
            reserva.hora || '';

        const nombre =
            reserva.nombreCliente || '';

        const personas =
            reserva.cantidadPersonas || 1;

        const observaciones =
            reserva.observaciones || 'Sin observaciones';


        const mensaje =
            `Reserva Mesa ${numeroMesa}\n\n` +
            `Cliente: ${nombre}\n` +
            `Fecha: ${fecha}\n` +
            `Hora: ${hora}\n` +
            `Personas: ${personas}\n` +
            `Observaciones: ${observaciones}`;


        /*
         * Se utiliza alert para mantener
         * la interfaz sencilla.
         */

        alert(mensaje);
    }


    /* =========================================================
       CLIENTE LLEGÓ
    ========================================================= */

    function clienteLlego(numeroMesa) {

        const reservas =
            obtenerReservas();

        const clave =
            String(numeroMesa);

        const reserva =
            reservas[clave];


        if (!reserva) {

            mostrarMensaje(
                'No existe una reserva para esta mesa.'
            );

            return;
        }


        /*
         * Al llegar el cliente:
         *
         * RESERVADA
         *      ↓
         * OCUPADA
         *
         * Se crea un pedido vacío
         * para poder agregar productos.
         */

        const pedidos =
            obtenerPedidos();


        if (!pedidos[clave]) {

            pedidos[clave] = {

                mesa:
                    Number(numeroMesa),

                nombreCliente:
                    reserva.nombreCliente || '',

                cantidadPersonas:
                    Number(
                        reserva.cantidadPersonas || 1
                    ),

                productos:
                    [],

                total:
                    0,

                estado:
                    'PENDIENTE',

                fecha:
                    new Date().toISOString()
            };

            guardarPedidos(
                pedidos
            );
        }


        reserva.estado =
            'ATENDIDA';


        delete reservas[clave];


        guardarReservas(
            reservas
        );


        marcarMesaOcupada(
            numeroMesa
        );


        /*
         * Dejamos seleccionada la mesa
         * para que el empleado pueda
         * agregar productos.
         */

        mesaActual =
            Number(numeroMesa);

        modoPedido =
            'AGREGAR';


        if (selectedTable) {

            selectedTable.textContent =
                `Mesa ${numeroMesa}`;
        }


        orderItems.innerHTML = `
            <p class="cart-empty">
                Cliente recibido.
                Agrega los productos del pedido.
            </p>
        `;


        actualizarTotales([]);


        if (confirmOrderButton) {

            confirmOrderButton.textContent =
                'Agregar al pedido';

            confirmOrderButton.style.display =
                'block';
        }


        if (closeOrderButton) {

            closeOrderButton.style.display =
                'block';
        }


        mostrarMensaje(
            `El cliente de la Mesa ${numeroMesa} llegó.`
        );
    }


    /* =========================================================
       CANCELAR RESERVA
    ========================================================= */

    function cancelarReserva(numeroMesa) {

        const reservas =
            obtenerReservas();

        const clave =
            String(numeroMesa);


        if (!reservas[clave]) {

            mostrarMensaje(
                'No existe una reserva para esta mesa.'
            );

            return;
        }


        const confirmar =
            window.confirm(
                `¿Cancelar la reserva de la Mesa ${numeroMesa}?`
            );


        if (!confirmar) {
            return;
        }


        reservas[clave].estado =
            'CANCELADA';


        delete reservas[clave];


        guardarReservas(
            reservas
        );


        liberarMesaVisual(
            numeroMesa
        );


        if (
            mesaActual ===
            Number(numeroMesa)
        ) {

            limpiarResumen();
        }


        mostrarMensaje(
            `Reserva de la Mesa ${numeroMesa} cancelada.`
        );
    }


    /* =========================================================
       CERRAR PEDIDO
    ========================================================= */

    function abrirCierrePedido() {

        console.log(
            'ABRIR CIERRE PEDIDO',
            mesaActual
        );


        /*
         * Se obtiene la mesa directamente
         * desde la variable actual.
         */

        const mesa =
            Number(mesaActual);


        if (!mesa) {

            mostrarMensaje(
                'Primero selecciona una mesa.'
            );

            return;
        }


        const pedido =
            obtenerPedidoMesa(
                mesa
            );


        if (!pedido) {

            mostrarMensaje(
                'No existe un pedido abierto para esta mesa.'
            );

            return;
        }


        const productos =
            pedido.productos || [];


        /*
         * Si no quedan productos,
         * la mesa se libera directamente.
         */

        if (!productos.length) {

            const pedidos =
                obtenerPedidos();


            delete pedidos[String(mesa)];


            guardarPedidos(
                pedidos
            );


            liberarMesaVisual(
                mesa
            );


            limpiarResumen();


            mostrarMensaje(
                `Mesa ${mesa} disponible porque no tiene productos.`
            );

            return;
        }


        const total =
            calcularTotal(
                productos
            );


        /*
         * IMPORTANTE:
         * Buscamos el modal directamente
         * para evitar problemas si la referencia
         * inicial no existe.
         */

        const modal =
            document.querySelector(
                '#payment-modal'
            );


        if (!modal) {

            mostrarMensaje(
                'No se encontró la ventana de pago.'
            );

            console.error(
                'No existe #payment-modal'
            );

            return;
        }


        const paymentTable =
            document.querySelector(
                '#payment-table'
            );

        const paymentTotal =
            document.querySelector(
                '#payment-total'
            );


        if (paymentTable) {

            paymentTable.textContent =
                `Mesa ${mesa}`;
        }


        if (paymentTotal) {

            paymentTotal.textContent =
                dinero(total);
        }


        /*
         * Guardamos la mesa en el modal.
         * Así confirmarPago() siempre sabrá
         * cuál mesa cerrar.
         */

        modal.dataset.mesa =
            String(mesa);


        /*
         * FORZAMOS LA VISIBILIDAD.
         */

        modal.style.display = 'flex';
modal.style.position = 'fixed';
modal.style.top = '0';
modal.style.left = '0';
modal.style.width = '100vw';
modal.style.height = '100vh';
modal.style.alignItems = 'center';
modal.style.justifyContent = 'center';
modal.style.zIndex = '99999';


        console.log(
            'MODAL DE PAGO MOSTRADO',
            mesa,
            total
        );
    }


    /* =========================================================
       CONFIRMAR PAGO
    ========================================================= */

    function confirmarPago() {

        const modal =
            document.querySelector(
                '#payment-modal'
            );


        /*
         * Primero intentamos obtener
         * la mesa desde el modal.
         */

        let numeroMesa =
            modal &&
            modal.dataset.mesa
                ? Number(
                    modal.dataset.mesa
                )
                : Number(
                    mesaActual
                );


        if (!numeroMesa) {

            mostrarMensaje(
                'No se pudo determinar la mesa.'
            );

            return;
        }


        const pedido =
            obtenerPedidoMesa(
                numeroMesa
            );


        if (!pedido) {

            mostrarMensaje(
                'No existe un pedido abierto para esta mesa.'
            );

            if (modal) {
                modal.style.display =
                    'none';
            }

            return;
        }


        const productos =
            pedido.productos || [];


        /*
         * Si no hay productos,
         * no hay nada que cobrar.
         */

        if (!productos.length) {

            const pedidos =
                obtenerPedidos();


            delete pedidos[
                String(numeroMesa)
            ];


            guardarPedidos(
                pedidos
            );


            liberarMesaVisual(
                numeroMesa
            );


            if (modal) {

                modal.style.display =
                    'none';

                delete modal.dataset.mesa;
            }


            limpiarResumen();


            mostrarMensaje(
                `Mesa ${numeroMesa} disponible.`
            );

            return;
        }


        /*
         * Guardamos copia del pedido
         * antes de eliminarlo.
         */

        const pedidoCerrado = {

            ...pedido,

            productos:
                productos.map(
                    producto => ({
                        ...producto
                    })
                )
        };


        /*
         * Guardar en historial.
         */

        guardarPedidoEnHistorial(
            pedidoCerrado
        );


        /*
         * Eliminar pedido abierto.
         */

        const pedidos =
            obtenerPedidos();


        delete pedidos[
            String(numeroMesa)
        ];


        guardarPedidos(
            pedidos
        );


        /*
         * Liberar mesa.
         */

        liberarMesaVisual(
            numeroMesa
        );


        /*
         * Cerrar modal.
         */

        if (modal) {

            modal.style.display =
                'none';

            delete modal.dataset.mesa;
        }


        /*
         * Mostrar resumen del consumo
         * que acaba de cerrarse.
         */

        mostrarConsumoCerrado(
            pedidoCerrado
        );


        mesaActual =
            null;

        modoPedido =
            null;


        if (confirmOrderButton) {

            confirmOrderButton.style.display =
                'none';
        }


        if (closeOrderButton) {

            closeOrderButton.style.display =
                'none';
        }


        mostrarMensaje(
            `Pedido de la Mesa ${numeroMesa} cerrado correctamente.`
        );
    }


    /* =========================================================
       MOSTRAR CONSUMO CERRADO
    ========================================================= */

    function mostrarConsumoCerrado(
        pedido
    ) {

        const productos =
            pedido.productos || [];


        if (selectedTable) {

            selectedTable.textContent =
                `Consumo cerrado - Mesa ${pedido.mesa}`;
        }


        if (!orderItems) {
            return;
        }


        if (!productos.length) {

            orderItems.innerHTML = `
                <p class="cart-empty">
                    No hubo productos en este pedido.
                </p>
            `;

            actualizarTotales([]);

            return;
        }


        orderItems.innerHTML =
            productos.map(producto => {

                const nombre =
                    producto.name ||
                    producto.nombre ||
                    'Producto';

                const precio =
                    Number(
                        producto.price ??
                        producto.precio ??
                        0
                    );

                const cantidad =
                    Number(
                        producto.quantity ??
                        producto.cantidad ??
                        0
                    );


                return `
                    <div class="cart-item">

                        <div>

                            <strong>
                                ${nombre}
                            </strong>

                            <p>
                                ${cantidad}
                                ×
                                ${dinero(precio)}
                            </p>

                        </div>

                        <strong>
                            ${dinero(
                                precio * cantidad
                            )}
                        </strong>

                    </div>
                `;

            }).join('');


        actualizarTotales(
            productos
        );


        if (confirmOrderButton) {

            confirmOrderButton.style.display =
                'none';
        }


        if (closeOrderButton) {

            closeOrderButton.style.display =
                'none';
        }
    }


    /* =========================================================
       LIMPIAR RESUMEN
    ========================================================= */

    function limpiarResumen() {

        mesaActual =
            null;

        modoPedido =
            null;


        if (selectedTable) {

            selectedTable.textContent =
                'Ninguna mesa seleccionada';
        }


        if (orderItems) {

            orderItems.innerHTML = `
                <p class="cart-empty">
                    Selecciona una mesa para comenzar.
                </p>
            `;
        }


        actualizarTotales([]);


        if (confirmOrderButton) {

            confirmOrderButton.style.display =
                'none';
        }


        if (closeOrderButton) {

            closeOrderButton.style.display =
                'none';
        }
    }


    /* =========================================================
       EVENTOS DE PRODUCTOS
    ========================================================= */

    document.addEventListener(
        'click',
        event => {

            const button =
                event.target.closest(
                    '[data-product-action]'
                );


            if (!button) {
                return;
            }


            event.preventDefault();


            const accion =
                button.dataset.productAction;

            const productoId =
                button.dataset.productId;


            modificarProductoEnEdicion(
                productoId,
                accion
            );
        }
    );


    /* =========================================================
       EVENTOS DE MESAS
    ========================================================= */

    document.addEventListener(
        'click',
        event => {

            const button =
                event.target.closest(
                    '[data-action]'
                );


            if (!button) {
                return;
            }


            const action =
                button.dataset.action;

            const numeroMesa =
                Number(
                    button.dataset.table
                );


            if (!numeroMesa) {
                return;
            }


            /* =================================================
               CREAR PEDIDO
            ================================================= */

            if (
                action ===
                'create-order'
            ) {

                seleccionarMesaNueva(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               VER PEDIDO
            ================================================= */

            if (
                action ===
                'view-order'
            ) {

                mostrarPedido(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               AGREGAR PRODUCTOS
            ================================================= */

            if (
                action ===
                'add-products'
            ) {

                agregarProductosMesa(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               RESERVAR
            ================================================= */

            if (
                action ===
                'reserve'
            ) {

                abrirReserva(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               VER RESERVA
            ================================================= */

            if (
                action ===
                'reservation'
            ) {

                verReserva(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               CLIENTE LLEGÓ
            ================================================= */

            if (
                action ===
                'client-arrived'
            ) {

                clienteLlego(
                    numeroMesa
                );

                return;
            }


            /* =================================================
               CANCELAR RESERVA
            ================================================= */

            if (
                action ===
                'cancel-reservation'
            ) {

                cancelarReserva(
                    numeroMesa
                );

                return;
            }
        }
    );


    /* =========================================================
       BOTÓN CONFIRMAR PEDIDO
    ========================================================= */

    if (confirmOrderButton) {

        confirmOrderButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                /*
                 * PEDIDO NUEVO
                 */

                if (
                    modoPedido ===
                    'NUEVO'
                ) {

                    confirmarPedidoNuevo();

                    return;
                }


                /*
                 * AGREGAR PRODUCTOS
                 */

                if (
                    modoPedido ===
                    'AGREGAR'
                ) {

                    confirmarAgregarProductos();

                    return;
                }


                /*
                 * EDITAR PEDIDO
                 */

                if (
                    modoPedido ===
                    'EDITAR'
                ) {

                    guardarCambiosPedido();

                    return;
                }


                /*
                 * SI ESTAMOS VIENDO
                 * EL PEDIDO.
                 */

                if (
                    modoPedido ===
                    'VER'
                ) {

                    editarPedido();

                    return;
                }
            };
    }


    /* =========================================================
       BOTÓN CERRAR PEDIDO
    ========================================================= */

    if (closeOrderButton) {

        closeOrderButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                console.log(
                    'CLICK REAL EN CERRAR PEDIDO',
                    mesaActual
                );


                abrirCierrePedido();
            };
    }


    /* =========================================================
       BOTÓN CONFIRMAR PAGO
    ========================================================= */

    const confirmPaymentButton =
        document.querySelector(
            '#confirm-payment'
        );


    if (confirmPaymentButton) {

        confirmPaymentButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                console.log(
                    'CLICK CONFIRMAR PAGO'
                );


                confirmarPago();
            };
    }


    /* =========================================================
       BOTÓN CANCELAR PAGO
    ========================================================= */

    const cancelPaymentButton =
        document.querySelector(
            '#cancel-payment'
        );


    if (cancelPaymentButton) {

        cancelPaymentButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                const modal =
                    document.querySelector(
                        '#payment-modal'
                    );


                if (modal) {

                    modal.style.display =
                        'none';

                    delete modal.dataset.mesa;
                }
            };
    }


    /* =========================================================
       BOTÓN CONFIRMAR RESERVA
    ========================================================= */

    const confirmReservationButton =
        document.querySelector(
            '#confirm-reservation'
        );


    if (confirmReservationButton) {

        confirmReservationButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                confirmarReserva();
            };
    }


    /* =========================================================
       BOTÓN CANCELAR MODAL RESERVA
    ========================================================= */

    const cancelReservationModalButton =
        document.querySelector(
            '#cancel-reservation-modal'
        );


    if (
        cancelReservationModalButton
    ) {

        cancelReservationModalButton.onclick =
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                cerrarReservaModal();
            };
    }


    /* =========================================================
       CERRAR MODALES AL HACER CLICK AFUERA
    ========================================================= */

    if (reservationModal) {

        reservationModal.addEventListener(
            'click',
            event => {

                if (
                    event.target ===
                    reservationModal
                ) {

                    cerrarReservaModal();
                }
            }
        );
    }


    if (paymentModal) {

        paymentModal.addEventListener(
            'click',
            event => {

                if (
                    event.target ===
                    paymentModal
                ) {

                    paymentModal.style.display =
                        'none';

                    delete paymentModal.dataset.mesa;
                }
            }
        );
    }


    /* =========================================================
       INICIALIZACIÓN
    ========================================================= */

    document.addEventListener(
        'DOMContentLoaded',
        () => {

            sincronizarMesas();


            /*
             * El botón cerrar empieza oculto
             * hasta seleccionar una mesa
             * con pedido.
             */

            if (closeOrderButton) {

                closeOrderButton.style.display =
                    'none';
            }


            if (confirmOrderButton) {

                confirmOrderButton.style.display =
                    'none';
            }


            console.log(
                'RB-BAR pedido.js cargado correctamente.'
            );
        }
    );

})();