<?php
/**
 * Panel administrativo.
 * Solo los administradores pueden acceder; los indicadores se obtienen
 * directamente de la base de datos y luego se presentan en tarjetas.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
// Indicadores principales del sistema.
$totalDocs = $conexion->query("SELECT COUNT(*) FROM documentos WHERE activo=1")->fetchColumn();
$totalTras = $conexion->query("SELECT COUNT(*) FROM traslados")->fetchColumn();
$totalEncuestas = $conexion->query("SELECT COUNT(*) FROM encuestas_config")->fetchColumn();
$titulo = 'Panel administrativo';
require __DIR__ . '/vistas/encabezado.php';
 ?>
<!-- Encabezado del panel administrativo. -->
<section class="hero">
    <h1>
        Panel administrativo
    </h1>
    <p>
        Desde aquí puede administrar la información que verá el paciente.
    </p>
</section>
<!-- Resumen de las tres áreas administrativas. -->
<div class="tarjetas">
    <div class="tarjeta">
        <h2>
            Documentos
        </h2>
        <p class="numero">
            <?= $totalDocs ?>
        </p>
        <a class="boton" href="documentos.php"> Administrar documentos</a>
    </div>
    <div class="tarjeta">
        <h2>
            Encuestas
        </h2>
        <p class="numero">
            <?= $totalEncuestas ?>
        </p>
        <a class="boton" href="encuestas_admin.php"> Administrar encuestas</a>
    </div>
    <div class="tarjeta">
        <h2>
            Traslados
        </h2>
        <p class="numero">
            <?= $totalTras ?>
        </p>
        <a class="boton" href="ambulancias.php"> Administrar traslados</a>
    </div>
</div>
<!-- Acceso rápido al portal público. -->
<div class="tarjeta">
    <h2>
        Portal del paciente
    </h2>
    <p>
        Esta es la página que puede abrir el paciente sin usuario ni contraseña.
    </p>
    <a class="boton" href="../portal.php" target="_blank">Abrir portal</a>
</div>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
