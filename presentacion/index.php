<?php
require_once __DIR__.'/../logica/funciones.php'; iniciarSesion();
if(isset($_SESSION['usuario_id'])){header('Location: inicio.php');exit;}
$titulo='Acceso administrativo'; require __DIR__.'/vistas/encabezado.php';
?><section class="tarjeta login"><h1>Acceso del personal</h1><p>Ingrese para administrar documentos, encuestas y traslados.</p><form action="../login.php" method="post"><label>Usuario</label><input type="text" name="usuario" required maxlength="50"><label>Contraseña</label><input type="password" name="clave" required><button type="submit">Ingresar</button></form><?php if(isset($_GET['error'])):?><div class="mensaje error">El usuario o la contraseña no son correctos.</div><?php endif;?><div class="ayuda">Este acceso es solamente para el personal autorizado.</div></section><?php require __DIR__.'/vistas/pie.php'; ?>
