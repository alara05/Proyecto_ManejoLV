CREATE DATABASE IF NOT EXISTS manejo_buses
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE manejo_buses;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS registro_accesos;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS boletos;
DROP TABLE IF EXISTS salidas;
DROP TABLE IF EXISTS asientos;
DROP TABLE IF EXISTS tipo_asientos;
DROP TABLE IF EXISTS rutas;
DROP TABLE IF EXISTS buses;
DROP TABLE IF EXISTS frecuencia_paradas;
DROP TABLE IF EXISTS frecuencias;
DROP TABLE IF EXISTS configuracion_aplicacion;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cooperativas;
DROP TABLE IF EXISTS ciudades;
DROP TABLE IF EXISTS provincias;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS migrations;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE cooperativas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL UNIQUE,
  ruc VARCHAR(13) NULL UNIQUE,
  telefono VARCHAR(20) NULL,
  email VARCHAR(255) NULL,
  direccion VARCHAR(255) NULL,
  logo_path VARCHAR(255) NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE provincias (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL UNIQUE,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ciudades (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  provincia_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY ciudades_nombre_provincia_id_unique (nombre, provincia_id),
  KEY ciudades_provincia_id_index (provincia_id),
  CONSTRAINT ciudades_provincia_id_foreign
    FOREIGN KEY (provincia_id) REFERENCES provincias(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  cedula VARCHAR(10) NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'cooperativa', 'oficinista', 'cliente', 'personal_bus') NOT NULL DEFAULT 'cliente',
  cooperativa_id BIGINT UNSIGNED NULL,
  telefono VARCHAR(20) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  KEY users_cooperativa_id_index (cooperativa_id),
  CONSTRAINT users_cooperativa_id_foreign
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE frecuencias (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cooperativa_id BIGINT UNSIGNED NOT NULL,
  ciudad_origen_id BIGINT UNSIGNED NOT NULL,
  ciudad_destino_id BIGINT UNSIGNED NOT NULL,
  hora_salida TIME NOT NULL,
  numero_resolucion_ant VARCHAR(255) NULL,
  fecha_resolucion_ant DATE NULL,
  tiene_paradas TINYINT(1) NOT NULL DEFAULT 0,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  KEY frecuencias_cooperativa_id_index (cooperativa_id),
  KEY frecuencias_ciudad_origen_id_index (ciudad_origen_id),
  KEY frecuencias_ciudad_destino_id_index (ciudad_destino_id),
  CONSTRAINT frecuencias_cooperativa_id_foreign
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id)
    ON DELETE CASCADE,
  CONSTRAINT frecuencias_ciudad_origen_id_foreign
    FOREIGN KEY (ciudad_origen_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT,
  CONSTRAINT frecuencias_ciudad_destino_id_foreign
    FOREIGN KEY (ciudad_destino_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE frecuencia_paradas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  frecuencia_id BIGINT UNSIGNED NOT NULL,
  ciudad_id BIGINT UNSIGNED NOT NULL,
  orden INT UNSIGNED NOT NULL,
  minutos_desde_origen INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY frecuencia_paradas_frecuencia_ciudad_unique (frecuencia_id, ciudad_id),
  UNIQUE KEY frecuencia_paradas_frecuencia_orden_unique (frecuencia_id, orden),
  KEY frecuencia_paradas_ciudad_id_index (ciudad_id),
  CONSTRAINT frecuencia_paradas_frecuencia_id_foreign
    FOREIGN KEY (frecuencia_id) REFERENCES frecuencias(id)
    ON DELETE CASCADE,
  CONSTRAINT frecuencia_paradas_ciudad_id_foreign
    FOREIGN KEY (ciudad_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE buses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cooperativa_id BIGINT UNSIGNED NOT NULL,
  numero VARCHAR(255) NOT NULL,
  placa VARCHAR(10) NOT NULL UNIQUE,
  marca_chasis VARCHAR(255) NULL,
  marca_carroceria VARCHAR(255) NULL,
  anio SMALLINT UNSIGNED NULL,
  capacidad_total SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  foto_path VARCHAR(255) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY buses_cooperativa_numero_unique (cooperativa_id, numero),
  CONSTRAINT buses_cooperativa_id_foreign
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rutas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cooperativa_id BIGINT UNSIGNED NOT NULL,
  bus_id BIGINT UNSIGNED NULL,
  ciudad_origen_id BIGINT UNSIGNED NOT NULL,
  ciudad_destino_id BIGINT UNSIGNED NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  tipo_viaje ENUM('directo', 'con_paradas') NOT NULL DEFAULT 'directo',
  distancia_km DECIMAL(8,2) NULL,
  duracion_minutos SMALLINT UNSIGNED NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY rutas_coop_origen_destino_nombre_unique (cooperativa_id, ciudad_origen_id, ciudad_destino_id, nombre),
  KEY rutas_bus_id_index (bus_id),
  KEY rutas_ciudad_origen_id_index (ciudad_origen_id),
  KEY rutas_ciudad_destino_id_index (ciudad_destino_id),
  CONSTRAINT rutas_cooperativa_id_foreign
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id)
    ON DELETE CASCADE,
  CONSTRAINT rutas_bus_id_foreign
    FOREIGN KEY (bus_id) REFERENCES buses(id)
    ON DELETE SET NULL,
  CONSTRAINT rutas_ciudad_origen_id_foreign
    FOREIGN KEY (ciudad_origen_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT,
  CONSTRAINT rutas_ciudad_destino_id_foreign
    FOREIGN KEY (ciudad_destino_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tipo_asientos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cooperativa_id BIGINT UNSIGNED NULL,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  recargo DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  KEY tipo_asientos_cooperativa_id_index (cooperativa_id),
  CONSTRAINT tipo_asientos_cooperativa_id_foreign
    FOREIGN KEY (cooperativa_id) REFERENCES cooperativas(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE asientos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bus_id BIGINT UNSIGNED NOT NULL,
  tipo_asiento_id BIGINT UNSIGNED NOT NULL,
  numero VARCHAR(255) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY asientos_bus_numero_unique (bus_id, numero),
  KEY asientos_tipo_asiento_id_index (tipo_asiento_id),
  CONSTRAINT asientos_bus_id_foreign
    FOREIGN KEY (bus_id) REFERENCES buses(id)
    ON DELETE CASCADE,
  CONSTRAINT asientos_tipo_asiento_id_foreign
    FOREIGN KEY (tipo_asiento_id) REFERENCES tipo_asientos(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE salidas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  frecuencia_id BIGINT UNSIGNED NOT NULL,
  bus_id BIGINT UNSIGNED NOT NULL,
  fecha DATE NOT NULL,
  hora_salida TIME NOT NULL,
  estado ENUM('programada', 'en_ruta', 'finalizada', 'cancelada') NOT NULL DEFAULT 'programada',
  precio_base DECIMAL(8,2) NOT NULL,
  generada_automaticamente TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY salidas_bus_fecha_hora_unique (bus_id, fecha, hora_salida),
  KEY salidas_frecuencia_id_index (frecuencia_id),
  CONSTRAINT salidas_frecuencia_id_foreign
    FOREIGN KEY (frecuencia_id) REFERENCES frecuencias(id)
    ON DELETE RESTRICT,
  CONSTRAINT salidas_bus_id_foreign
    FOREIGN KEY (bus_id) REFERENCES buses(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE boletos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  salida_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  asiento_id BIGINT UNSIGNED NOT NULL,
  ciudad_origen_id BIGINT UNSIGNED NOT NULL,
  ciudad_destino_id BIGINT UNSIGNED NOT NULL,
  codigo VARCHAR(255) NOT NULL UNIQUE,
  pasajero_nombre VARCHAR(255) NOT NULL,
  pasajero_cedula VARCHAR(10) NOT NULL,
  tipo_descuento ENUM('ninguno', 'menor_edad', 'discapacidad', 'tercera_edad') NOT NULL DEFAULT 'ninguno',
  porcentaje_descuento DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  precio DECIMAL(8,2) NOT NULL,
  estado ENUM('reservado', 'pagado', 'anulado', 'usado') NOT NULL DEFAULT 'reservado',
  vendido_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY boletos_salida_asiento_unique (salida_id, asiento_id),
  KEY boletos_user_id_index (user_id),
  KEY boletos_ciudad_origen_id_index (ciudad_origen_id),
  KEY boletos_ciudad_destino_id_index (ciudad_destino_id),
  KEY boletos_asiento_id_index (asiento_id),
  CONSTRAINT boletos_salida_id_foreign
    FOREIGN KEY (salida_id) REFERENCES salidas(id)
    ON DELETE RESTRICT,
  CONSTRAINT boletos_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL,
  CONSTRAINT boletos_asiento_id_foreign
    FOREIGN KEY (asiento_id) REFERENCES asientos(id)
    ON DELETE RESTRICT,
  CONSTRAINT boletos_ciudad_origen_id_foreign
    FOREIGN KEY (ciudad_origen_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT,
  CONSTRAINT boletos_ciudad_destino_id_foreign
    FOREIGN KEY (ciudad_destino_id) REFERENCES ciudades(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pagos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  boleto_id BIGINT UNSIGNED NOT NULL,
  validado_por BIGINT UNSIGNED NULL,
  metodo ENUM('transferencia', 'deposito') NOT NULL DEFAULT 'transferencia',
  monto DECIMAL(8,2) NOT NULL,
  comprobante_path VARCHAR(255) NULL,
  estado ENUM('pendiente', 'validado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  validado_at TIMESTAMP NULL DEFAULT NULL,
  observacion TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  KEY pagos_boleto_id_index (boleto_id),
  KEY pagos_validado_por_index (validado_por),
  CONSTRAINT pagos_boleto_id_foreign
    FOREIGN KEY (boleto_id) REFERENCES boletos(id)
    ON DELETE CASCADE,
  CONSTRAINT pagos_validado_por_foreign
    FOREIGN KEY (validado_por) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE registro_accesos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  boleto_id BIGINT UNSIGNED NOT NULL,
  registrado_por BIGINT UNSIGNED NULL,
  registrado_at TIMESTAMP NOT NULL,
  resultado ENUM('permitido', 'rechazado') NOT NULL DEFAULT 'permitido',
  observacion TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  KEY registro_accesos_boleto_id_index (boleto_id),
  KEY registro_accesos_registrado_por_index (registrado_por),
  CONSTRAINT registro_accesos_boleto_id_foreign
    FOREIGN KEY (boleto_id) REFERENCES boletos(id)
    ON DELETE CASCADE,
  CONSTRAINT registro_accesos_registrado_por_foreign
    FOREIGN KEY (registrado_por) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE configuracion_aplicacion (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre_aplicacion VARCHAR(255) NOT NULL DEFAULT 'Manejo Buses',
  logo_path VARCHAR(255) NULL,
  color_primario VARCHAR(20) NOT NULL DEFAULT '#0f172a',
  color_secundario VARCHAR(20) NOT NULL DEFAULT '#f59e0b',
  email_soporte VARCHAR(255) NULL,
  telefono_soporte VARCHAR(20) NULL,
  redes_sociales JSON NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
  email VARCHAR(255) PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
  id VARCHAR(255) PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload LONGTEXT NOT NULL,
  last_activity INT NOT NULL,
  KEY sessions_user_id_index (user_id),
  KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache (
  `key` VARCHAR(255) PRIMARY KEY,
  value MEDIUMTEXT NOT NULL,
  expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
  `key` VARCHAR(255) PRIMARY KEY,
  owner VARCHAR(255) NOT NULL,
  expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  queue VARCHAR(255) NOT NULL,
  payload LONGTEXT NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL,
  reserved_at INT UNSIGNED NULL,
  available_at INT UNSIGNED NOT NULL,
  created_at INT UNSIGNED NOT NULL,
  KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_batches (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  total_jobs INT NOT NULL,
  pending_jobs INT NOT NULL,
  failed_jobs INT NOT NULL,
  failed_job_ids LONGTEXT NOT NULL,
  options MEDIUMTEXT NULL,
  cancelled_at INT NULL,
  created_at INT NOT NULL,
  finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid VARCHAR(255) NOT NULL UNIQUE,
  connection TEXT NOT NULL,
  queue TEXT NOT NULL,
  payload LONGTEXT NOT NULL,
  exception LONGTEXT NOT NULL,
  failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE migrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(255) NOT NULL,
  batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
