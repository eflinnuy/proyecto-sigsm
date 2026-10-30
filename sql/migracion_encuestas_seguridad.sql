-- Migración no destructiva e idempotente para bases SIGSM ya existentes.
-- Se puede ejecutar más de una vez; agrega únicamente lo que falte.
USE sigsm;

-- Asociación opcional entre una encuesta y un servicio.
SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'encuestas_config' AND column_name = 'servicio_id') = 0,
    'ALTER TABLE encuestas_config ADD COLUMN servicio_id INT NULL',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.table_constraints
     WHERE constraint_schema = DATABASE() AND table_name = 'encuestas_config'
       AND constraint_name = 'fk_encuestas_servicio' AND constraint_type = 'FOREIGN KEY') = 0,
    'ALTER TABLE encuestas_config ADD CONSTRAINT fk_encuestas_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

-- Corrige textos que quedaron guardados con UTF-8 interpretado como latin1
-- (por ejemplo, "AtenciÃ³n" en vez de "Atención"). Solo transforma filas
-- que contienen los marcadores típicos de esa codificación dañada.
UPDATE categorias
SET nombre = CONVERT(CAST(CONVERT(nombre USING latin1) AS BINARY) USING utf8mb4)
WHERE (nombre LIKE '%Ã%' OR nombre LIKE '%Â%')
  AND CONVERT(CAST(CONVERT(nombre USING latin1) AS BINARY) USING utf8mb4) IS NOT NULL;

UPDATE servicios
SET nombre = CONVERT(CAST(CONVERT(nombre USING latin1) AS BINARY) USING utf8mb4)
WHERE nombre LIKE '%Ã%' OR nombre LIKE '%Â%';

UPDATE documentos
SET titulo = CONVERT(CAST(CONVERT(titulo USING latin1) AS BINARY) USING utf8mb4)
WHERE titulo LIKE '%Ã%' OR titulo LIKE '%Â%';
UPDATE documentos
SET descripcion = CONVERT(CAST(CONVERT(descripcion USING latin1) AS BINARY) USING utf8mb4)
WHERE descripcion LIKE '%Ã%' OR descripcion LIKE '%Â%';

UPDATE encuestas_config
SET nombre = CONVERT(CAST(CONVERT(nombre USING latin1) AS BINARY) USING utf8mb4)
WHERE nombre LIKE '%Ã%' OR nombre LIKE '%Â%';
UPDATE encuestas_config
SET descripcion = CONVERT(CAST(CONVERT(descripcion USING latin1) AS BINARY) USING utf8mb4)
WHERE descripcion LIKE '%Ã%' OR descripcion LIKE '%Â%';
-- Datos requeridos por versiones recientes de la aplicación.
SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'encuestas_config' AND column_name = 'pregunta') = 0,
    'ALTER TABLE encuestas_config ADD COLUMN pregunta VARCHAR(250) NOT NULL DEFAULT ''¿Cómo valora el servicio recibido?''',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

UPDATE encuestas_config
SET pregunta = CONVERT(CAST(CONVERT(pregunta USING latin1) AS BINARY) USING utf8mb4)
WHERE pregunta LIKE '%Ã%' OR pregunta LIKE '%Â%';

SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'encuestas' AND column_name = 'token_envio') = 0,
    'ALTER TABLE encuestas ADD COLUMN token_envio CHAR(64) NULL',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = DATABASE() AND table_name = 'encuestas'
       AND column_name = 'token_envio' AND non_unique = 0) = 0,
    'ALTER TABLE encuestas ADD UNIQUE KEY uq_encuestas_token_envio (token_envio)',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;
