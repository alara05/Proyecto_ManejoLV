-- =======================================================
-- 1. CREACIÓN DE LAS BASES DE DATOS (MICROSERVICIOS)
-- =======================================================
CREATE DATABASE auth_db;
CREATE DATABASE bus_db;
CREATE DATABASE ticket_db;
CREATE DATABASE ms_pagos; -- Creada para futuro uso

-- =======================================================
-- 2. MICROSERVICIO: AUTH & USUARIOS (auth_db)
-- =======================================================
\c auth_db;

CREATE TYPE EstadoUsuario AS ENUM ('ACTIVO', 'INACTIVO');
CREATE TYPE EstadoRolUsuario AS ENUM ('ACTIVO', 'INACTIVO');
CREATE TYPE NombreRol AS ENUM ('ADMIN', 'OFICIAL', 'OFICINISTA', 'DUENO', 'PASAJERO', 'SOPORTE');
CREATE TYPE NombrePermiso AS ENUM ('ESCANEAR_QR', 'VER_TURNO', 'VER_ASIENTOS', 'VENDER_BOLETO', 'VER_REPORTES', 'GESTIONAR_BUSES', 'PAGAR_DUENOS', 'GESTIONAR_USUARIOS', 'COMPRAR_BOLETO', 'VER_MIS_BOLETOS', 'VER_MIS_PAGOS', 'VER_TOTAL_VIAJES', 'ALERTA_GPS', 'VER_RECLAMOS', 'GESTIONAR_RECLAMOS', 'APROBAR_TRANSFERENCIA', 'VENDER_BOLETO_OFICINA', 'VER_PAGOS_PENDIENTES', 'ANULAR_BOLETO');

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    estado EstadoUsuario DEFAULT 'ACTIVO',
    creado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nombre NombreRol UNIQUE NOT NULL,
    descripcion TEXT
);

CREATE TABLE permisos (
    id SERIAL PRIMARY KEY,
    nombre NombrePermiso UNIQUE NOT NULL,
    descripcion TEXT
);

CREATE TABLE usuario_roles (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    rol_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    asignado_por INTEGER,
    asignado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP(3),
    estado EstadoRolUsuario DEFAULT 'ACTIVO',
    UNIQUE(usuario_id, rol_id)
);

CREATE TABLE rol_permisos (
    id SERIAL PRIMARY KEY,
    rol_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    permiso_id INTEGER NOT NULL REFERENCES permisos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE(rol_id, permiso_id)
);

CREATE TABLE sesiones (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    token TEXT UNIQUE NOT NULL,
    expira_en TIMESTAMP(3) NOT NULL,
    dispositivo TEXT
);

-- =======================================================
-- 3. MICROSERVICIO: OPERACIONES & FLOTA (bus_db)
-- =======================================================
\c bus_db;

CREATE TYPE EstadoGeneral AS ENUM ('ACTIVO', 'INACTIVO');
CREATE TYPE EstadoBus AS ENUM ('ACTIVO', 'MANTENIMIENTO', 'INACTIVO');
CREATE TYPE EstadoTurno AS ENUM ('PENDIENTE', 'EN_RUTA', 'COMPLETADO', 'CANCELADO');
CREATE TYPE TipoAsiento AS ENUM ('NORMAL', 'VIP', 'DISCAPACIDAD');
CREATE TYPE EstadoAsientoTurno AS ENUM ('DISPONIBLE', 'RESERVADO', 'OCUPADO', 'VACIO');
CREATE TYPE DiaSemana AS ENUM ('LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM');

CREATE TABLE cooperativas (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    ruc TEXT UNIQUE NOT NULL,
    estado EstadoGeneral DEFAULT 'ACTIVO'
);

CREATE TABLE duenos (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    cedula TEXT UNIQUE NOT NULL,
    telefono TEXT,
    cuenta_bancaria TEXT,
    banco TEXT,
    estado EstadoGeneral DEFAULT 'ACTIVO'
);

