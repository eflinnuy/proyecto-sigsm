RESTRICCIONES NO ESTRUCTURALES IMPLEMENTADAS EN LA BASE DE DATOS
Sistema SIGSM

Estas restricciones complementan las restricciones estructurales de la base de datos
(PK, FK, UNIQUE, NOT NULL, etc.) y tienen como objetivo controlar reglas de negocio
y valores válidos.

1. PUNTAJE DE ENCUESTAS
- El puntaje de una encuesta debe estar comprendido entre 1 y 5.
- No se permiten valores menores que 1 ni mayores que 5.
- Restricción implementada mediante CHECK.

2. ESTADOS DE LOS TRASLADOS
- El estado de un traslado solamente puede tomar valores previamente definidos.
- Los estados permitidos son:
  - pendiente
  - confirmado
  - realizado
  - cancelado
- Esto evita que se almacenen estados arbitrarios o inconsistentes.

3. VALIDACIÓN DE FECHAS Y HORARIOS DE TRASLADOS
- La fecha/hora de llegada no puede ser anterior a la fecha/hora de salida.
- Se garantiza que:
  llegada >= salida
- Esto evita registrar traslados con una secuencia temporal incorrecta.

4. CAMPOS DE TEXTO
- Determinados campos de texto no pueden contener únicamente espacios.
- Se utilizan validaciones con TRIM para garantizar que exista contenido real.
- Esto evita registros aparentemente completos pero cuyo contenido sea vacío.

5. CAMPOS DE ESTADO ACTIVO/INACTIVO
- Los campos utilizados para indicar si un registro está activo solamente aceptan:
  - 0 = inactivo
  - 1 = activo
- No se permiten otros valores.

6. LONGITUD MÍNIMA DE DETERMINADOS CAMPOS
- Algunos nombres, títulos y textos deben superar una longitud mínima.
- El objetivo es evitar datos incompletos o demasiado cortos para representar
  correctamente la información.

7. VALIDACIÓN EN DOS CAPAS
- Las restricciones de la base de datos funcionan como segunda línea de defensa.
- El sistema también realiza validaciones desde PHP antes de insertar o actualizar
  información.
- De esta forma, se evita depender exclusivamente de la validación del formulario.

8. OBJETIVO DE ESTAS RESTRICCIONES
Estas reglas buscan mantener la integridad lógica de los datos y evitar situaciones
como:
- Puntajes fuera del rango permitido.
- Estados inexistentes.
- Fechas de traslado incoherentes.
- Campos con espacios en blanco como único contenido.
- Valores inválidos en indicadores de estado.
- Información insuficiente en campos de texto.

NOTA DE COMPATIBILIDAD
Las restricciones CHECK dependen de la versión del motor de base de datos.
Se recomienda utilizar MySQL 8.0.16 o superior, o una versión moderna de MariaDB.

Las validaciones realizadas en PHP se mantienen como una primera capa de control,
por lo que las restricciones de la base de datos no sustituyen las validaciones
de la aplicación, sino que las complementan.
