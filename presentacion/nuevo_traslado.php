<?php
/**
 * Alta de traslados.
 * Recibe los datos del formulario y delega la validación/persistencia
 * a las capas correspondientes.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloTraslados();
require_once __DIR__ . '/../datos/conexion.php';
$id = 0;
$edit = false;
$dato = null;
if ($edit) {
    $s = $pdo->prepare('SELECT * FROM traslados WHERE id=?');
    $s->execute([$id]);
    $dato = $s->fetch(PDO::FETCH_ASSOC);
    if (!$dato) die('Traslado no encontrado.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $campos = ['paciente', 'chofer', 'enfermero', 'vehiculo', 'origen', 'destino', 'salida', 'llegada', 'estado'];
    $v = [];
    foreach ($campos as $c) $v[$c] = trim($_POST[$c] ?? '');
    if ($v['paciente'] === '' || $v['chofer'] === '' || $v['enfermero'] === '' || $v['origen'] === '' || $v['destino'] === '') mensaje('Completa los datos obligatorios.', 'error');
    else {
        if ($edit) {
            $s = $pdo->prepare('UPDATE traslados SET paciente=?,chofer=?,enfermero=?,vehiculo=?,origen=?,destino=?,salida=?,llegada=?,estado=? WHERE id=?');
            $s->execute([$v['paciente'], $v['chofer'], $v['enfermero'], $v['vehiculo'], $v['origen'], $v['destino'], $v['salida'] ?: null, $v['llegada'] ?: null, $v['estado'], $id]);
            mensaje('Traslado actualizado.');
        } else {
            $s = $pdo->prepare('INSERT INTO traslados(paciente,chofer,enfermero,vehiculo,origen,destino,salida,llegada,estado) VALUES(?,?,?,?,?,?,?,?,?)');
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
<section class="card shadow-sm tarjeta">
    <h1>
        <?= $edit ? 'Editar' : 'Nuevo' ?> traslado
    </h1>
    <form method="post">
        <?= campoCsrf() ?>
        <label class="form-label">
            Paciente o elemento *
        </label>
        <input class="form-control" name="paciente" value="<?= limpiar($d['paciente']) ?>" required>
        <label class="form-label">
            Chofer *
        </label>
        <input class="form-control" name="chofer" value="<?= limpiar($d['chofer']) ?>" required>
        <label class="form-label">
            Enfermero *
        </label>
        <input class="form-control" name="enfermero" value="<?= limpiar($d['enfermero']) ?>" required>
        <label class="form-label">
            Vehículo
        </label>
        <input class="form-control" name="vehiculo" value="<?= limpiar($d['vehiculo']) ?>" placeholder="Ambulancia 01">
        <label class="form-label">
            Origen *
        </label>
        <input class="form-control" name="origen" value="<?= limpiar($d['origen']) ?>" required>
        <label class="form-label">
            Destino *
        </label>
        <input class="form-control" name="destino" value="<?= limpiar($d['destino']) ?>" required>
        <label class="form-label">
            Hora de salida
        </label>
        <input class="form-control" type="datetime-local" name="salida" value="<?= $d['salida'] ? date('Y-m-d\\TH:i', strtotime($d['salida'])) : '' ?>">
        <label class="form-label">
            Hora de llegada
        </label>
        <input class="form-control" type="datetime-local" name="llegada" value="<?= $d['llegada'] ? date('Y-m-d\\TH:i', strtotime($d['llegada'])) : '' ?>">
        <label class="form-label">
            Estado
        </label>
        <select class="form-select" name="estado">
            <?php
foreach (['Solicitado', 'En camino', 'Realizado', 'Cancelado'] as $e) :
 ?> <option <?= $d['estado'] === $e ? 'selected' : '' ?>> <?= $e ?></option> <?php
endforeach;
 ?>
        </select>
        <button class="btn btn-primary">
            Guardar
        </button>
    </form>
    <a href="ambulancias.php"> Volver</a>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
