<?php
/**
 * Configuración y apertura de la conexión MySQL mediante PDO.
 * ERRMODE_EXCEPTION permite que los errores de persistencia puedan
 * ser detectados por las capas superiores.
 */
$servidor = 'localhost';
$base = 'sigsm';
$usuarioBD = 'root';
$claveBD = '';
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4", $usuarioBD, $claveBD);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die('No se pudo conectar con la base de datos. Revisa datos/conexion.php y el SQL.');
}
