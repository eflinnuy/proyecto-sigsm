<?php
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/TrasladoDAO.php';
require_once __DIR__.'/funciones.php';
function trasladosDAO() : TrasladoDAO {
    global $conexion;
    return new TrasladoDAO($conexion);
}
function validarTraslado(array $v) : bool {
    return $v['paciente'] !== '' && $v['chofer'] !== '' && $v['enfermero'] !== '' && $v['origen'] !== '' && $v['destino'] !== '';
}
