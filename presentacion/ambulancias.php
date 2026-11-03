<?php
/**
 * Administración de traslados.
 * Permite consultar y gestionar los registros de traslado desde el
 * panel administrativo.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloTraslados();
require_once __DIR__ . '/../datos/conexion.php';
$q = trim($_GET['q'] ?? '');
if ($q) {
    $s = $pdo->prepare('SELECT * FROM traslados WHERE paciente LIKE ? OR destino LIKE ? OR estado LIKE ? ORDER BY id DESC');
    $s->execute(["%$q%", "%$q%", "%$q%"]);
} else {
    $s = $pdo->query('SELECT * FROM traslados ORDER BY id DESC');
}
$lista = $s->fetchAll(PDO::FETCH_ASSOC);
$titulo = 'Traslados';
require __DIR__ . '/vistas/encabezado.php';
 ?>
<div class="cabecera">
    <div>
        <h1>
            Traslados
        </h1>
        <p>
            Seguimiento de ambulancias y otros vehículos.
        </p>
    </div>
    <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'enfermero'], true)): ?><a class="btn btn-primary boton" href="nuevo_traslado.php"> + Nuevo traslado</a><?php endif; ?>
</div>
<form class="buscar">
    <input class="form-control" name="q" value="<?= limpiar($q) ?>" placeholder="Buscar paciente, destino o estado">
    <button class="btn btn-primary">
        Buscar
    </button>
</form>
<div class="tabla-wrap">
    <table class="table table-hover align-middle mb-0">
        <tr>
            <th>
                Paciente / elemento
            </th>
            <th>
                Chofer
            </th>
            <th>
                Enfermero
            </th>
            <th>
                Origen
            </th>
            <th>
                Destino
            </th>
            <th>
                Estado
            </th>
            <th>
                Acción
            </th>
        </tr>
        <?php
foreach ($lista as $x) :
 ?>
        <tr>
            <td>
                <?= limpiar($x['paciente']) ?>
            </td>
            <td>
                <?= limpiar($x['chofer']) ?>
            </td>
            <td>
                <?= limpiar($x['enfermero']) ?>
            </td>
            <td>
                <?= limpiar($x['origen']) ?>
            </td>
            <td>
                <?= limpiar($x['destino']) ?>
            </td>
            <td>
                <span class="estado"> <?= limpiar($x['estado']) ?></span>
            </td>
            <td>
                <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'enfermero'], true)): ?><a href="editar_traslado.php?id=<?= (int)$x['id'] ?>">Actualizar seguimiento</a><?php else: ?>Consulta<?php endif; ?>
            </td>
        </tr>
        <?php
endforeach;
 ?>
    </table>
</div>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
