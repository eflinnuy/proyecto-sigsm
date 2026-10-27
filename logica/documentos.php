<?php
/**
 * Reglas de negocio relacionadas con documentos.
 * La persistencia se delega al DAO y aquí se mantienen validaciones básicas
 * previas a las operaciones de presentación.
 */
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/DocumentoDAO.php';
require_once __DIR__.'/funciones.php';
function documentosDAO() : DocumentoDAO {
    global $pdo;
    return new DocumentoDAO($pdo);
}
function validarDocumento(string $t, int $c) : bool {
    return $t !== '' && $c>0;
}

function validarPdfSubido(array $archivo): bool {
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($archivo['tmp_name'] ?? '')) return false;
    if (($archivo['size'] ?? 0) < 1 || $archivo['size'] > 5 * 1024 * 1024) return false;
    if (strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION)) !== 'pdf') return false;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($finfo->file($archivo['tmp_name']) !== 'application/pdf') return false;
    $handle = fopen($archivo['tmp_name'], 'rb');
    $signature = $handle ? fread($handle, 5) : '';
    if ($handle) fclose($handle);
    return $signature === '%PDF-';
}
