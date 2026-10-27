-- Ejecutar una sola vez en la base SIGSM ya instalada.
-- Agrega servicio a encuestas y una llave idempotente por sesión para respuestas anónimas.
ALTER TABLE encuestas_config
    ADD COLUMN servicio_id INT NULL,
    ADD CONSTRAINT fk_encuestas_servicio FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE SET NULL;
ALTER TABLE encuestas
    ADD COLUMN token_envio CHAR(64) NULL UNIQUE;
ALTER TABLE encuestas_config
    ADD COLUMN pregunta VARCHAR(250) NOT NULL DEFAULT '¿Cómo valora el servicio recibido?';
