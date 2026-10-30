-- Ejecutar una sola vez en la base de datos existente SIGSM.
-- Las cuentas existentes quedarán con cédula NULL hasta que se complete
-- desde Funcionarios; el formulario requiere cédula para crear o editar.
USE sigsm;

ALTER TABLE usuarios
    ADD COLUMN cedula VARCHAR(20) NULL AFTER usuario,
    ADD UNIQUE KEY uq_usuarios_cedula (cedula);
