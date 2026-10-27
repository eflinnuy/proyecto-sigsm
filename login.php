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
$usuario = trim($_POST['usuario'] ?? '');
$clave = $_POST['clave'] ?? '';
if ($usuario === '' || $clave === '' || !iniciarSesionUsuario($usuario, $clave)) {
    header('Location: presentacion/index.php?error=1');
    exit;
}
if (($_SESSION['rol'] ?? '') === 'admin') {
    header('Location: presentacion/inicio.php');
} else {
    header('Location: portal.php');
}
exit;
