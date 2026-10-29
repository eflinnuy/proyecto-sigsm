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
