# SIGSM

### Sistema de gestión de documentación para pacientes

**SIGSM** es un sistema desarrollado para la **gestión y organización de documentación de pacientes**, utilizando una arquitectura separada por capas para mantener el proyecto organizado y facilitar su mantenimiento.

El panel tiene tres perfiles: **Administrador** (acceso completo y gestión de cuentas), **Médico / Funcionario** (panel, documentos y encuestas) y **Enfermero/a** (panel y creación/seguimiento de traslados). La aplicación conserva la base de datos `sigsm` y sus credenciales configuradas.

---

## Estructura del proyecto

El sistema está organizado en diferentes capas según la función que cumple cada componente:

```text
SIGSM/
├── presentacion/    # Páginas, CSS, JavaScript y vistas
├── logica/          # Reglas, validaciones y coordinación de operaciones
├── datos/            # Conexión y consultas de MySQL
├── modelos/          # Clases del dominio
├── documentos/       # Archivos PDF
└── sql/              # Base de datos y datos de prueba
```

### Capas

| Carpeta | Función |
|---|---|
| `presentacion/` | Páginas, estilos, JavaScript y vistas |
| `logica/` | Reglas, validaciones y coordinación de operaciones |
| `datos/` | Conexión y consultas a MySQL |
| `modelos/` | Clases que representan el dominio del sistema |
| `documentos/` | Almacenamiento de archivos PDF |
| `sql/` | Base de datos y datos de prueba |

---

## Instalación

### 1. Copiar el proyecto

Copiar la carpeta `sigsm` dentro de:

```text
xampp/htdocs/
```

### 2. Crear la base de datos `sigsm`

Abrir **phpMyAdmin** y ejecutar el archivo:

```text
sql/sigsm.sql
```

El esquema crea y selecciona la base `sigsm`.

### 3. Configurar la conexión

Si las credenciales de MySQL son diferentes a las utilizadas por defecto, modificar:

```text
datos/conexion.php
```

### 4. Instalar dependencias PHP

Desde la carpeta `sigsm`, ejecutar:

```bash
composer install
```

Esto instala la biblioteca PHP que genera los códigos QR en formato SVG.

### 5. Iniciar el sistema

Con Apache y MySQL ejecutándose desde XAMPP, acceder a:

**http://localhost/sigsm/**

### Ejecutar con Docker

Desde la carpeta `sigsm`, ejecutar:

```bash
docker compose up --build
```

La aplicación quedará disponible en **http://localhost:3500/**. Docker inicializa la base de datos con `sql/sigsm.sql` al crear el volumen por primera vez. Para cambiar las credenciales de la base de datos, definir las variables `MYSQL_ROOT_PASSWORD` y `SIGSM_DB_PASSWORD` antes de iniciar Compose.

---

## Usuarios de prueba

| Usuario | Contraseña |
|---|---|
| `admin` | `1234` |
| `usuario` | `1234` |

> Estas credenciales son únicamente para realizar pruebas en el entorno de desarrollo.

---

## Flujo del sistema

SIGSM utiliza una separación por capas para organizar el funcionamiento de la aplicación:

```text
Presentación
     ↓
  Lógica
     ↓
   Datos
     ↓
   MySQL
```

Cada capa tiene una responsabilidad específica, permitiendo mantener una estructura más clara y facilitar futuras modificaciones.

---

## EFLINN

**SIGSM** forma parte de los proyectos desarrollados por **EFLINN** como parte de nuestro proyecto de egreso.

<p align="center">
  <strong>EFLINN</strong><br>
  Tecnología para problemas reales.
</p>


## Actualización de seguridad y encuestas

La instalación SQL no elimina tablas existentes. Para actualizar una base existente, ejecuta `sql/migracion_roles_personal.sql` para habilitar los roles y convertir el rol antiguo `usuario` a `funcionario`; Docker aplica también las migraciones de roles, encuestas y cédula al iniciar. La base conserva el nombre `sigsm`. Las respuestas usan escala de 1 a 5, CSRF y token de sesión. Los archivos PDF se sirven a través de `presentacion/descargar_documento.php`, que vuelve a comprobar que el documento siga activo; Apache también bloquea el acceso directo a la carpeta `documentos/`.

## Gestión de funcionarios

Los administradores tienen acceso completo y pueden administrar cuentas; Médico / Funcionario gestiona documentos y encuestas; Enfermero/a consulta el panel y registra y sigue traslados. Solo los administradores pueden abrir **Funcionarios**. Ejecuta [`sql/migracion_roles_personal.sql`](sql/migracion_roles_personal.sql) para convertir las cuentas antiguas `usuario`. Para publicar los archivos y seguir los pasos de actualización, consulta [`docs/actualizacion_funcionarios.md`](docs/actualizacion_funcionarios.md).
