# Actualización: gestión de funcionarios

## Archivos incluidos

Copiar o publicar estos archivos del proyecto:

- `login.php` y `presentacion/index.php` (actualizados: todas las cuentas del personal entran al panel)
- `logica/autenticacion.php` (actualizado: permisos del personal y acción exclusiva del administrador)
- `portal.php`, `presentacion/descargar_documento.php` y `presentacion/ver_publico.php` (actualizados: descarga de PDF desde el portal y la vista pública)
- `datos/FuncionarioDAO.php` (nuevo)
- `presentacion/funcionarios.php` (nuevo)
- `presentacion/inicio.php` (actualizado: acceso para funcionarios)
- `presentacion/documentos.php` (actualizado: funcionarios no pueden dar de baja documentos)
- `presentacion/editar_documento.php` y `presentacion/nuevo_documento.php` (actualizados: acceso para funcionarios)
- `presentacion/ambulancias.php`, `presentacion/nuevo_traslado.php` y `presentacion/editar_traslado.php` (actualizados: acceso para funcionarios)
- `presentacion/encuestas_admin.php` (actualizado: acceso para funcionarios)
- `presentacion/vistas/encabezado.php` (actualizado: navegación del panel para el personal)
- `presentacion/css/estilo.css` (actualizado: estilos de acciones)
- `README.md` (actualizado)
- `docs/actualizacion_funcionarios.md` (nuevo)

No se requiere ejecutar cambios SQL: la función usa la tabla `usuarios` ya incluida en SIGSM.

## Publicar en un servidor con XAMPP

1. En phpMyAdmin, selecciona la base `sigsm` y usa **Exportar** para guardar una copia de seguridad antes de publicar.
2. Copia la carpeta del proyecto a una carpeta temporal o guarda una copia de los archivos indicados arriba. No reemplaces la base de datos.
3. Copia los archivos incluidos manteniendo sus rutas dentro de `htdocs/sigsm/`. Si publicas por Git, realiza primero los pasos de la sección siguiente en tu equipo de desarrollo.
4. Confirma que Apache y MySQL estén iniciados en XAMPP y abre `http://localhost/sigsm/`.
5. Inicia sesión con una cuenta que tenga el rol `admin` y abre **Funcionarios** desde la barra superior.
6. Crea una cuenta de prueba con una contraseña de al menos 8 caracteres; cierra sesión y comprueba que esa cuenta puede ingresar.
7. Inicia sesión con la cuenta de prueba de rol **Funcionario**. Comprueba que el acceso lleva al panel, no al portal, y que puede abrir Funcionarios, crear y modificar cuentas, gestionar documentos, encuestas y traslados.
8. Comprueba que esa cuenta no ve **Dar de baja** en Documentos y que un intento de enviar la acción directamente a `presentacion/eliminar_documento.php` devuelve acceso denegado.
9. Desactiva la cuenta de prueba y confirma que ya no puede iniciar sesión. Reactívala si debe quedar disponible.

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

- **Crear:** completa nombre, usuario, contraseña inicial y permiso. La contraseña debe tener 8 caracteres como mínimo.
- **Modificar:** selecciona **Modificar**. Si la contraseña nueva queda vacía, se conserva la existente.
- **Baja:** selecciona **Desactivar**. La cuenta queda inhabilitada para iniciar sesión y sus registros se conservan.
- **Reactivar:** selecciona **Activar**.
- No es posible desactivar la cuenta con la que se inició sesión ni dejar el sistema sin administradores activos.
- Los funcionarios pueden usar el panel, gestionar cuentas y documentos, pero solo los administradores pueden dar de baja documentos. El servidor también bloquea esa acción si un funcionario intenta abrir directamente la ruta.