CREATE TABLE buses (
    id SERIAL PRIMARY KEY,
    cooperativa_id INTEGER NOT NULL REFERENCES cooperativas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    dueno_id INTEGER NOT NULL REFERENCES duenos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    placa TEXT UNIQUE NOT NULL,
    marca TEXT NOT NULL,
    carroceria TEXT NOT NULL,
    modelo TEXT NOT NULL,
    anio INTEGER NOT NULL,
    capacidad INTEGER NOT NULL,
    color TEXT,
    estado EstadoBus DEFAULT 'ACTIVO'
);

CREATE TABLE choferes (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    cedula TEXT UNIQUE NOT NULL,
    telefono TEXT,
    licencia TEXT UNIQUE NOT NULL,
    tipo_licencia TEXT NOT NULL,
    estado EstadoGeneral DEFAULT 'ACTIVO'
);

CREATE TABLE rutas (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    origen TEXT NOT NULL,
    destino TEXT NOT NULL,
    duracion_min INTEGER NOT NULL,
    precio_pasaje DECIMAL(10, 2) NOT NULL
);

CREATE TABLE paradas (
    id SERIAL PRIMARY KEY,
    ruta_id INTEGER NOT NULL REFERENCES rutas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    nombre TEXT NOT NULL,
    orden INTEGER NOT NULL,
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    metros_alerta INTEGER NOT NULL DEFAULT 500
);

CREATE TABLE frecuencias (
    id SERIAL PRIMARY KEY,
    ruta_id INTEGER NOT NULL REFERENCES rutas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    bus_id INTEGER NOT NULL REFERENCES buses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    dia_semana DiaSemana NOT NULL,
    hora_salida TEXT NOT NULL,
    hora_llegada TEXT NOT NULL
);

CREATE TABLE turnos (
    id SERIAL PRIMARY KEY,
    bus_id INTEGER NOT NULL REFERENCES buses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ruta_id INTEGER NOT NULL REFERENCES rutas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    chofer_id INTEGER NOT NULL REFERENCES choferes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    fecha DATE NOT NULL,
    hora_inicio TEXT NOT NULL,
    hora_fin TEXT,
    estado EstadoTurno DEFAULT 'PENDIENTE'
);

CREATE TABLE asientos (
    id SERIAL PRIMARY KEY,
    bus_id INTEGER NOT NULL REFERENCES buses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    numero INTEGER NOT NULL,
    fila TEXT,
    tipo TipoAsiento DEFAULT 'NORMAL',
    estado EstadoGeneral DEFAULT 'ACTIVO',
    UNIQUE(bus_id, numero)
);

CREATE TABLE asiento_turnos (
    id SERIAL PRIMARY KEY,
    turno_id INTEGER NOT NULL REFERENCES turnos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    asiento_id INTEGER NOT NULL REFERENCES asientos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    boleto_id INTEGER,
    estado EstadoAsientoTurno DEFAULT 'DISPONIBLE',
    UNIQUE(turno_id, asiento_id)
);

-- =======================================================
-- 4. MICROSERVICIO: VENTAS & PAGOS (ticket_db)
-- =======================================================
\c ticket_db;

CREATE TYPE CanalVenta AS ENUM ('APP', 'WEB', 'OFICIAL', 'OFICINISTA');
CREATE TYPE EstadoCompra AS ENUM ('PENDIENTE', 'CONFIRMADA', 'ANULADA');
CREATE TYPE EstadoBoleto AS ENUM ('PENDIENTE', 'VIGENTE', 'UTILIZADO', 'EXPIRADO', 'ANULADO');
CREATE TYPE MetodoPago AS ENUM ('TARJETA', 'TRANSFERENCIA', 'EFECTIVO');
CREATE TYPE EstadoPago AS ENUM ('PENDIENTE', 'APROBADO', 'RECHAZADO');
CREATE TYPE EstadoTransferencia AS ENUM ('PENDIENTE', 'APROBADO', 'RECHAZADO');
CREATE TYPE EstadoAprobacion AS ENUM ('APROBADO', 'RECHAZADO');
CREATE TYPE CanalEfectivo AS ENUM ('BUS', 'OFICINA');
CREATE TYPE ResultadoEscaneo AS ENUM ('APROBADO', 'RECHAZADO');
CREATE TYPE EstadoBoletoParada AS ENUM ('PENDIENTE', 'ALERTADO', 'BAJADO');
CREATE TYPE EstadoAlerta AS ENUM ('ACTIVA', 'DISPARADA', 'INACTIVA');
CREATE TYPE TipoTarifa AS ENUM ('NORMAL', 'TERCERA_EDAD', 'DISCAPACIDAD', 'MENOR');

