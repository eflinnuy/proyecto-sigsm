-- Actualiza los roles del personal sin eliminar cuentas existentes.
-- Las cuentas antiguas con rol "usuario" pasan a Médico / Funcionario.
USE sigsm;

ALTER TABLE usuarios
    MODIFY rol ENUM('admin','usuario','funcionario','enfermero') NOT NULL DEFAULT 'funcionario';

UPDATE usuarios SET rol='funcionario' WHERE rol='usuario';

ALTER TABLE usuarios
    MODIFY rol ENUM('admin','funcionario','enfermero') NOT NULL DEFAULT 'funcionario';
