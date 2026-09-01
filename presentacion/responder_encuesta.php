<?php
/**
 * Respuesta pública de una encuesta.
 * Solo permite responder encuestas que se encuentren activas.
 */
require_once __DIR__ . '/../logica/funciones.php';
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? $_POST['encuesta_id'] ?? 0);
$s = $conexion->prepare("SELECT * FROM encuestas_config WHERE id=? AND activa=1");
$s->execute([$id]);
$encuesta = $s->fetch(PDO::FETCH_ASSOC);
if (!$encuesta) die('Encuesta no encontrada.');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $punt = (int)($_POST['puntaje'] ?? 0);
    $coment = trim($_POST['comentario'] ?? '');
    if ($punt<1 || $punt>5) {
        mensaje('Seleccione una puntuación.', 'error');
    }
    else {
        $s = $conexion->prepare('INSERT INTO encuestas(encuesta_id,puntaje,comentario) VALUES(?,?,?)');
        $s->execute([$id, $punt, $coment]);
        mensaje('¡Muchas gracias por su respuesta!');
        header('Location: portal.php');
        exit;
    }
}
 ?> <!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            <?= limpiar($encuesta['nombre']) ?>
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <link rel="stylesheet" href="css/portal.css">
    </head>
    <body>
        <main class="container py-4" style="max-width:750px">
            <div class="card-accesible p-4">
                <h1>
                    <?= limpiar($encuesta['nombre']) ?>
                </h1>
                <p>
                    <?= nl2br(limpiar($encuesta['descripcion'])) ?>
                </p>
                <form method="post">
                    <input type="hidden" name="encuesta_id" value="<?= $id ?>">
                    <label class="form-label fw-bold">
                        ¿Qué tan conforme está? (1 a 5)
                    </label>
                    <select class="form-select form-select-lg mb-3" name="puntaje" required>
                        <option value=""> Elegir...</option> <?php
for ($i = 1;$i <= 5;$i++) :
 ?> <option value="<?= $i ?>"> <?= $i ?> - <?= $i === 1 ? 'Muy disconforme' : ($i === 5 ? 'Muy conforme' : '') ?></option> <?php
endfor;
 ?>
                    </select>
                    <label class="form-label fw-bold">
                        Comentario (opcional)
                    </label>
                    <textarea class="form-control form-control-lg mb-3" name="comentario" rows="4" maxlength="500"></textarea>
                    <button class="btn btn-primary btn-lg">
                        Enviar respuesta
                    </button>
                    <a class="btn btn-secondary btn-lg" href="../portal.php"> Volver</a>
                </form>
            </div>
        </main>
    </body>
</html>
