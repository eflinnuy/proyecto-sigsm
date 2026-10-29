<?php
/**
 * Gestión de autenticación y autorización.
 * - iniciarSesion(): inicia la sesión cuando es necesario.
 * - iniciarSesionUsuario(): valida credenciales y crea la sesión.
 * - proteger(): exige una sesión válida.
 * - soloPersonal(): permite el acceso al personal autenticado.
 * - soloAdmin(): restringe las operaciones exclusivas de administradores.
 * - cerrarSesion(): elimina la sesión y sus cookies.
 */
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/UsuarioDAO.php';
// Inicializa la sesión solo cuando todavía no existe una sesión activa.
function iniciarSesion() : void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ini_set('session.cookie_secure', '1');
        session_start();
    }
}
// Busca el usuario activo, verifica el hash de la contraseña y crea la sesión.
function iniciarSesionUsuario(string $u, string $c) : bool {
    global $pdo;
    $p = (new UsuarioDAO($pdo))->buscarPorUsuario($u);
    if (!$p || !password_verify($c, $p['clave'])) return false;
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
// Permite el acceso a las funciones del panel a administradores y funcionarios.
function soloPersonal() : void {
    proteger();
    if (!in_array($_SESSION['rol'] ?? '', ['admin', 'usuario', 'funcionario'], true)) {
        header('Location: ../portal.php');
        exit;
    }
}
// Restringe operaciones reservadas al administrador, como dar de baja documentos.
function soloAdmin() : void {
    proteger();
    if (($_SESSION['rol'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('No tienes permiso para realizar esta operación.');
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
