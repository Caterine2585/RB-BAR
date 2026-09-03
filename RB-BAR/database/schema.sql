-- Esquema definitivo de RB-BAR para MySQL 8.0.16 o superior.
-- No incluye datos de prueba ni triggers de inventario.

CREATE DATABASE IF NOT EXISTS bar_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bar_db;

CREATE TABLE administradores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    usuario VARCHAR(60) NOT NULL,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_administradores_usuario UNIQUE (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    estado ENUM('ACTIVA', 'INACTIVA') NOT NULL DEFAULT 'ACTIVA',
    CONSTRAINT uq_categorias_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255) NULL,
    estado ENUM('DISPONIBLE', 'AGOTADO', 'INACTIVO') NOT NULL DEFAULT 'DISPONIBLE',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_productos_precio_no_negativo CHECK (precio >= 0),
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_productos_categoria (categoria_id),
    INDEX idx_productos_estado (estado),
    INDEX idx_productos_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id BIGINT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL DEFAULT 0,
    stock_minimo INT UNSIGNED NOT NULL DEFAULT 0,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_inventario_producto UNIQUE (producto_id),
    CONSTRAINT chk_inventario_cantidad_no_negativa CHECK (cantidad >= 0),
    CONSTRAINT chk_inventario_stock_minimo_no_negativo CHECK (stock_minimo >= 0),
    CONSTRAINT fk_inventario_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE movimientos_inventario (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id BIGINT UNSIGNED NOT NULL,
    administrador_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('ENTRADA', 'SALIDA', 'AJUSTE') NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_movimientos_cantidad_positiva CHECK (cantidad > 0),
    CONSTRAINT fk_movimientos_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_administrador
        FOREIGN KEY (administrador_id) REFERENCES administradores (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_movimientos_producto_fecha (producto_id, fecha),
    INDEX idx_movimientos_administrador_fecha (administrador_id, fecha),
    INDEX idx_movimientos_tipo_fecha (tipo, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mesas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero SMALLINT UNSIGNED NOT NULL,
    capacidad_sillas SMALLINT UNSIGNED NOT NULL,
    zona VARCHAR(100) NOT NULL,
    estado ENUM('DISPONIBLE', 'OCUPADA', 'RESERVADA', 'INACTIVA') NOT NULL DEFAULT 'DISPONIBLE',
    CONSTRAINT uq_mesas_numero UNIQUE (numero),
    CONSTRAINT chk_mesas_numero_valido CHECK (numero > 0),
    CONSTRAINT chk_mesas_capacidad_valida CHECK (capacidad_sillas > 0),
    INDEX idx_mesas_estado_zona (estado, zona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mesa_id BIGINT UNSIGNED NULL,
    nombre_cliente VARCHAR(150) NULL,
    cantidad_personas SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    estado ENUM('PENDIENTE', 'EN_PREPARACION', 'LISTO', 'ENTREGADO', 'CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    observaciones TEXT NULL,
    fecha_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_pedidos_cantidad_personas_valida CHECK (cantidad_personas > 0),
    CONSTRAINT chk_pedidos_total_no_negativo CHECK (total >= 0),
    CONSTRAINT fk_pedidos_mesa
        FOREIGN KEY (mesa_id) REFERENCES mesas (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_pedidos_mesa_fecha (mesa_id, fecha_pedido),
    INDEX idx_pedidos_estado_fecha (estado, fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detalle_pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT chk_detalle_cantidad_positiva CHECK (cantidad > 0),
    CONSTRAINT chk_detalle_precio_no_negativo CHECK (precio_unitario >= 0),
    CONSTRAINT chk_detalle_subtotal_no_negativo CHECK (subtotal >= 0),
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_detalle_pedido (pedido_id),
    INDEX idx_detalle_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eventos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    imagen VARCHAR(255) NULL,
    estado ENUM('ACTIVO', 'FINALIZADO', 'CANCELADO') NOT NULL DEFAULT 'ACTIVO',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_eventos_estado_fecha (estado, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE promociones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    tipo ENUM('PORCENTAJE', 'VALOR', 'DOS_POR_UNO') NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('ACTIVA', 'FINALIZADA', 'INACTIVA') NOT NULL DEFAULT 'ACTIVA',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_promociones_fechas_validas CHECK (fecha_fin >= fecha_inicio),
    CONSTRAINT chk_promociones_valor_valido CHECK (
        (tipo = 'PORCENTAJE' AND valor > 0 AND valor <= 100)
        OR (tipo = 'VALOR' AND valor > 0)
        OR (tipo = 'DOS_POR_UNO' AND valor = 0)
    ),
    INDEX idx_promociones_estado_fechas (estado, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE promociones_productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    promocion_id BIGINT UNSIGNED NOT NULL,
    producto_id BIGINT UNSIGNED NOT NULL,
    CONSTRAINT uq_promociones_productos UNIQUE (promocion_id, producto_id),
    CONSTRAINT fk_promociones_productos_promocion
        FOREIGN KEY (promocion_id) REFERENCES promociones (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_promociones_productos_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_promociones_productos_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
