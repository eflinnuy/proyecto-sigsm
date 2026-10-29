<?php
/**
 * Pantalla de acceso del personal.
 * Si ya existe una sesión activa, evita volver a mostrar el formulario.
 */
require_once __DIR__.'/../logica/funciones.php';
iniciarSesion();
if (isset($_SESSION['usuario_id'])) {
    $rolesPanel = ['admin', 'usuario', 'funcionario'];
    header('Location: ' . (in_array($_SESSION['rol'] ?? '', $rolesPanel, true) ? 'inicio.php' : '../portal.php'));
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
        <?= campoCsrf() ?>
        <label>
            Usuario
        </label>
        <input type="text" name="usuario" required maxlength="50">
        <label for="clave">
            Contraseña
        </label>
        <input id="clave" type="password" name="clave" required autocomplete="current-password">
        <button id="alternar-clave" class="secundario" type="button" aria-controls="clave" aria-pressed="false">Mostrar contraseña</button>
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
<script>
document.getElementById('alternar-clave').addEventListener('click', function () {
    const campo = document.getElementById('clave');
    const visible = campo.type === 'password';
    campo.type = visible ? 'text' : 'password';
    this.textContent = visible ? 'Ocultar contraseña' : 'Mostrar contraseña';
    this.setAttribute('aria-pressed', String(visible));
});
</script>
<?php
require __DIR__.'/vistas/pie.php';
 ?>
