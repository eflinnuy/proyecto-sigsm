<?php
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
validarCsrf();
require_once __DIR__ . '/../datos/conexion.php';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$s = $pdo->prepare('UPDATE documentos SET activo=0 WHERE id=?');
$s->execute([$id]);
mensaje('Documento dado de baja.');
header('Location: documentos.php');
exit;
