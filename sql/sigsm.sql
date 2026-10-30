CREATE DATABASE IF NOT EXISTS sigsm
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sigsm;
-- Instalación no destructiva: no elimina tablas ni vistas existentes.

-- ============================================================
-- TABLA: USUARIOS
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    usuario VARCHAR(50) NOT NULL UNIQUE,

    cedula VARCHAR(20) NULL UNIQUE,

    clave VARCHAR(255) NOT NULL,

    rol ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',

    activo TINYINT(1) NOT NULL DEFAULT 1,

    -- Restricciones de negocio
    CONSTRAINT chk_usuarios_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2),

    CONSTRAINT chk_usuarios_activo
        CHECK (activo IN (0,1))

);


-- ============================================================
-- TABLA: CATEGORIAS
-- Servicios/secciones que se mostrarán en el portal
-- del paciente.
-- ============================================================

CREATE TABLE IF NOT EXISTS categorias (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    -- Una categoría debe contener texto significativo.
    CONSTRAINT chk_categorias_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);


-- ============================================================
-- TABLA: DOCUMENTOS
-- ============================================================

CREATE TABLE IF NOT EXISTS documentos (

    id INT AUTO_INCREMENT PRIMARY KEY,

    titulo VARCHAR(150) NOT NULL,

    categoria_id INT NOT NULL,

    descripcion TEXT,

    archivo VARCHAR(255) NOT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id)
        REFERENCES categorias(id),

    -- Restricciones de negocio
    CONSTRAINT chk_documentos_titulo
        CHECK (CHAR_LENGTH(TRIM(titulo)) >= 3),

    CONSTRAINT chk_documentos_archivo
        CHECK (CHAR_LENGTH(TRIM(archivo)) >= 1),

    CONSTRAINT chk_documentos_activo
        CHECK (activo IN (0,1))

);


-- ============================================================
-- TABLA: SERVICIOS
-- ============================================================

CREATE TABLE IF NOT EXISTS servicios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    CONSTRAINT chk_servicios_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);


-- ============================================================
-- TABLA: CONFIGURACION DE ENCUESTAS
-- ============================================================

CREATE TABLE IF NOT EXISTS encuestas_config (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    pregunta VARCHAR(250) NOT NULL DEFAULT '¿Cómo valora el servicio recibido?',

    descripcion VARCHAR(500),

    activa TINYINT(1) NOT NULL DEFAULT 1,

    servicio_id INT NULL,

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_encuestas_config_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 3),

    CONSTRAINT chk_encuestas_config_activa
        CHECK (activa IN (0,1)),
    CONSTRAINT fk_encuestas_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL

);


-- ============================================================
-- TABLA: ENCUESTAS
-- ============================================================

CREATE TABLE IF NOT EXISTS encuestas (

    id INT AUTO_INCREMENT PRIMARY KEY,

    encuesta_id INT NOT NULL,

    puntaje TINYINT NOT NULL,

    comentario VARCHAR(500),

    token_envio CHAR(64) NULL UNIQUE,

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (encuesta_id)
        REFERENCES encuestas_config(id),

    -- Escala de valoración de 1 a 5.
    CONSTRAINT chk_encuestas_puntaje
        CHECK (puntaje BETWEEN 1 AND 5)

);


-- ============================================================
-- TABLA: TRASLADOS
-- ============================================================

CREATE TABLE IF NOT EXISTS traslados (

    id INT AUTO_INCREMENT PRIMARY KEY,

    paciente VARCHAR(150) NOT NULL,

    chofer VARCHAR(100) NOT NULL,

    enfermero VARCHAR(100) NOT NULL,

    vehiculo VARCHAR(100),

    origen VARCHAR(150) NOT NULL,

    destino VARCHAR(150) NOT NULL,

    salida DATETIME NULL,

    llegada DATETIME NULL,

    estado VARCHAR(40) NOT NULL DEFAULT 'Solicitado',

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Validaciones de datos obligatorios
    CONSTRAINT chk_traslados_paciente
        CHECK (CHAR_LENGTH(TRIM(paciente)) >= 2),

    CONSTRAINT chk_traslados_chofer
        CHECK (CHAR_LENGTH(TRIM(chofer)) >= 2),

    CONSTRAINT chk_traslados_enfermero
        CHECK (CHAR_LENGTH(TRIM(enfermero)) >= 2),

    CONSTRAINT chk_traslados_origen
        CHECK (CHAR_LENGTH(TRIM(origen)) >= 2),

    CONSTRAINT chk_traslados_destino
        CHECK (CHAR_LENGTH(TRIM(destino)) >= 2),

    CONSTRAINT chk_traslados_vehiculo
        CHECK (
            vehiculo IS NULL
            OR CHAR_LENGTH(TRIM(vehiculo)) >= 2
        ),

    -- Estados permitidos
    CONSTRAINT chk_traslados_estado
        CHECK (
            estado IN (
                'Solicitado',
                'En camino',
                'Realizado',
                'Cancelado'
            )
        ),

    -- La llegada no puede ser anterior a la salida
    CONSTRAINT chk_traslados_fechas
        CHECK (
            salida IS NULL
            OR llegada IS NULL
            OR llegada >= salida
        )

);

INSERT IGNORE INTO usuarios(nombre, usuario, clave, rol) VALUES

(
    'Administrador del sistema',
    'admin',
    '$2y$12$3opYqVWebTuX2SGqIngE1.UcZFIT9qqxWpHbFGJrpdf.X7hqEyHfW',
    'admin'
),

