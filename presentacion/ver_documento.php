<?php
/**
 * Visualización de un documento desde el área administrativa.
 */
require_once __DIR__ . '/../logica/funciones.php';
require_once __DIR__ . '/../logica/generador_qr.php';
proteger();
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? 0);
$s = $conexion->prepare('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.id=? AND d.activo=1');
$s->execute([$id]);
$d = $s->fetch(PDO::FETCH_ASSOC);
if (!$d) die('Documento no encontrado.');
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/ver_publico.php?id='.$id;
$qrSvg = generarQrSvg($url);
$titulo = $d['titulo'];
require __DIR__ . '/vistas/encabezado.php';
 ?>
<section class="documento">
    <span class="etiqueta"> <?= limpiar($d['categoria']) ?></span>
    <h1>
        <?= limpiar($d['titulo']) ?>
    </h1>
    <p>
        <?= nl2br(limpiar($d['descripcion'])) ?>
    </p>
    <div class="qr">
        <img class="imagen-qr" src="<?= limpiar($qrSvg) ?>" alt="Código QR para abrir <?= limpiar($d['titulo']) ?>">
        <p>
            <b>Acceso para pacientes</b><br>Escanee este código con la cámara del celular para abrir el documento.
        </p>
        <input readonly value="<?= limpiar($url) ?>" onclick="this.select()">
    </div>
    <a class="boton" href="<?= '../documentos/'.rawurlencode($d['archivo']) ?>" target="_blank"> Abrir PDF</a> <br> <br> <a href="documentos.php"> Volver</a>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
