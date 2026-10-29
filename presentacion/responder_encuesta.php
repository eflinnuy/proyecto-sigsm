<?php
/**
 * Respuesta pública de una encuesta.
 * Solo permite responder encuestas que se encuentren activas.
 */
require_once __DIR__ . '/../logica/funciones.php';
require_once __DIR__ . '/../datos/conexion.php';
iniciarSesion();

$id = (int)($_GET['id'] ?? $_POST['encuesta_id'] ?? 0);
if (empty($_SESSION['survey_token']) || !is_string($_SESSION['survey_token'])) {
    $_SESSION['survey_token'] = bin2hex(random_bytes(32));
}

$s = $pdo->prepare("SELECT * FROM encuestas_config WHERE id=? AND activa=1");
$s->execute([$id]);
$encuesta = $s->fetch(PDO::FETCH_ASSOC);
if (!$encuesta) die('Encuesta no encontrada.');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $envio = $_POST['survey_token'] ?? '';
    $tokenSesion = $_SESSION['survey_token'] ?? null;

    if (!is_string($tokenSesion) || !is_string($envio) || !hash_equals($tokenSesion, $envio)) {
        http_response_code(409);
        exit('La respuesta ya fue enviada o el formulario venció.');
    }
    $punt = filter_var($_POST['puntaje'] ?? null, FILTER_VALIDATE_INT);
    $punt = $punt === false ? 0 : $punt;
    $coment = trim($_POST['comentario'] ?? '');
    if (mb_strlen($coment, 'UTF-8') > 500) $coment = '';
    if ($punt<1 || $punt>5) {
        mensaje('Seleccione una puntuación.', 'error');
    }
    else {
        try {
            $s = $pdo->prepare('INSERT INTO encuestas(encuesta_id,puntaje,comentario,token_envio) VALUES(?,?,?,?)');
            $s->execute([$id, $punt, $coment, $_SESSION['survey_token']]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) !== 1062) throw $e;
            mensaje('Ya se registró una respuesta desde esta sesión.', 'error');
            header('Location: ../portal.php'); exit;
        }
        $_SESSION['survey_token'] = bin2hex(random_bytes(32));
        mensaje('La encuesta fue enviada exitosamente.');
        header('Location: ../portal.php');
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
                    <input type="hidden" name="survey_token" value="<?= limpiar($_SESSION['survey_token']) ?>">
                    <?= campoCsrf() ?>
                    <label class="form-label fw-bold">
                        <?= limpiar($encuesta['pregunta'] ?? '¿Cómo valora el servicio recibido?') ?>
                    </label>
                    <select class="form-select form-select-lg mb-3" name="puntaje" required>
                        <option value=""> Elegir...</option> <?php
for ($i = 1;$i <= 5;$i++) :
 ?> <option value="<?= $i ?>"> <?= $i ?> - <?= [1 => 'Muy mala', 2 => 'Mala', 3 => 'Regular', 4 => 'Buena', 5 => 'Muy buena'][$i] ?></option> <?php
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
