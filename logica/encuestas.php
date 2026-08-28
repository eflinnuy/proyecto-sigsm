<?php
require_once __DIR__.'/../datos/conexion.php'; require_once __DIR__.'/../datos/EncuestaDAO.php'; require_once __DIR__.'/funciones.php';
function encuestasDAO():EncuestaDAO{global $conexion;return new EncuestaDAO($conexion);}
