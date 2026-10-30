<?php
/** Recuperación en dos pasos: validar usuario/cédula y luego permitir el cambio. */
require_once __DIR__.'/../logica/funciones.php';
require_once __DIR__.'/../datos/UsuarioDAO.php';
iniciarSesion();

$ahora = time();
if (isset($_GET['reiniciar'])) {
    unset($_SESSION['recuperacion_usuario_id'], $_SESSION['recuperacion_expira']);
    header('Location: recuperar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $captchaOk = validarCaptcha($_POST['captcha'] ?? null);
    $accion = is_string($_POST['accion'] ?? null) ? $_POST['accion'] : '';
    $bloqueoHasta = (int)($_SESSION['recuperacion_bloqueo_hasta'] ?? 0);
    $bloqueado = $ahora < $bloqueoHasta;
    $dao = new UsuarioDAO($pdo);

    if ($accion === 'verificar' && !$bloqueado && $captchaOk) {
        $usuario = is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '';
        $cedula = is_string($_POST['cedula'] ?? null) ? trim($_POST['cedula']) : '';
        try {
            $cuenta = $dao->buscarIdPorUsuarioYCedula($usuario, $cedula);
        } catch (PDOException $e) {
            error_log('Error verificando identidad para recuperar clave SIGSM: '.$e->getMessage());
            $cuenta = null;
        }

        if ($cuenta) {
            $_SESSION['recuperacion_usuario_id'] = (int)$cuenta['id'];
            $_SESSION['recuperacion_expira'] = $ahora + 600;
            $_SESSION['recuperacion_intentos'] = 0;
            header('Location: recuperar.php?paso=clave');
            exit;
        }

        $_SESSION['recuperacion_intentos'] = (int)($_SESSION['recuperacion_intentos'] ?? 0) + 1;
        if ($_SESSION['recuperacion_intentos'] >= 5) {
            $_SESSION['recuperacion_bloqueo_hasta'] = $ahora + 900;
            $_SESSION['recuperacion_intentos'] = 0;
        }
        header('Location: recuperar.php?error=1');
        exit;
    }

    $idVerificado = (int)($_SESSION['recuperacion_usuario_id'] ?? 0);
    $expira = (int)($_SESSION['recuperacion_expira'] ?? 0);
    if ($accion === 'cambiar' && $captchaOk && $idVerificado > 0 && $ahora <= $expira) {
        $clave = is_string($_POST['clave'] ?? null) ? $_POST['clave'] : '';
        $confirmacion = is_string($_POST['confirmacion'] ?? null) ? $_POST['confirmacion'] : '';
        if (strlen($clave) >= 8 && hash_equals($clave, $confirmacion)) {
            try {
                if ($dao->restablecerClavePorId($idVerificado, $clave)) {
                    unset($_SESSION['recuperacion_usuario_id'], $_SESSION['recuperacion_expira']);
                    $_SESSION['recuperacion_intentos'] = 0;
                    header('Location: recuperar.php?resultado=1');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('Error al restablecer clave SIGSM: '.$e->getMessage());
            }
        }
        header('Location: recuperar.php?paso=clave&error=2');
        exit;
    }

    if ($idVerificado && $ahora > $expira) {
        unset($_SESSION['recuperacion_usuario_id'], $_SESSION['recuperacion_expira']);
    }
    header('Location: recuperar.php?error=1');
    exit;
}

$verificado = !empty($_SESSION['recuperacion_usuario_id'])
    && $ahora <= (int)($_SESSION['recuperacion_expira'] ?? 0);
if (!$verificado) {
    unset($_SESSION['recuperacion_usuario_id'], $_SESSION['recuperacion_expira']);
}
$captcha = preguntaCaptcha();
$titulo = 'Recuperar contraseña';
require __DIR__.'/vistas/encabezado.php';
?>
<section class="tarjeta login">
    <h1>Recuperar contraseña</h1>
    <?php if (isset($_GET['resultado'])): ?>
        <div class="mensaje bien">La contraseña se actualizó. Ya puede volver al inicio e ingresar.</div>
        <p><a href="index.php">Volver al inicio de sesión</a></p>
    <?php elseif (!$verificado): ?>
        <p>Primero confirme su identidad con el nombre de usuario y la cédula registrada en SIGSM.</p>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error">No se pudo validar la información. Revise los datos e inténtelo nuevamente.</div>
        <?php endif; ?>
        <?php if ($ahora < (int)($_SESSION['recuperacion_bloqueo_hasta'] ?? 0)): ?>
            <div class="mensaje error">Se alcanzó el límite de intentos. Espere 15 minutos antes de volver a intentarlo.</div>
        <?php else: ?>
            <form method="post" autocomplete="off">
                <?= campoCsrf() ?>
                <input type="hidden" name="accion" value="verificar">
                <label for="usuario">Nombre de usuario</label>
                <input id="usuario" name="usuario" maxlength="50" required autocomplete="username">
                <label for="cedula">Cédula</label>
                <input id="cedula" name="cedula" maxlength="20" required autocomplete="off">
                <label for="captcha">Verificación: ¿cuánto es <?= limpiar($captcha) ?>?</label>
                <input id="captcha" name="captcha" type="number" required inputmode="numeric" autocomplete="off">
                <button type="submit">Verificar identidad</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>La identidad fue verificada. Ahora puede elegir una contraseña nueva.</p>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error">Revise que ambas contraseñas coincidan, tengan al menos 8 caracteres y que el captcha sea correcto.</div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <?= campoCsrf() ?>
            <input type="hidden" name="accion" value="cambiar">
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
            <button type="submit">Cambiar contraseña</button>
        </form>
        <p><a href="recuperar.php?reiniciar=1">Volver a verificar la identidad</a></p>
    <?php endif; ?>
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
