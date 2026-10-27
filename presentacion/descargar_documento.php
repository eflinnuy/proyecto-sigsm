<?php
require_once __DIR__ . '/../datos/conexion.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = $pdo->prepare('SELECT archivo,titulo FROM documentos WHERE id=? AND activo=1');
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);
$base = realpath(__DIR__ . '/../documentos');
$file = $doc ? realpath($base . DIRECTORY_SEPARATOR . basename($doc['archivo'])) : false;
if (!$doc || !$base || !$file || dirname($file) !== $base || !is_file($file) || strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf') {
    http_response_code(404); exit('Documento no encontrado.');
}
header('Content-Type: application/pdf');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="documento.pdf"');
header('Content-Length: ' . filesize($file));
readfile($file);
