-- Migración repetible para la base de datos existente SIGSM.
-- Las cuentas existentes quedarán con cédula NULL hasta que se complete
-- desde Funcionarios; el formulario requiere cédula para crear o editar.
USE sigsm;

SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.columns
     WHERE table_schema = DATABASE() AND table_name = 'usuarios' AND column_name = 'cedula') = 0,
    'ALTER TABLE usuarios ADD COLUMN cedula VARCHAR(20) NULL AFTER usuario',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

SET @migration_sql = IF(
    (SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = DATABASE() AND table_name = 'usuarios' AND index_name = 'uq_usuarios_cedula') = 0,
    'ALTER TABLE usuarios ADD UNIQUE KEY uq_usuarios_cedula (cedula)',
    'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;
