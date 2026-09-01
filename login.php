<?php
/**
 * Punto de entrada del proceso de autenticación.
 * Recibe las credenciales, valida el método HTTP y delega la comprobación
 * de usuario/contraseña a la capa de lógica.
 */
require __DIR__ . '/logica/autenticacion.php';
iniciarSesion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: presentacion/index.php');
    exit;
}
$usuario = trim($_POST['usuario'] ?? '');
$clave = $_POST['clave'] ?? '';
if ($usuario === '' || $clave === '' || !iniciarSesionUsuario($usuario, $clave)) {
    header('Location: presentacion/index.php?error=1');
    exit;
}
header('Location: presentacion/inicio.php');
exit;
