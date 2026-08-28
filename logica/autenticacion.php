<?php
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/UsuarioDAO.php';
function iniciarSesion() : void {
    if (session_status() === PHP_SESSION_NONE)session_start();
}
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
function proteger() : void {
    iniciarSesion();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: index.php');
        exit;
    }
}
function soloAdmin() : void {
    proteger();
    if ($_SESSION['rol'] !== 'admin') {
        header('Location: inicio.php');
        exit;
    }
}
function cerrarSesion() : void {
    iniciarSesion();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
