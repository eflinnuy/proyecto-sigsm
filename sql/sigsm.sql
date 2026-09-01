CREATE DATABASE IF NOT EXISTS sigsm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sigsm;

DROP VIEW IF EXISTS resumen_encuestas;

DROP TABLE IF EXISTS encuestas, encuestas_config, traslados, documentos, servicios, categorias, usuarios;

CREATE TABLE usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    usuario VARCHAR(50) NOT NULL UNIQUE,

    clave VARCHAR(255) NOT NULL,

    rol ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',

    activo TINYINT(1) NOT NULL DEFAULT 1,

    -- Restricciones de negocio: se evita guardar datos textuales vacíos
    -- y se limita el indicador de actividad a los valores 0/1.
    CONSTRAINT chk_usuarios_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2),
    CONSTRAINT chk_usuarios_activo CHECK (activo IN (0,1))

);

CREATE TABLE categorias (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    -- Una categoría debe contener texto significativo.
    CONSTRAINT chk_categorias_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);

CREATE TABLE documentos (

    id INT AUTO_INCREMENT PRIMARY KEY,

    titulo VARCHAR(150) NOT NULL,

    categoria_id INT NOT NULL,

    descripcion TEXT,

    archivo VARCHAR(255) NOT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(categoria_id) REFERENCES categorias(id),

    -- Reglas de negocio que no se deducen de PK/FK/UNIQUE.
    CONSTRAINT chk_documentos_titulo CHECK (CHAR_LENGTH(TRIM(titulo)) >= 3),
    CONSTRAINT chk_documentos_archivo CHECK (CHAR_LENGTH(TRIM(archivo)) >= 1),
    CONSTRAINT chk_documentos_activo CHECK (activo IN (0,1))

);

CREATE TABLE servicios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE,

    CONSTRAINT chk_servicios_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) >= 2)

);

CREATE TABLE encuestas_config (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    descripcion VARCHAR(500),

    activa TINYINT(1) NOT NULL DEFAULT 1,

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_encuestas_config_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) >= 3),
    CONSTRAINT chk_encuestas_config_activa CHECK (activa IN (0,1))

);

CREATE TABLE encuestas (

    id INT AUTO_INCREMENT PRIMARY KEY,

    encuesta_id INT NOT NULL,

    puntaje TINYINT NOT NULL,

    comentario VARCHAR(500),

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(encuesta_id) REFERENCES encuestas_config(id),

    -- Regla de negocio: las encuestas utilizan una escala de 1 a 5.
    CONSTRAINT chk_encuestas_puntaje CHECK (puntaje BETWEEN 1 AND 5)

);

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

    -- Reglas de negocio: campos obligatorios no pueden ser solo espacios,
    -- el estado debe pertenecer al flujo definido y la llegada no puede
    -- ocurrir antes que la salida.
    CONSTRAINT chk_traslados_paciente CHECK (CHAR_LENGTH(TRIM(paciente)) >= 2),
    CONSTRAINT chk_traslados_chofer CHECK (CHAR_LENGTH(TRIM(chofer)) >= 2),
    CONSTRAINT chk_traslados_enfermero CHECK (CHAR_LENGTH(TRIM(enfermero)) >= 2),
    CONSTRAINT chk_traslados_origen CHECK (CHAR_LENGTH(TRIM(origen)) >= 2),
    CONSTRAINT chk_traslados_destino CHECK (CHAR_LENGTH(TRIM(destino)) >= 2),
    CONSTRAINT chk_traslados_vehiculo CHECK (vehiculo IS NULL OR CHAR_LENGTH(TRIM(vehiculo)) >= 2),
    CONSTRAINT chk_traslados_estado CHECK (estado IN ('Solicitado','En camino','Realizado','Cancelado')),
    CONSTRAINT chk_traslados_fechas CHECK (salida IS NULL OR llegada IS NULL OR llegada >= salida)

);

