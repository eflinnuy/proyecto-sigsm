<?php
/**
 * Baja lógica de documentos.
 * No elimina físicamente el registro: cambia su estado activo a 0.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? 0);
$s = $conexion->prepare('UPDATE documentos SET activo=0 WHERE id=?');
$s->execute([$id]);
mensaje('Documento eliminado.');
header('Location: documentos.php');
exit;
 ?>
