<?php
/**
 * Funciones auxiliares comunes.
 * Incluye escape de salida HTML y el mecanismo de mensajes de sesión
 * utilizado entre redirecciones.
 */
require_once __DIR__.'/autenticacion.php';
function tokenCsrf() : string {
    iniciarSesion();
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function campoCsrf() : string {
    return '<input type="hidden" name="csrf_token" value="'.limpiar(tokenCsrf()).'">';
}
function validarCsrf() : void {
    iniciarSesion();
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('La sesión del formulario venció. Recargue la página e inténtelo nuevamente.');
    }
}
function limpiar($t) : string {
    return htmlspecialchars($t ?? '', ENT_QUOTES, 'UTF-8');
}
function mensaje(string $t, string $tipo = 'bien') : void {
    iniciarSesion();
    $_SESSION['mensaje'] = ['texto' => $t, 'tipo' => $tipo];
}
function mostrarMensaje() : void {
    iniciarSesion();
    if (!empty($_SESSION['mensaje'])) {
        $m = $_SESSION['mensaje'];
        echo '<div class="mensaje '.limpiar($m['tipo']).'">'.limpiar($m['texto']).'</div>';
        unset($_SESSION['mensaje']);
    }
}
