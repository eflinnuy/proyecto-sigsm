<?php
/**
 * Administración de encuestas.
 * Muestra las encuestas configuradas y permite gestionar su estado.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloPersonal();
require_once __DIR__ . '/../datos/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre'] ?? '');
        $pregunta = trim($_POST['pregunta'] ?? '');
        $desc = trim($_POST['descripcion'] ?? '');
        $servicioId = filter_var($_POST['servicio_id'] ?? '', FILTER_VALIDATE_INT) ?: null;
        if ($nombre === '' || $pregunta === '') {
            mensaje('Escriba el nombre de la encuesta.', 'error');
        } else {
            $s = $pdo->prepare('INSERT INTO encuestas_config(nombre,pregunta,descripcion,servicio_id,activa) VALUES(?,?,?,?,1)');
            $s->execute([$nombre, $pregunta, $desc, $servicioId]);
            mensaje('Encuesta creada.');
            header('Location: encuestas_admin.php');
            exit;
        }
    }
    if ($accion === 'cambiar') {
        $id = (int) $_POST['id'];
        $s = $pdo->prepare('UPDATE encuestas_config SET activa=IF(activa=1,0,1) WHERE id=?');
        $s->execute([$id]);
        mensaje('Estado de la encuesta actualizado.');
        header('Location: encuestas_admin.php');
        exit;
    }
}
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT e.*,sv.nombre AS servicio,COUNT(r.id) respuestas,ROUND(AVG(r.puntaje),2) promedio,SUM(r.puntaje=1) p1,SUM(r.puntaje=2) p2,SUM(r.puntaje=3) p3,SUM(r.puntaje=4) p4,SUM(r.puntaje=5) p5 FROM encuestas_config e LEFT JOIN servicios sv ON sv.id=e.servicio_id LEFT JOIN encuestas r ON r.encuesta_id=e.id';
if ($q !== '') { $s=$pdo->prepare($sql.' WHERE e.nombre LIKE ? OR sv.nombre LIKE ? GROUP BY e.id,sv.nombre ORDER BY e.id DESC'); $s->execute(["%$q%","%$q%"]); } else { $s=$pdo->query($sql.' GROUP BY e.id,sv.nombre ORDER BY e.id DESC'); }
$lista=$s->fetchAll(PDO::FETCH_ASSOC);
$respuestas = $pdo->query(
    'SELECT r.creada_en, r.puntaje, r.comentario, e.nombre AS encuesta
     FROM encuestas r
     INNER JOIN encuestas_config e ON e.id = r.encuesta_id
     ORDER BY r.creada_en DESC'
)->fetchAll(PDO::FETCH_ASSOC);
$servicios=$pdo->query('SELECT id,nombre FROM servicios ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
$titulo = 'Administrar encuestas';
require __DIR__ . '/vistas/encabezado.php';
 ?>
<div class="cabecera">
    <div>
        <h1>
            Encuestas
        </h1>
        <p>
            Crear las encuestas que aparecerán en el Portal del Paciente.
        </p>
    </div>
</div>
<section class="tarjeta">
    <h2>
        Nueva encuesta
    </h2>
    <form method="post">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="crear">
        <label>
            Nombre
        </label>
        <input name="nombre" maxlength="150" required placeholder="Ej.: Evaluar atención general">
        <label>
            Pregunta de valoración
        </label>
        <input name="pregunta" maxlength="250" required placeholder="¿Cómo valora la atención recibida?">
        <label>
            Descripción
        </label>
        <textarea name="descripcion" rows="3" maxlength="500" placeholder="Explique brevemente qué se quiere evaluar."></textarea>
        <label>Servicio</label>
        <select name="servicio_id"><option value="">General</option><?php foreach ($servicios as $servicio): ?><option value="<?= (int)$servicio['id'] ?>"><?= limpiar($servicio['nombre']) ?></option><?php endforeach; ?></select>
        <button>
            Crear encuesta
        </button>
    </form>
</section>
<section class="tarjeta">
    <h2>
        Encuestas creadas
    </h2>
    <form method="get" class="buscar"><input name="q" value="<?= limpiar($q) ?>" placeholder="Buscar por título o servicio"><button>Buscar</button></form>
    <div class="tabla-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        Nombre
                    </th>
                    <th>Servicio</th>
                    <th>
                        Estado
                    </th>
                    <th>
                        Respuestas
                    </th>
                    <th>
                        Promedio
                    </th>
                    <th>Distribución 1–5</th>
                    <th>
                        Acción
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
foreach ($lista as $e) :
 ?>
                <tr>
                    <td>
                        <?= limpiar($e['nombre']) ?> <br><small><?= limpiar($e['pregunta'] ?? '') ?></small><br><small><?= limpiar($e['descripcion']) ?></small>
                    </td>
                    <td><?= limpiar($e['servicio'] ?? 'General') ?></td>
                    <td>
                        <?= $e['activa'] ? 'Activa' : 'Pausada' ?>
                    </td>
                    <td>
                        <?= (int) $e['respuestas'] ?>
                    </td>
                    <td><?= $e['promedio'] ?? '-' ?></td>
                    <td><?php for ($puntaje=1;$puntaje<=5;$puntaje++): ?><?= $puntaje ?>: <?= (int)($e['p'.$puntaje] ?? 0) ?><?= $puntaje<5 ? ' · ' : '' ?><?php endfor; ?></td>
                    <td>
                        <form method="post">
        <?= campoCsrf() ?>
                            <input type="hidden" name="accion" value="cambiar"> <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <button class="secundario">
                                <?= $e['activa'] ? 'Pausar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php
endforeach;
 ?>
            </tbody>
        </table>
    </div>
</section>
<section class="tarjeta">
    <h2>Respuestas recibidas</h2>
    <div class="tabla-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Encuesta</th>
                    <th>Puntaje</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($respuestas as $r): ?>
                    <tr>
                        <td><?= limpiar($r['creada_en']) ?></td>
                        <td><?= limpiar($r['encuesta']) ?></td>
                        <td><?= (int)$r['puntaje'] ?> de 5</td>
                        <td><?= limpiar($r['comentario'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$respuestas): ?>
                    <tr>
                        <td colspan="4">Todavía no hay respuestas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
