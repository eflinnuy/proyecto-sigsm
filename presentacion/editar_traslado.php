<?php
/**
 * Edición de traslados.
 * Permite actualizar los datos del traslado seleccionado.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? 0);
$edit = $id>0;
$dato = null;
if ($edit) {
    $s = $conexion->prepare('SELECT * FROM traslados WHERE id=?');
    $s->execute([$id]);
    $dato = $s->fetch(PDO::FETCH_ASSOC);
    if (!$dato) die('Traslado no encontrado.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = ['paciente', 'chofer', 'enfermero', 'vehiculo', 'origen', 'destino', 'salida', 'llegada', 'estado'];
    $v = [];
    foreach ($campos as $c) $v[$c] = trim($_POST[$c] ?? '');
    if ($v['paciente'] === '' || $v['chofer'] === '' || $v['enfermero'] === '' || $v['origen'] === '' || $v['destino'] === '') mensaje('Completa los datos obligatorios.', 'error');
    else {
        if ($edit) {
            $s = $conexion->prepare('UPDATE traslados SET paciente=?,chofer=?,enfermero=?,vehiculo=?,origen=?,destino=?,salida=?,llegada=?,estado=? WHERE id=?');
            $s->execute([$v['paciente'], $v['chofer'], $v['enfermero'], $v['vehiculo'], $v['origen'], $v['destino'], $v['salida'] ?: null, $v['llegada'] ?: null, $v['estado'], $id]);
            mensaje('Traslado actualizado.');
        } else {
            $s = $conexion->prepare('INSERT INTO traslados(paciente,chofer,enfermero,vehiculo,origen,destino,salida,llegada,estado) VALUES(?,?,?,?,?,?,?,?,?)');
            $s->execute([$v['paciente'], $v['chofer'], $v['enfermero'], $v['vehiculo'], $v['origen'], $v['destino'], $v['salida'] ?: null, $v['llegada'] ?: null, $v['estado']]);
            mensaje('Traslado guardado.');
        }
        header('Location: ambulancias.php');
        exit;
    }
}
$titulo = $edit ? 'Editar traslado' : 'Nuevo traslado';
require __DIR__ . '/vistas/encabezado.php';
$d = $dato ?: ['paciente' => '', 'chofer' => '', 'enfermero' => '', 'vehiculo' => '', 'origen' => 'Hospital de Clínicas', 'destino' => '', 'salida' => '', 'llegada' => '', 'estado' => 'Solicitado'];
 ?>
<section class="tarjeta">
    <h1>
        <?= $edit ? 'Editar' : 'Nuevo' ?> traslado
    </h1>
    <form method="post">
        <label>
            Paciente o elemento *
        </label>
        <input name="paciente" value="<?= limpiar($d['paciente']) ?>" required>
        <label>
            Chofer *
        </label>
        <input name="chofer" value="<?= limpiar($d['chofer']) ?>" required>
        <label>
            Enfermero *
        </label>
        <input name="enfermero" value="<?= limpiar($d['enfermero']) ?>" required>
        <label>
            Vehículo
        </label>
        <input name="vehiculo" value="<?= limpiar($d['vehiculo']) ?>" placeholder="Ambulancia 01">
        <label>
            Origen *
        </label>
        <input name="origen" value="<?= limpiar($d['origen']) ?>" required>
        <label>
            Destino *
        </label>
        <input name="destino" value="<?= limpiar($d['destino']) ?>" required>
        <label>
            Hora de salida
        </label>
        <input type="datetime-local" name="salida" value="<?= $d['salida'] ? date('Y-m-d\\TH:i', strtotime($d['salida'])) : '' ?>">
        <label>
            Hora de llegada
        </label>
        <input type="datetime-local" name="llegada" value="<?= $d['llegada'] ? date('Y-m-d\\TH:i', strtotime($d['llegada'])) : '' ?>">
        <label>
            Estado
        </label>
        <select name="estado">
    <?php
    foreach (['Solicitado', 'En camino', 'Realizado', 'Cancelado'] as $e) :
    ?>
        <option <?= $d['estado'] === $e ? 'selected' : '' ?>>
            <?= $e ?>
        </option>
    <?php
    endforeach;
    ?>
</select>
        <button>
            Guardar
        </button>
    </form>
    <a href="ambulancias.php"> Volver</a>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
