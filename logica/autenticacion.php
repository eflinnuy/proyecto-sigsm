<?php
/**
 * Gestión de autenticación y autorización.
 * - iniciarSesion(): inicia la sesión cuando es necesario.
 * - iniciarSesionUsuario(): valida credenciales y crea la sesión.
 * - proteger(): exige una sesión válida.
 * - soloAdmin(): restringe las operaciones administrativas.
 * - cerrarSesion(): elimina la sesión y sus cookies.
 */
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/UsuarioDAO.php';
// Inicializa la sesión solo cuando todavía no existe una sesión activa.
function iniciarSesion() : void {
    if (session_status() === PHP_SESSION_NONE)session_start();
}
// Busca el usuario activo, verifica el hash de la contraseña y crea la sesión.
function iniciarSesionUsuario(string $u, string $c) : bool {
    global $conexion;
    $p = (new UsuarioDAO($conexion))->buscarPorUsuario($u);
    if (!$p || !password_verify($c, $p['clave'])) return false;
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $p['id'];
    $_SESSION['nombre'] = $p['nombre'];
    $_SESSION['rol'] = $p['rol'];
    return true;
}
// Protege cualquier pantalla que requiera autenticación.
function proteger() : void {
    iniciarSesion();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }
}
// Agrega la autorización específica para el rol administrador.
function soloAdmin() : void {
    proteger();
    if ($_SESSION['rol'] !== 'admin') {
        header('Location: inicio.php');
        exit;
    }
}
// Limpia datos de sesión y elimina la cookie asociada.
function cerrarSesion() : void {
    iniciarSesion();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
