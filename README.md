# SIGSM

Sistema de gestión de documentación para pacientes.

## Estructura

- `presentacion/`: páginas, CSS, JavaScript y vistas.
- `logica/`: reglas, validaciones y coordinación de operaciones.
- `datos/`: conexión y consultas de MySQL.
- `modelos/`: clases del dominio.
- `documentos/`: archivos PDF.
- `sql/`: base de datos y datos de prueba.

## Instalación

1. Copiar `sigsm` en `htdocs` de XAMPP.
2. Ejecutar `sql/sigsm.sql` en phpMyAdmin.
3. Revisar `datos/conexion.php` si las credenciales de MySQL son diferentes.
4. Abrir `http://localhost/sigsm/`.

Usuarios de prueba: `admin / 1234` y `usuario / 1234`.

Flujo: Presentación → Lógica → Datos → MySQL.
