# Actualización: gestión de funcionarios

## Archivos incluidos

Copiar o publicar estos archivos del proyecto:

- `login.php` y `presentacion/index.php` (actualizados: todas las cuentas del personal entran al panel)
- `presentacion/recuperar.php` (nuevo: restablecimiento local con usuario y cédula)
- `logica/funciones.php` (actualizado: captcha aritmético y funciones auxiliares)
- `datos/UsuarioDAO.php` (actualizado: cambio de contraseña con usuario y cédula)
- `sql/migracion_cedula_funcionarios.sql` (nuevo: agrega el campo único de cédula)
- `sql/sigsm.sql` (actualizado: incluye cédula en instalaciones nuevas)
- `logica/autenticacion.php` (actualizado: permisos del personal y acción exclusiva del administrador)
- `portal.php`, `presentacion/descargar_documento.php` y `presentacion/ver_publico.php` (actualizados: descarga de PDF desde el portal y la vista pública)
- `datos/FuncionarioDAO.php` (nuevo)
- `presentacion/funcionarios.php` (nuevo)
- `presentacion/inicio.php` (actualizado: acceso para funcionarios)
- `presentacion/documentos.php` (actualizado: funcionarios no pueden dar de baja documentos)
- `presentacion/editar_documento.php` y `presentacion/nuevo_documento.php` (actualizados: acceso para funcionarios)
- `presentacion/ambulancias.php`, `presentacion/nuevo_traslado.php` y `presentacion/editar_traslado.php` (actualizados: acceso para funcionarios)
- `presentacion/encuestas_admin.php` (actualizado: acceso para funcionarios)
- `presentacion/vistas/encabezado.php` (actualizado: navegación del panel y menú hamburguesa móvil)
- `presentacion/css/estilo.css` (actualizado: iconos de mostrar contraseña)
- `README.md` (actualizado)
- `docs/actualizacion_funcionarios.md` (nuevo)

## Actualización de base de datos

Esta versión agrega una cédula única a cada cuenta. Antes de publicar los archivos, haz una copia de seguridad de la base `sigsm` y ejecuta una sola vez [`../sql/migracion_cedula_funcionarios.sql`](../sql/migracion_cedula_funcionarios.sql) en phpMyAdmin. Las cuentas existentes quedan inicialmente sin cédula; entra como administrador, abre **Funcionarios** y edita cada cuenta para registrarla. Hasta completar ese paso, esas cuentas no podrán usar la recuperación de contraseña.

## Publicar en un servidor con XAMPP

1. En phpMyAdmin, selecciona la base `sigsm` y usa **Exportar** para guardar una copia de seguridad antes de publicar.
2. Copia la carpeta del proyecto a una carpeta temporal o guarda una copia de los archivos indicados arriba. No reemplaces la base de datos.
3. Copia los archivos incluidos manteniendo sus rutas dentro de `htdocs/sigsm/`. Si publicas por Git, realiza primero los pasos de la sección siguiente en tu equipo de desarrollo.
4. Confirma que Apache y MySQL estén iniciados en XAMPP y abre `http://localhost/sigsm/`.
5. Inicia sesión con una cuenta que tenga el rol `admin` y abre **Funcionarios** desde la barra superior.
6. Completa la cédula de las cuentas existentes. Crea una cuenta de prueba con usuario, cédula y contraseña de al menos 8 caracteres; cierra sesión y comprueba que puede ingresar y que el captcha aparece.
7. En el inicio de sesión, abre **¿Olvidó su contraseña?** y recupera la cuenta de prueba indicando su usuario y cédula. La cédula más el usuario funcionan como verificación de identidad; quien conozca ambos podría cambiar la contraseña, así que limita el acceso a esos datos.
8. Inicia sesión con la cuenta de prueba de rol **Funcionario**. Comprueba que el acceso lleva al panel, no al portal, que puede gestionar documentos, encuestas y traslados, y que no ve ni puede abrir **Funcionarios**.
9. Comprueba que esa cuenta no ve **Dar de baja** en Documentos y que un intento de enviar la acción directamente a `presentacion/eliminar_documento.php` devuelve acceso denegado.
10. Desactiva la cuenta de prueba y confirma que ya no puede iniciar sesión. Reactívala si debe quedar disponible.

## Subir los cambios con Git

Desde una terminal, entra al repositorio `sigsm` y revisa los cambios antes de agregarlos:

```bash
cd C:/xampp/htdocs/sigsm
git status --short
git diff -- README.md login.php logica/autenticacion.php portal.php presentacion
```

Agrega los archivos nuevos y los archivos sin otros cambios previos. En esta copia, `portal.php`, `presentacion/encuestas_admin.php` y `presentacion/nuevo_traslado.php` ya tenían cambios locales antes de esta corrección; usa `git add -p` en esos tres archivos para seleccionar los cambios de esta tarea y conservar separadas las demás modificaciones:

```bash
git add datos/FuncionarioDAO.php docs/actualizacion_funcionarios.md README.md login.php logica/autenticacion.php presentacion/ambulancias.php presentacion/css/estilo.css presentacion/documentos.php presentacion/descargar_documento.php presentacion/editar_documento.php presentacion/editar_traslado.php presentacion/funcionarios.php presentacion/index.php presentacion/inicio.php presentacion/nuevo_documento.php presentacion/vistas/encabezado.php presentacion/ver_publico.php
git add -p portal.php presentacion/encuestas_admin.php presentacion/nuevo_traslado.php
git diff --cached
git commit -m "Agregar gestión de funcionarios"
git push origin <nombre-de-tu-rama>
```

Reemplaza `<nombre-de-tu-rama>` por la rama real acordada en el repositorio. Si el equipo despliega desde una rama de publicación, integra el commit siguiendo su flujo habitual y copia/publica esos mismos archivos en el servidor. Revisa el diff staged antes del commit. No uses `git add .` sin revisar el estado: puede incluir PDFs u otros cambios locales que no forman parte de esta corrección.

## Uso del módulo

- **Crear:** completa nombre, usuario, cédula, contraseña inicial y permiso. El usuario acepta nombres con espacios y otros caracteres, hasta 50 caracteres; la cédula debe ser única y tener entre 5 y 20 letras o números (puede incluir punto o guion). La contraseña debe tener 8 caracteres como mínimo.
- **Modificar:** selecciona **Modificar**. Si la contraseña nueva queda vacía, se conserva la existente.
- **Baja:** selecciona **Desactivar**. La cuenta queda inhabilitada para iniciar sesión y sus registros se conservan.
- **Reactivar:** selecciona **Activar**.
- **Recuperar contraseña:** en el login, la persona ingresa su usuario y cédula registrados y define una contraseña nueva. No se envían correos.
- El captcha de suma se solicita al iniciar sesión y al restablecer la contraseña.
- No es posible desactivar la cuenta con la que se inició sesión ni dejar el sistema sin administradores activos.
- Los funcionarios pueden usar el panel y gestionar documentos, encuestas y traslados. Solo los administradores pueden gestionar cuentas o dar de baja documentos; el servidor bloquea las rutas administrativas para el personal sin rol de administrador.
- En pantallas de hasta 750 píxeles, el navbar se oculta tras el botón de menú hamburguesa.
