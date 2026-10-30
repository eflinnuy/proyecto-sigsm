<?php
/**
 * Configuración y apertura de la conexión MySQL mediante PDO.
 * ERRMODE_EXCEPTION permite que los errores de persistencia puedan
 * ser detectados por las capas superiores.
 */
$servidor = getenv('DB_HOST') ?: 'localhost';
$base = getenv('DB_NAME') ?: 'sigsm';
$usuarioBD = getenv('DB_USER') ?: 'root';
$claveBD = getenv('DB_PASSWORD') ?: '';
try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4", $usuarioBD, $claveBD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die('No se puede conectar con la base de datos. Revisa datos/conexion.php y el SQL.');
}
