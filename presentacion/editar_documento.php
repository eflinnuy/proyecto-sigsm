<?php
require_once __DIR__ . '/../logica/funciones.php';
soloAdmin();
require_once __DIR__ . '/../datos/conexion.php';
$id = (int)($_GET['id'] ?? 0);
$s = $conexion->prepare('SELECT * FROM documentos WHERE id=? AND activo=1');
$s->execute([$id]);
$doc = $s->fetch(PDO::FETCH_ASSOC);
if (!$doc) die('Documento no encontrado.');
$cats = $conexion->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = trim($_POST['titulo'] ?? '');
    $cat = (int) $_POST['categoria_id'];
    $desc = trim($_POST['descripcion'] ?? '');
    if ($t === '' || !$cat) mensaje('Completa los datos.', 'error');
    else {
        $nuevo = $doc['archivo'];
        if (!empty($_FILES['archivo']['name']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf' || $_FILES['archivo']['size']>5000000) mensaje('El nuevo archivo debe ser PDF y menor de 5 MB.', 'error');
            else {
                $nuevo = uniqid('doc_').'.pdf';
                if (move_uploaded_file($_FILES['archivo']['tmp_name'], '../documentos/'.$nuevo)) {
                    if (is_file('../documentos/'.$doc['archivo']))@unlink('../documentos/'.$doc['archivo']);
                }
            }
        }
        if (empty($_SESSION['mensaje']) || $_SESSION['mensaje']['tipo'] !== 'error') {
            $u = $conexion->prepare('UPDATE documentos SET titulo=?,categoria_id=?,descripcion=?,archivo=? WHERE id=?');
            $u->execute([$t, $cat, $desc, $nuevo, $id]);
            mensaje('Documento actualizado.');
            header('Location: documentos.php');
            exit;
        }
    }
}
$titulo = 'Editar documento';
require __DIR__ . '/vistas/encabezado.php';
 ?>
<section class="tarjeta">
    <h1>
        Editar documento
    </h1>
    <form method="post" enctype="multipart/form-data">
        <label>
            Título
        </label>
        <input name="titulo" value="<?= limpiar($doc['titulo']) ?>" required>
        <label>
            Categoría
        </label>
        <select name="categoria_id">
            <?php
foreach ($cats as $c) :
 ?> <option value="<?= $c['id'] ?>" <?= $c['id'] == $doc['categoria_id'] ? 'selected' : '' ?>> <?= limpiar($c['nombre']) ?></option> <?php
endforeach;
 ?>
        </select>
        <label>
            Descripción
        </label>
        <textarea name="descripcion" rows="5"> <?= limpiar($doc['descripcion']) ?></textarea>
        <label>
            Reemplazar PDF (opcional)
        </label>
        <input type="file" name="archivo" accept="application/pdf">
        <button>
            Guardar cambios
        </button>
    </form>
    <a href="documentos.php"> Volver</a>
</section>
<?php
require __DIR__ . '/vistas/pie.php';
 ?>