(
    'Usuario básico',
    'usuario',
    '$2y$12$3opYqVWebTuX2SGqIngE1.UcZFIT9qqxWpHbFGJrpdf.X7hqEyHfW',
    'usuario'
);


-- ============================================================
-- CATEGORIAS / SERVICIOS DEL PORTAL DEL PACIENTE
-- ============================================================

INSERT IGNORE INTO categorias(nombre) VALUES

    ('Unidad de Cuidados Paliativos'),

    ('Emergencia'),

    ('Neonatología'),

    ('Dpto. Clínico de medicina'),

    ('Cardiología'),

    ('Centro cardiovascular'),

    ('Hemodinamia'),

    ('Cirugía cardíaca'),

    ('Clínica médica A'),

    ('Clínica médica B'),

    ('Clínica médica C'),

    ('Dermatología'),

    ('Endocrinología'),

    ('Gastroenterología'),

    ('Unidad de ostomías'),

    ('Geriatría'),

    ('Hematología'),

    ('Infectología'),

    ('Medicina física, rehabilitación y medicina del deporte'),

    ('Nefrología'),

    ('Neurología'),

    ('Oncología'),

    ('Psiquiatría'),

    ('U.E. Autoinmunes sistémicas'),

    ('Unidad de tabaquismo'),

    ('Depto. Clínico de cirugía'),

    ('Anestesiología'),

    ('Cirugía plástica y quemados'),

    ('Cirugía vascular periférica'),

    ('Ginecotocologica B'),

    ('Neurocirugía'),

    ('Odontología'),

    ('Oftalmología'),

    ('Otorrinolaringología'),

    ('Quirúrgica A'),

    ('Quirúrgica B'),

    ('Quirúrgica F'),

    ('Urología'),

    ('Traumatología y ortopedia de adultos'),

    ('Radioterapia'),

    ('Medicina Nuclear'),

    ('Hemoterapia'),

    ('Imagenología');


-- ============================================================
-- SERVICIOS GENERALES DEL SISTEMA
-- ============================================================

INSERT IGNORE INTO servicios(nombre) VALUES

    ('Atención al paciente'),

    ('Enfermería'),

    ('Traslados'),

    ('Información');


-- ============================================================
-- DOCUMENTOS DE EJEMPLO
-- ============================================================

INSERT IGNORE INTO documentos(
    titulo,
    categoria_id,
    descripcion,
    archivo
) VALUES

(
    'Indicaciones de interrupción voluntaria del embarazo',
    30,
    'Información para el paciente sobre interrupción voluntaria del embarazo.',
    'ejemplo.pdf'
),

(
    'Prostatectomía radical - indicaciones e información para el paciente',
    38,
    'Indicaciones e información para pacientes sometidos a prostatectomía radical.',
    'ejemplo.pdf'
),

(
    'Preparación para estudios imagenológicos',
    43,
    'Información para preparar al paciente antes de un estudio imagenológico.',
    'ejemplo.pdf'
),

(
    'Estudios diagnósticos con pertecneciato',
    41,
    'Información para pacientes sobre estudios diagnósticos con pertecneciato.',
    'ejemplo.pdf'
),

(
    'Información sobre Centellograma de perfusión miocárdica',
    41,
    'Información para el paciente sobre el centellograma de perfusión miocárdica.',
    'ejemplo.pdf'
),

(
    'Indicaciones ecocardiograma con dobutamina',
    5,
    'Indicaciones para el paciente que realizará un ecocardiograma con dobutamina.',
    'ejemplo.pdf'
),

(
    'Indicaciones para pacientes en tratamiento con warfarina',
    5,
    'Indicaciones para pacientes en tratamiento con warfarina.',
    'ejemplo.pdf'
),

(
    'Indicaciones ecocardiograma transesofágico',
    5,
    'Indicaciones para pacientes que realizarán un ecocardiograma transesofágico.',
    'ejemplo.pdf'
),

(
    'Pauta para pacientes ostomizados',
    15,
    'Cuidados y recomendaciones para pacientes ostomizados.',
    'ejemplo.pdf'
),

(
    'Prevención de infecciones',
    18,
    'Recomendaciones generales para prevenir infecciones.',
    'ejemplo.pdf'
),

(
    'Plan de alta de enfermería',
    1,
    'Indicaciones para continuar los cuidados luego del alta.',
    'ejemplo.pdf'
);


-- ============================================================
-- CONFIGURACION DE ENCUESTAS
-- ============================================================

INSERT IGNORE INTO encuestas_config(
    nombre,
    descripcion,
    activa
) VALUES

(
    'Evaluar Atención General',
    'Queremos conocer cómo fue la atención recibida.',
    1
),

(
    'Evaluar Instalaciones',
    'Queremos conocer su opinión sobre las instalaciones.',
    1
);


-- ============================================================
-- TRASLADO DE PRUEBA
-- ============================================================

INSERT IGNORE INTO traslados(
    paciente,
    chofer,
    enfermero,
    vehiculo,
    origen,
    destino,
    estado
) VALUES

(
    'Paciente de prueba',
    'Juan Pérez',
    'María Gómez',
    'Ambulancia 01',
    'Hospital de Clínicas',
    'Clínica Central',
    'En camino'
);


-- ============================================================
-- VISTA: RESUMEN DE ENCUESTAS
-- ============================================================

CREATE OR REPLACE VIEW resumen_encuestas AS

SELECT
    c.id,
    c.nombre,
    COUNT(e.id) AS respuestas,
    ROUND(AVG(e.puntaje), 2) AS promedio

FROM encuestas_config c

LEFT JOIN encuestas e
    ON e.encuesta_id = c.id

GROUP BY
    c.id,
    c.nombre;
