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
$captcha = preguntaCaptcha();
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
        <label for="usuario">
            Usuario
        </label>
        <input id="usuario" type="text" name="usuario" required maxlength="50" autocomplete="username">
        <label for="clave">
            Contraseña
        </label>
        <div class="password-field">
            <input id="clave" type="password" name="clave" required autocomplete="current-password">
            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="clave" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button>
        </div>
        <label for="captcha">Verificación: ¿cuánto es <?= limpiar($captcha) ?>?</label>
        <input id="captcha" type="number" name="captcha" required inputmode="numeric" autocomplete="off">
        <button type="submit">
            Ingresar
        </button>
    </form>
    <p><a href="recuperar.php">¿Olvidó su contraseña?</a></p>
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
document.querySelectorAll('.password-toggle').forEach(function (button) {
    button.addEventListener('click', function () {
        const field = document.getElementById(this.getAttribute('aria-controls'));
        const visible = field.type === 'password';
        field.type = visible ? 'text' : 'password';
        this.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        this.setAttribute('aria-pressed', String(visible));
    });
});
</script>
<?php
require __DIR__.'/vistas/pie.php';
 ?>