CREATE TABLE compras (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL,
    frecuencia_id INTEGER NOT NULL,
    fecha_viaje DATE NOT NULL,
    cantidad INTEGER NOT NULL DEFAULT 1,
    total DECIMAL(10, 2) NOT NULL,
    canal CanalVenta NOT NULL,
    estado EstadoCompra DEFAULT 'PENDIENTE',
    creado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE boletos (
    id SERIAL PRIMARY KEY,
    compra_id INTEGER NOT NULL REFERENCES compras(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    uuid_qr TEXT UNIQUE NOT NULL,
    cedula_pasajero TEXT NOT NULL,
    nombre_pasajero TEXT NOT NULL,
    tipo_tarifa TipoTarifa DEFAULT 'NORMAL',
    estado EstadoBoleto DEFAULT 'PENDIENTE',
    expira_en TIMESTAMP(3) NOT NULL,
    creado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pagos_pasajero (
    id SERIAL PRIMARY KEY,
    compra_id INTEGER UNIQUE NOT NULL REFERENCES compras(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    monto DECIMAL(10, 2) NOT NULL,
    metodo MetodoPago NOT NULL,
    estado EstadoPago DEFAULT 'PENDIENTE',
    pagado_en TIMESTAMP(3)
);

CREATE TABLE pagos_tarjeta (
    id SERIAL PRIMARY KEY,
    pago_id INTEGER UNIQUE NOT NULL REFERENCES pagos_pasajero(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    ultimos_4 TEXT NOT NULL,
    marca TEXT NOT NULL,
    referencia_pasarela TEXT NOT NULL
);

CREATE TABLE pagos_transferencia (
    id SERIAL PRIMARY KEY,
    pago_id INTEGER UNIQUE NOT NULL REFERENCES pagos_pasajero(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    banco TEXT NOT NULL,
    referencia TEXT NOT NULL,
    comprobante_url TEXT,
    estado EstadoTransferencia DEFAULT 'PENDIENTE'
);

CREATE TABLE aprobaciones (
    id SERIAL PRIMARY KEY,
    pago_transferencia_id INTEGER UNIQUE NOT NULL REFERENCES pagos_transferencia(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    oficinista_id INTEGER NOT NULL,
    estado EstadoAprobacion NOT NULL,
    observacion TEXT,
    revisado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pagos_efectivo (
    id SERIAL PRIMARY KEY,
    pago_id INTEGER UNIQUE NOT NULL REFERENCES pagos_pasajero(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    vendedor_id INTEGER NOT NULL,
    monto_recibido DECIMAL(10, 2) NOT NULL,
    cambio DECIMAL(10, 2) NOT NULL,
    canal_venta CanalEfectivo NOT NULL,
    turno_id INTEGER
);

CREATE TABLE escaneos (
    id SERIAL PRIMARY KEY,
    boleto_id INTEGER UNIQUE NOT NULL REFERENCES boletos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    oficial_id INTEGER NOT NULL,
    bus_id INTEGER NOT NULL,
    turno_id INTEGER NOT NULL,
    resultado ResultadoEscaneo NOT NULL,
    escaneado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE boleto_paradas (
    id SERIAL PRIMARY KEY,
    boleto_id INTEGER UNIQUE NOT NULL REFERENCES boletos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    parada_origen_id INTEGER NOT NULL,
    parada_destino_id INTEGER NOT NULL,
    estado EstadoBoletoParada DEFAULT 'PENDIENTE'
);

CREATE TABLE alertas_gps (
    id SERIAL PRIMARY KEY,
    boleto_parada_id INTEGER NOT NULL REFERENCES boleto_paradas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    turno_id INTEGER NOT NULL,
    lat_actual DECIMAL(10, 8) NOT NULL,
    lng_actual DECIMAL(11, 8) NOT NULL,
    metros_restantes INTEGER NOT NULL,
    estado EstadoAlerta DEFAULT 'ACTIVA',
    actualizado_en TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP
);