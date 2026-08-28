<?php
$servidor='localhost'; $base='sigsm'; $usuarioBD='root'; $claveBD='';
try { $conexion=new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4",$usuarioBD,$claveBD); $conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); }
catch(PDOException $e){ die('No se pudo conectar con la base de datos. Revisa datos/conexion.php y el SQL.'); }
