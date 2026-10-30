<?php
/** Restablecimiento local de contraseña con usuario y cédula registrados. */
require_once __DIR__.'/../logica/funciones.php';
require_once __DIR__.'/../datos/UsuarioDAO.php';
iniciarSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $captchaOk = validarCaptcha($_POST['captcha'] ?? null);
    $ahora = time();
    if (!isset($_SESSION['recuperacion_bloqueo_hasta'])) $_SESSION['recuperacion_bloqueo_hasta'] = 0;
    $bloqueado = $ahora < (int)$_SESSION['recuperacion_bloqueo_hasta'];
    $usuario = is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '';
    $cedula = is_string($_POST['cedula'] ?? null) ? trim($_POST['cedula']) : '';
    $clave = is_string($_POST['clave'] ?? null) ? $_POST['clave'] : '';
    $confirmacion = is_string($_POST['confirmacion'] ?? null) ? $_POST['confirmacion'] : '';
    $cambioRealizado = false;

    if (!$bloqueado && $captchaOk && $clave !== '' && strlen($clave) >= 8 && hash_equals($clave, $confirmacion)) {
        try {
            $cambioRealizado = (new UsuarioDAO($pdo))->restablecerClavePorUsuarioYCedula($usuario, $cedula, $clave);
        } catch (PDOException $e) {
            error_log('Error en restablecimiento de contraseña SIGSM: '.$e->getMessage());
        }
    }

    // Mismo mensaje para datos correctos e incorrectos para no confirmar cuentas existentes.
    if (!$cambioRealizado) {
        $_SESSION['recuperacion_intentos'] = (int)($_SESSION['recuperacion_intentos'] ?? 0) + 1;
        if ($_SESSION['recuperacion_intentos'] >= 5) {
            $_SESSION['recuperacion_bloqueo_hasta'] = $ahora + 900;
            $_SESSION['recuperacion_intentos'] = 0;
        }
    } else {
        $_SESSION['recuperacion_intentos'] = 0;
    }
    header('Location: recuperar.php?resultado=1');
    exit;
}

$captcha = preguntaCaptcha();
$titulo = 'Recuperar contraseña';
require __DIR__.'/vistas/encabezado.php';
?>
<section class="tarjeta login">
    <h1>Recuperar contraseña</h1>
    <p>Ingrese el usuario y la cédula registrados para establecer una contraseña nueva.</p>
    <div class="ayuda">Por seguridad, si no puede recuperar el acceso, solicite ayuda al administrador del sistema.</div>
    <?php if (isset($_GET['resultado'])): ?>
        <div class="mensaje bien">Si los datos ingresados coinciden con una cuenta activa, la contraseña quedó actualizada. Vuelva al inicio e intente ingresar.</div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <?= campoCsrf() ?>
        <label for="usuario">Nombre de usuario</label>
        <input id="usuario" name="usuario" maxlength="50" required autocomplete="username">
        <label for="cedula">Cédula</label>
        <input id="cedula" name="cedula" maxlength="20" required autocomplete="off">
        <label for="clave">Nueva contraseña</label>
        <div class="password-field">
            <input id="clave" name="clave" type="password" minlength="8" required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="clave" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button>
        </div>
        <label for="confirmacion">Repita la contraseña</label>
        <div class="password-field">
            <input id="confirmacion" name="confirmacion" type="password" minlength="8" required autocomplete="new-password">
            <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="confirmacion" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button>
        </div>
        <label for="captcha">Verificación: ¿cuánto es <?= limpiar($captcha) ?>?</label>
        <input id="captcha" name="captcha" type="number" required inputmode="numeric" autocomplete="off">
        <button type="submit">Actualizar contraseña</button>
    </form>
    <p><a href="index.php">Volver al inicio de sesión</a></p>
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
<?php require __DIR__.'/vistas/pie.php'; ?>
