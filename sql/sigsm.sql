CREATE DATABASE IF NOT EXISTS sigsm
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sigsm;

-- ============================================================
-- LIMPIEZA DE OBJETOS ANTERIORES
-- ============================================================

DROP VIEW IF EXISTS resumen_encuestas;

DROP TABLE IF EXISTS
    encuestas,
    encuestas_config,
    traslados,
    documentos,
    servicios,
    categorias,
    usuarios;


-- ============================================================
-- TABLA: USUARIOS
-- ============================================================

CREATE TABLE usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    usuario VARCHAR(50) NOT NULL UNIQUE,

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

CREATE TABLE categorias (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    -- Una categoría debe contener texto significativo.
    CONSTRAINT chk_categorias_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);


-- ============================================================
-- TABLA: DOCUMENTOS
-- ============================================================

CREATE TABLE documentos (

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

CREATE TABLE servicios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    CONSTRAINT chk_servicios_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);


-- ============================================================
-- TABLA: CONFIGURACION DE ENCUESTAS
-- ============================================================

CREATE TABLE encuestas_config (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    descripcion VARCHAR(500),

    activa TINYINT(1) NOT NULL DEFAULT 1,

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_encuestas_config_nombre
        CHECK (CHAR_LENGTH(TRIM(nombre)) >= 3),

    CONSTRAINT chk_encuestas_config_activa
        CHECK (activa IN (0,1))

);


-- ============================================================
-- TABLA: ENCUESTAS
-- ============================================================

CREATE TABLE encuestas (

    id INT AUTO_INCREMENT PRIMARY KEY,

    encuesta_id INT NOT NULL,

    puntaje TINYINT NOT NULL,

    comentario VARCHAR(500),

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

CREATE TABLE traslados (

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


-- ============================================================
-- RESTRICCIONES NO ESTRUCTURALES / REGLAS DE NEGOCIO
-- ============================================================

/*
Estas restricciones representan condiciones del dominio que
van más allá de la identidad o relación entre tablas.

Se implementan mediante CHECK para que la base de datos
también proteja la integridad cuando los datos se insertan
fuera de la aplicación PHP.

1. Usuarios
   - nombre debe contener al menos 2 caracteres no vacíos.
   - activo solo admite 0 o 1.

2. Categorías y servicios
   - nombre debe contener al menos 2 caracteres significativos.

3. Documentos
   - título debe contener al menos 3 caracteres.
   - archivo no puede estar vacío.
   - activo solo admite 0 o 1.

4. Configuración de encuestas
   - nombre debe contener al menos 3 caracteres significativos.
   - activa solo admite 0 o 1.

5. Respuestas de encuestas
   - puntaje debe estar entre 1 y 5.

6. Traslados
   - paciente, chofer, enfermero, origen y destino deben contener
     texto significativo.
   - vehículo, cuando se informa, no puede estar compuesto solo
     por espacios.
   - estado queda limitado al flujo:
     Solicitado, En camino, Realizado o Cancelado.
   - si se informan salida y llegada, llegada no puede ser
     anterior a salida.
*/


-- ============================================================
-- USUARIOS DE PRUEBA
-- Contraseña: 1234
-- ============================================================

INSERT INTO usuarios(nombre, usuario, clave, rol) VALUES

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

INSERT INTO categorias(nombre) VALUES

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

INSERT INTO servicios(nombre) VALUES

    ('Atención al paciente'),

    ('Enfermería'),

    ('Traslados'),

    ('Información');


-- ============================================================
-- DOCUMENTOS DE EJEMPLO
-- ============================================================

INSERT INTO documentos(
    titulo,
    categoria_id,
    descripcion,
    archivo
) VALUES

(
    'Indicaciones de interrupción voluntaria del embarazo',
    30,
    'Información para el paciente sobre interrupción voluntaria del embarazo.',
    'doc_6a985e11a3723.pdf'
),

(
    'Prostatectomía radical - indicaciones e información para el paciente',
    38,
    'Indicaciones e información para pacientes sometidos a prostatectomía radical.',
    'doc_6a985e11a3723.pdf'
),

(
    'Preparación para estudios imagenológicos',
    43,
    'Información para preparar al paciente antes de un estudio imagenológico.',
    'doc_6a985e11a3723.pdf'
),

(
    'Estudios diagnósticos con pertecneciato',
    41,
    'Información para pacientes sobre estudios diagnósticos con pertecneciato.',
    'doc_6a985e11a3723.pdf'
),

(
    'Información sobre Centellograma de perfusión miocárdica',
    41,
    'Información para el paciente sobre el centellograma de perfusión miocárdica.',
    'doc_6a985e11a3723.pdf'
),

(
    'Indicaciones ecocardiograma con dobutamina',
    5,
    'Indicaciones para el paciente que realizará un ecocardiograma con dobutamina.',
    'doc_6a985e11a3723.pdf'
),

(
    'Indicaciones para pacientes en tratamiento con warfarina',
    5,
    'Indicaciones para pacientes en tratamiento con warfarina.',
    'doc_6a985e11a3723.pdf'
),

(
    'Indicaciones ecocardiograma transesofágico',
    5,
    'Indicaciones para pacientes que realizarán un ecocardiograma transesofágico.',
    'doc_6a985e11a3723.pdf'
),

(
    'Pauta para pacientes ostomizados',
    15,
    'Cuidados y recomendaciones para pacientes ostomizados.',
    'doc_6a985e11a3723.pdf'
),

(
    'Prevención de infecciones',
    18,
    'Recomendaciones generales para prevenir infecciones.',
    'doc_6a985e11a3723.pdf'
),

(
    'Plan de alta de enfermería',
    1,
    'Indicaciones para continuar los cuidados luego del alta.',
    'doc_6a985e11a3723.pdf'
);


-- ============================================================
-- CONFIGURACION DE ENCUESTAS
-- ============================================================

INSERT INTO encuestas_config(
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

INSERT INTO traslados(
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