<?php
require __DIR__ . '/logica/funciones.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
validarCsrf();
cerrarSesion();
header('Location: presentacion/index.php');
exit;
