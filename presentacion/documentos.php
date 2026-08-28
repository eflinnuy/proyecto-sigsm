<?php
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $conexion->prepare('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.activo=1 AND (d.titulo LIKE ? OR c.nombre LIKE ?) ORDER BY d.id DESC');
    $stmt->execute(["%$q%", "%$q%"]);
} else {
    $stmt = $conexion->query('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.activo=1 ORDER BY d.id DESC');
}
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$titulo = 'Documentos';
require __DIR__ . '/vistas/encabezado.php';
 ?>
<div class="cabecera">
    <div>
        <h1>
            Documentos
        </h1>
        <p>
            Información para pacientes.
        </p>
    </div>
    <?php
if ($_SESSION['rol'] === 'admin') :
 ?> <a class="boton" href="nuevo_documento.php"> + Nuevo documento</a> <?php
endif;
 ?>
</div>
<form class="buscar" method="get">
    <input name="q" value="<?= limpiar($q) ?>" placeholder="Buscar por título o categoría">
    <button>
        Buscar
    </button>
</form>
<div class="tabla-wrap">
    <table>
        <thead>
            <tr>
                <th>
                    Título
                </th>
                <th>
                    Categoría
                </th>
                <th>
                    Archivo
                </th>
                <th>
                    Acceso
                </th>
                <?php
if ($_SESSION['rol'] === 'admin') :
 ?>
                <th>
                    Acciones
                </th>
                <?php
endif;
 ?>
            </tr>
        </thead>
        <tbody>
            <?php
foreach ($docs as $d) :
 ?>
            <tr>
                <td>
                    <?= limpiar($d['titulo']) ?>
                </td>
                <td>
                    <?= limpiar($d['categoria']) ?>
                </td>
                <td>
                    <?= limpiar($d['archivo']) ?>
                </td>
                <td>
                    <a href="ver_documento.php?id=<?= $d['id'] ?>"> Ver / QR</a>
                </td>
                <?php
if ($_SESSION['rol'] === 'admin') :
 ?>
                <td>
                    <a href="editar_documento.php?id=<?= $d['id'] ?>"> Editar</a> | <a onclick="return confirm('¿Eliminar este documento?')" href="eliminar_documento.php?id=<?= $d['id'] ?>"> Eliminar</a>
                </td>
                <?php
endif;
 ?>
            </tr>
            <?php
endforeach;
 ?> <?php
if (!$docs) :
 ?>
            <tr>
                <td colspan="5">
                    No hay documentos.
                </td>
            </tr>
            <?php
endif;
 ?>
        </tbody>
    </table>
</div>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