/*
===============================================================================
RESTRICCIONES NO ESTRUCTURALES / REGLAS DE NEGOCIO
===============================================================================
Estas restricciones representan condiciones del dominio que van más allá de
la identidad o relación entre tablas. Se implementan mediante CHECK para que
la base de datos también proteja la integridad cuando los datos se insertan
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
   - paciente, chofer, enfermero, origen y destino deben contener texto
     significativo.
   - vehículo, cuando se informa, no puede estar compuesto solo por espacios.
   - estado queda limitado al flujo: Solicitado, En camino, Realizado o
     Cancelado.
   - si se informan salida y llegada, llegada no puede ser anterior a salida.

Estas reglas complementan las restricciones estructurales existentes
(PRIMARY KEY, FOREIGN KEY, UNIQUE, NOT NULL, ENUM y DEFAULT).

Compatibilidad:
Las restricciones CHECK son aplicadas por MySQL 8.0.16+ y por versiones
modernas de MariaDB. En motores antiguos que no las ejecuten, las validaciones
de PHP continúan siendo necesarias y deben mantenerse.
===============================================================================
*/

-- Contraseña de prueba: 1234. 
INSERT INTO usuarios(nombre,usuario,clave,rol) VALUES
('Administrador del sistema','admin','$2y$12$3opYqVWebTuX2SGqIngE1.UcZFIT9qqxWpHbFGJrpdf.X7hqEyHfW','admin'),
('Usuario básico','usuario','$2y$12$3opYqVWebTuX2SGqIngE1.UcZFIT9qqxWpHbFGJrpdf.X7hqEyHfW','usuario');

INSERT INTO categorias(nombre) VALUES

    ('Enfermería'),

    ('Nefrología y trasplante'),

    ('Estudios'),

    ('Prevención'),

    ('Información general'),

    ('Cardiología'),

    ('Ginecobstetricia'),

    ('Urología'),

    ('Gastroenterología')

;

INSERT INTO servicios(nombre) VALUES

    ('Atención al paciente'),

    ('Enfermería'),

    ('Traslados'),

    ('Información')

;

INSERT INTO documentos(titulo,categoria_id,descripcion,archivo) VALUES

    ('Preparación para estudios imagenológicos',3,'Información para preparar al paciente antes de un estudio.','ejemplo.pdf'),

    ('Estudios diagnósticos con pertecneciato',3,'Información general para el estudio.','ejemplo.pdf'),

    ('Centellograma de perfusión miocárdica',3,'Información para pacientes.','ejemplo.pdf'),

    ('Indicaciones ecocardiograma con dobutamina',6,'Indicaciones para el paciente.','ejemplo.pdf'),

    ('Indicaciones ecocardiograma transesofágico',6,'Indicaciones para el paciente.','ejemplo.pdf'),

    ('Indicaciones para pacientes en tratamiento con warfarina',6,'Recomendaciones generales.','ejemplo.pdf'),

    ('Indicaciones de interrupción voluntaria del embarazo',7,'Información para el paciente.','ejemplo.pdf'),

    ('Prostatectomía radical - información para el paciente',8,'Información general.','ejemplo.pdf'),

    ('Pauta para pacientes ostomizados',9,'Cuidados y recomendaciones.','ejemplo.pdf'),

    ('Prevención de infecciones',4,'Recomendaciones generales para prevenir infecciones.','ejemplo.pdf'),

    ('Plan de alta de enfermería',1,'Indicaciones para continuar los cuidados luego del alta.','ejemplo.pdf')

;

INSERT INTO encuestas_config(nombre,descripcion,activa) VALUES

    ('Evaluar Atención General','Queremos conocer cómo fue la atención recibida.',1),

    ('Evaluar Instalaciones','Queremos conocer su opinión sobre las instalaciones.',1)

;

INSERT INTO traslados(paciente,chofer,enfermero,vehiculo,origen,destino,estado) VALUES

    ('Paciente de prueba','Juan Pérez','María Gómez','Ambulancia 01','Hospital de Clínicas','Clínica Central','En camino')

;

CREATE OR REPLACE VIEW resumen_encuestas AS SELECT c.id,c.nombre,COUNT(e.id) respuestas,ROUND(AVG(e.puntaje),2) promedio
FROM encuestas_config c
LEFT JOIN encuestas e ON e.encuesta_id=c.id
GROUP BY c.id,c.nombre;
