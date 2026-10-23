<?php
/**
 * Genera códigos QR como SVG para mostrarlos directamente en el navegador.
 * La biblioteca se instala con Composer; no requiere la extensión GD.
 */
require_once __DIR__ . '/../vendor/autoload.php';

function generarQrSvg(string $contenido): string {
    return (new \chillerlan\QRCode\QRCode())->render($contenido);
}
