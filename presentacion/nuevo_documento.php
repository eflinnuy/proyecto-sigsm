<?php
require_once __DIR__ . '/../logica/funciones.php'; soloAdmin(); require_once __DIR__ . '/../datos/conexion.php';
$categorias=$conexion->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
if($_SERVER['REQUEST_METHOD']==='POST'){
 $tituloDoc=trim($_POST['titulo']??'');$cat=(int)($_POST['categoria_id']??0);$descripcion=trim($_POST['descripcion']??'');
 if($tituloDoc===''||$cat===0){mensaje('Completa el título y la categoría.','error');}elseif(empty($_FILES['archivo']['name'])||$_FILES['archivo']['error']!==UPLOAD_ERR_OK){mensaje('Selecciona un archivo PDF.','error');}else{
  $ext=strtolower(pathinfo($_FILES['archivo']['name'],PATHINFO_EXTENSION));
  if($ext!=='pdf'||$_FILES['archivo']['size']>5000000){mensaje('El archivo debe ser PDF y pesar menos de 5 MB.','error');}else{
   $nombre=uniqid('doc_').'.pdf';$ruta='../documentos/'.$nombre;
   if(move_uploaded_file($_FILES['archivo']['tmp_name'],$ruta)){$s=$conexion->prepare('INSERT INTO documentos(titulo,categoria_id,descripcion,archivo) VALUES(?,?,?,?)');$s->execute([$tituloDoc,$cat,$descripcion,$nombre]);mensaje('Documento guardado.');header('Location: documentos.php');exit;}else mensaje('No se pudo guardar el archivo.','error');
  }
 }
}
$titulo='Nuevo documento';require __DIR__ . '/vistas/encabezado.php';
?><section class="tarjeta"><h1>Nuevo documento</h1><form method="post" enctype="multipart/form-data"><label>Título</label><input name="titulo" required maxlength="150"><label>Categoría</label><select name="categoria_id" required><option value="">Elegir...</option><?php foreach($categorias as $c): ?><option value="<?= $c['id'] ?>"><?= limpiar($c['nombre']) ?></option><?php endforeach; ?></select><label>Descripción</label><textarea name="descripcion" rows="5"></textarea><label>Archivo PDF</label><input type="file" name="archivo" accept="application/pdf" required><button>Guardar</button></form><a href="documentos.php">Volver</a></section><?php require __DIR__ . '/vistas/pie.php'; ?>