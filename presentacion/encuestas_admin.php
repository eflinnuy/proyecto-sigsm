<?php
/**
 * Administración de encuestas.
 * Muestra las encuestas configuradas y permite gestionar su estado.
 */
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre'] ?? '');
        $desc = trim($_POST['descripcion'] ?? '');
        if ($nombre === '') {
            mensaje('Escriba el nombre de la encuesta.', 'error');
        } else {
            $s = $conexion->prepare('INSERT INTO encuestas_config(nombre,descripcion,activa) VALUES(?,?,1)');
            $s->execute([$nombre, $desc]);
            mensaje('Encuesta creada.');
            header('Location: encuestas_admin.php');
            exit;
        }
    }
    if ($accion === 'cambiar') {
        $id = (int) $_POST['id'];
        $s = $conexion->prepare('UPDATE encuestas_config SET activa=IF(activa=1,0,1) WHERE id=?');
        $s->execute([$id]);
        mensaje('Estado de la encuesta actualizado.');
        header('Location: encuestas_admin.php');
        exit;
    }
}
$lista = $conexion->query("SELECT e.*,COUNT(r.id) respuestas,ROUND(AVG(r.puntaje),2) promedio FROM encuestas_config e LEFT JOIN encuestas r ON r.encuesta_id=e.id GROUP BY e.id ORDER BY e.id DESC")->fetchAll(PDO::FETCH_ASSOC);
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
        <input type="hidden" name="accion" value="crear">
        <label>
            Nombre
        </label>
        <input name="nombre" maxlength="150" required placeholder="Ej.: Evaluar atención general">
        <label>
            Descripción
        </label>
        <textarea name="descripcion" rows="3" maxlength="500" placeholder="Explique brevemente qué se quiere evaluar."></textarea>
        <button>
            Crear encuesta
        </button>
    </form>
</section>
<section class="tarjeta">
    <h2>
        Encuestas creadas
    </h2>
    <div class="tabla-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        Nombre
                    </th>
                    <th>
                        Estado
                    </th>
                    <th>
                        Respuestas
                    </th>
                    <th>
                        Promedio
                    </th>
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
                        <?= limpiar($e['nombre']) ?> <br> <small> <?= limpiar($e['descripcion']) ?></small>
                    </td>
                    <td>
                        <?= $e['activa'] ? 'Activa' : 'Pausada' ?>
                    </td>
                    <td>
                        <?= (int) $e['respuestas'] ?>
                    </td>
                    <td>
                        <?= $e['promedio'] ?? '-' ?>
                    </td>
                    <td>
                        <form method="post">
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
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
