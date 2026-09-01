RESTRICCIONES NO ESTRUCTURALES DE SIGSM

OBJETIVO

Las restricciones no estructurales representan reglas de negocio que no quedan
completamente expresadas mediante claves primarias, claves foráneas, unicidad
o nulabilidad. En SIGSM se refuerzan directamente en MySQL mediante CHECK.


REGLAS IMPLEMENTADAS

Tabla: usuarios
- Regla: nombre debe tener al menos 2 caracteres significativos.
- Implementación: chk_usuarios_nombre

- Regla: activo solo puede ser 0 o 1.
- Implementación: chk_usuarios_activo


Tabla: categorias
- Regla: nombre debe tener al menos 2 caracteres significativos.
- Implementación: chk_categorias_nombre


Tabla: servicios
- Regla: nombre debe tener al menos 2 caracteres significativos.
- Implementación: chk_servicios_nombre


Tabla: documentos
- Regla: titulo debe tener al menos 3 caracteres.
- Implementación: chk_documentos_titulo

- Regla: archivo no puede estar vacío.
- Implementación: chk_documentos_archivo

- Regla: activo solo puede ser 0 o 1.
- Implementación: chk_documentos_activo


Tabla: encuestas_config
- Regla: nombre debe tener al menos 3 caracteres significativos.
- Implementación: chk_encuestas_config_nombre

- Regla: activa solo puede ser 0 o 1.
- Implementación: chk_encuestas_config_activa


Tabla: encuestas
- Regla: puntaje debe estar entre 1 y 5.
- Implementación: chk_encuestas_puntaje


Tabla: traslados
- Regla: los campos obligatorios deben contener texto significativo.
- Implementación: chk_traslados_*

- Regla: vehiculo, si existe, no puede estar compuesto solo por espacios.
- Implementación: chk_traslados_vehiculo

- Regla: el estado debe pertenecer al flujo permitido.
- Implementación: chk_traslados_estado

- Regla: llegada no puede ser anterior a salida.
- Implementación: chk_traslados_fechas


POR QUÉ SE CONSIDERAN REGLAS DE NEGOCIO

Por ejemplo, que una respuesta de encuesta tenga un puntaje de 1 a 5 no es
una condición necesaria para que la fila exista ni una relación con otra
tabla. Es una decisión funcional del sistema.

Lo mismo ocurre con los estados válidos de un traslado y con el orden
cronológico de salida/llegada.


DEFENSA EN PROFUNDIDAD

Las pantallas PHP continúan realizando validaciones antes de guardar datos.

Los CHECK agregan una segunda capa de protección: si otro proceso inserta o
actualiza directamente la base de datos, las reglas también son evaluadas por
el motor.


COMPATIBILIDAD

Se recomienda MySQL 8.0.16+ o una versión moderna de MariaDB para garantizar
la aplicación de CHECK.

En motores antiguos, no se debe asumir que los CHECK serán ejecutados; por eso
las validaciones de PHP deben conservarse.

Las reglas y sus comentarios técnicos también están documentados en:

sql/sigsm.sql
