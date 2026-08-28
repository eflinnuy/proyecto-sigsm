<?php
require_once __DIR__ . '/logica/funciones.php';
require_once __DIR__ . '/datos/DocumentoDAO.php';
require_once __DIR__ . '/datos/EncuestaDAO.php';
require_once __DIR__ . '/datos/conexion.php';
$documentoDAO = new DocumentoDAO($conexion);
$encuestaDAO = new EncuestaDAO($conexion);
$documentos = $documentoDAO->listarActivos();
$encuestas = $encuestaDAO->activas();
$porCategoria = [];
foreach ($documentos as $doc) {
    $porCategoria[$doc['categoria_id']]['nombre'] = $doc['categoria'];
    $porCategoria[$doc['categoria_id']]['documentos'][] = $doc;
}
$titulo = 'Portal del Paciente';
 ?> <!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= limpiar($titulo) ?>
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet"> <link rel="stylesheet" href="presentacion/css/portal.css">
    </head>
    <body>
        <header class="portal-header text-white text-center p-4 shadow-sm">
            <h1 class="fw-bold mb-2">
                Hospital de Clínicas
            </h1>
            <h2 class="fs-4 fw-normal">
                Portal del Paciente
            </h2>
            <p class="mt-3 mb-0 fs-5">
                Bienvenido/a. Elija la información que necesita ver.
            </p>
        </header>
        <main class="container py-4">
            <section id="documentos" class="mb-5">
                <h3 class="fw-bold border-bottom pb-2 mb-4">
                    <i class="bi bi-file-earmark-medical me-3 text-primary"></i> Documentos e Indicaciones
                </h3>
                <p class="fs-5 mb-4">
                    Seleccione una categoría para consultar los documentos disponibles.
                </p>
                <?php
if (!$documentos) :
 ?>
                <div class="alert alert-info">
                    Actualmente no hay documentos disponibles.
                </div>
                <?php
endif;
 ?>
                <div class="accordion" id="acordeonDocumentos">
                    <?php
$n = 0;
foreach ($porCategoria as $cat) : $n++;
$id = 'cat'.$n;
 ?>
                    <div class="accordion-item mb-3 card-accesible">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $id ?>">
                                <?= limpiar($cat['nombre']) ?>
                            </button>
                        </h2>
                        <div id="<?= $id ?>" class="accordion-collapse collapse" data-bs-parent="#acordeonDocumentos">
                            <div class="list-group list-group-flush">
                                <?php
foreach ($cat['documentos'] as $doc) :
 ?> <a href="presentacion/ver_publico.php?id=<?= (int)$doc['id'] ?>" class="list-group-item list-group-item-action"> <i class="bi bi-file-text me-2 text-muted"></i> <?= limpiar($doc['titulo']) ?></a> <?php
endforeach;
 ?>
                            </div>
                        </div>
                    </div>
                    <?php
endforeach;
 ?>
                </div>
            </section>
            <hr class="my-5 border-2 border-secondary opacity-25">
            <section id="encuestas">
                <h3 class="fw-bold border-bottom pb-2 mb-4">
                    <i class="bi bi-ui-checks me-3 text-primary"></i> Encuestas de Satisfacción
                </h3>
                <p class="fs-5 mb-4">
                    Ayúdenos a mejorar. Las respuestas son anónimas.
                </p>
                <div class="d-grid gap-3">
                    <?php
foreach ($encuestas as $e) :
 ?> <a class="btn btn-accesible d-flex justify-content-between align-items-center" href="presentacion/responder_encuesta.php?id=<?= (int)$e['id'] ?>"> <span> <?= limpiar($e['nombre']) ?></span> <i class="bi bi-pencil-square fs-3"></i></a> <?php
endforeach;
 ?> <?php
if (!$encuestas) :
 ?>
                    <div class="alert alert-secondary">
                        No hay encuestas activas en este momento.
                    </div>
                    <?php
endif;
 ?>
                </div>
            </section>
        </main>
        <footer class="bg-dark text-white text-center p-4 mt-5">
            <p class="fs-5 mb-2">
                Hospital de Clínicas
            </p>
            <p class="fs-6 mb-0">
                Portal de información para pacientes
            </p>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
        </script>
    </body>
</html>
