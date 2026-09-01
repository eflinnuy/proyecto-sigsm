<?php
/**
 * Reglas de negocio relacionadas con documentos.
 * La persistencia se delega al DAO y aquí se mantienen validaciones básicas
 * previas a las operaciones de presentación.
 */
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/DocumentoDAO.php';
require_once __DIR__.'/funciones.php';
function documentosDAO() : DocumentoDAO {
    global $conexion;
    return new DocumentoDAO($conexion);
}
function validarDocumento(string $t, int $c) : bool {
    return $t !== '' && $c>0;
}
