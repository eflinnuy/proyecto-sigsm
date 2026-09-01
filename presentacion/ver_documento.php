<?php
/**
 * Visualización de un documento desde el área administrativa.
 */
require_once __DIR__ . '/../logica/funciones.php';
proteger();
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? 0);
$s = $conexion->prepare('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.id=? AND d.activo=1');
$s->execute([$id]);
$d = $s->fetch(PDO::FETCH_ASSOC);
if (!$d) die('Documento no encontrado.');
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/ver_publico.php?id='.$id;
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
        <div class="cuadro-qr">
            QR
        </div>
        <p>
            <b> QR simulado</b> <br> El paciente puede escanear un QR que lleve a esta dirección:
        </p>
        <input readonly value="<?= limpiar($url) ?>" onclick="this.select()">
    </div>
    <a class="boton" href="<?= '../documentos/'.rawurlencode($d['archivo']) ?>" target="_blank"> Abrir PDF</a> <br> <br> <a href="documentos.php"> Volver</a>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
