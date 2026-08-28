<?php
require __DIR__ . '/logica/autenticacion.php';
iniciarSesion();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: presentacion/index.php'); exit; }
$usuario = trim($_POST['usuario'] ?? '');
$clave = $_POST['clave'] ?? '';
if ($usuario === '' || $clave === '' || !iniciarSesionUsuario($usuario, $clave)) { header('Location: presentacion/index.php?error=1'); exit; }
header('Location: presentacion/inicio.php'); exit;
