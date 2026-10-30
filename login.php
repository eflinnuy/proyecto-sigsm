<?php
/**
 * Punto de entrada del proceso de autenticación.
 * Recibe las credenciales, valida el método HTTP y delega la comprobación
 * de usuario/contraseña a la capa de lógica.
 */
require __DIR__ . '/logica/funciones.php';
iniciarSesion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: presentacion/index.php');
    exit;
}
validarCsrf();
$usuario = is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '';
$clave = is_string($_POST['clave'] ?? null) ? $_POST['clave'] : '';
if (!validarCaptcha($_POST['captcha'] ?? null) || $usuario === '' || $clave === '' || !iniciarSesionUsuario($usuario, $clave)) {
    header('Location: presentacion/index.php?error=1');
    exit;
}
// Todo el personal autorizado entra al panel; los permisos se verifican por acción.
header('Location: presentacion/inicio.php');
exit;
