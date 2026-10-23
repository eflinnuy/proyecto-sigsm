# SIGSM

### Sistema de gestión de documentación para pacientes

**SIGSM** es un sistema desarrollado para la **gestión y organización de documentación de pacientes**, utilizando una arquitectura separada por capas para mantener el proyecto organizado y facilitar su mantenimiento.

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

### 2. Crear la base de datos

Abrir **phpMyAdmin** y ejecutar el archivo:

```text
sql/sigsm.sql
```

### 3. Configurar la conexión

Si las credenciales de MySQL son diferentes a las utilizadas por defecto, modificar:

```text
datos/conexion.php
```

### 4. Iniciar el sistema

Con Apache y MySQL ejecutándose desde XAMPP, acceder a:

**http://localhost/sigsm/**

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
x