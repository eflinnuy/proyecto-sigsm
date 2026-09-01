<?php
/**
 * Pantalla de acceso del personal.
 * Si ya existe una sesión activa, evita volver a mostrar el formulario.
 */
require_once __DIR__.'/../logica/funciones.php';
iniciarSesion();
if (isset($_SESSION['usuario_id'])) {
    header('Location: inicio.php');
    exit;
}
$titulo = 'Acceso administrativo';
require __DIR__.'/vistas/encabezado.php';
 ?>
<!-- Formulario de autenticación del personal. -->
<section class="tarjeta login">
    <h1>
        Acceso del personal
    </h1>
    <p>
        Ingrese para administrar documentos, encuestas y traslados.
    </p>
    <form action="../login.php" method="post">
        <label>
            Usuario
        </label>
        <input type="text" name="usuario" required maxlength="50">
        <label>
            Contraseña
        </label>
        <input type="password" name="clave" required>
        <button type="submit">
            Ingresar
        </button>
    </form>
    <?php
// El parámetro error se utiliza únicamente para mostrar un mensaje genérico.
if (isset($_GET['error'])) :
 ?>
    <div class="mensaje error">
        El usuario o la contraseña no son correctos.
    </div>
    <?php
endif;
 ?>
    <div class="ayuda">
        Este acceso es solamente para el personal autorizado.
    </div>
</section>
<?php
require __DIR__.'/vistas/pie.php';
 ?>
