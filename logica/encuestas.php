<?php
/**
 * Reglas de negocio relacionadas con encuestas.
 * Centraliza la creación del DAO para evitar repetir la construcción
 * de la conexión en las pantallas.
 */
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/EncuestaDAO.php';
require_once __DIR__.'/funciones.php';
function encuestasDAO() : EncuestaDAO {
    global $conexion;
    return new EncuestaDAO($conexion);
}
