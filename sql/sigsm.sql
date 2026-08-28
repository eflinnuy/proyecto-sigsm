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

    activo TINYINT(1) NOT NULL DEFAULT 1

);

CREATE TABLE categorias (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE

);

CREATE TABLE documentos (

    id INT AUTO_INCREMENT PRIMARY KEY,

    titulo VARCHAR(150) NOT NULL,

    categoria_id INT NOT NULL,

    descripcion TEXT,

    archivo VARCHAR(255) NOT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(categoria_id) REFERENCES categorias(id)

);

CREATE TABLE servicios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL UNIQUE

);

CREATE TABLE encuestas_config (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    descripcion VARCHAR(500),

    activa TINYINT(1) NOT NULL DEFAULT 1,

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE encuestas (

    id INT AUTO_INCREMENT PRIMARY KEY,

    encuesta_id INT NOT NULL,

    puntaje TINYINT NOT NULL,

    comentario VARCHAR(500),

    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(encuesta_id) REFERENCES encuestas_config(id)

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

    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

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
