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

/** Genera una pregunta sencilla para verificar el envío de formularios públicos. */
function preguntaCaptcha() : string {
    iniciarSesion();
    if (!isset($_SESSION['captcha_a'], $_SESSION['captcha_b'])) {
        $_SESSION['captcha_a'] = random_int(2, 9);
        $_SESSION['captcha_b'] = random_int(1, 9);
    }
    return $_SESSION['captcha_a'].' + '.$_SESSION['captcha_b'];
}

function validarCaptcha($respuesta) : bool {
    iniciarSesion();
    $valido = isset($_SESSION['captcha_a'], $_SESSION['captcha_b'])
        && is_scalar($respuesta)
        && hash_equals((string)((int)$_SESSION['captcha_a'] + (int)$_SESSION['captcha_b']), trim((string)$respuesta));
    unset($_SESSION['captcha_a'], $_SESSION['captcha_b']);
    return $valido;
}
