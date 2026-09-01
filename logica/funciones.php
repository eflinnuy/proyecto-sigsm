<?php
/**
 * Funciones auxiliares comunes.
 * Incluye escape de salida HTML y el mecanismo de mensajes de sesión
 * utilizado entre redirecciones.
 */
require_once __DIR__.'/autenticacion.php';
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
