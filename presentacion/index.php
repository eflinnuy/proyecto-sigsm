<?php
/**
 * Pantalla de acceso del personal.
 * Si ya existe una sesión activa, evita volver a mostrar el formulario.
 */
require_once __DIR__.'/../logica/funciones.php';
iniciarSesion();
if (isset($_SESSION['usuario_id'])) {
    $rolesPanel = ['admin', 'usuario', 'funcionario', 'enfermero'];
    header('Location: ' . (in_array($_SESSION['rol'] ?? '', $rolesPanel, true) ? 'inicio.php' : '../portal.php'));
    exit;
}
$titulo = 'Acceso administrativo';
$captcha = preguntaCaptcha();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title><?= limpiar($titulo) ?> | SIGSM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login_sistema.css">
</head>
<body class="login-page">
<main class="login-wrapper row g-0">
    <section class="login-left col-lg-7" aria-label="Hospital de Clínicas">
        <div class="login-overlay">
            <a class="login-brand" href="index.php" aria-label="SIGSM, inicio">
                <img class="hc-logo" src="img/logo-hc.svg" alt="Hospital de Clínicas">
            </a>
            <div class="login-intro">
                <p class="eyebrow">HOSPITAL DE CLÍNICAS</p>
                <h1>Sistema de Gestión de Servicios Médicos</h1>
                <p>Una herramienta para organizar documentos, encuestas y traslados del equipo de salud.</p>
            </div>
            <p class="login-credit">Gestión clara para una atención conectada.</p>
        </div>
    </section>
    <section class="login-right col-lg-5" aria-labelledby="login-title">
        <div class="login-box card shadow-sm">
            <div class="mobile-brand"><img class="hc-logo" src="img/logo-hc.svg" alt="Hospital de Clínicas"></div>
            <p class="form-eyebrow">ACCESO INSTITUCIONAL</p>
            <h2 id="login-title">Bienvenido/a</h2>
            <p class="form-description">Ingresa tus datos para acceder al sistema.</p>
            <?php if (isset($_GET['error'])): ?>
                <div class="login-error" role="alert">El usuario o la contraseña no son correctos. Revisa tus datos e inténtalo nuevamente.</div>
            <?php endif; ?>
            <form action="../login.php" method="post" class="login-form">
                <?= campoCsrf() ?>
                <label class="form-label" for="usuario">Usuario</label>
                <input class="form-control" id="usuario" type="text" name="usuario" required maxlength="50" autocomplete="username" placeholder="Tu usuario">
                <label class="form-label" for="clave">Contraseña</label>
                <div class="password-field">
                    <input class="form-control" id="clave" type="password" name="clave" required autocomplete="current-password" placeholder="Tu contraseña">
                    <button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="clave" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <label class="form-label" for="captcha">Verificación: ¿cuánto es <?= limpiar($captcha) ?>?</label>
                <input class="form-control" id="captcha" type="number" name="captcha" required inputmode="numeric" autocomplete="off" placeholder="Escribe el resultado">
                <button class="submit-button btn btn-primary" type="submit">Ingresar al sistema</button>
            </form>
            <a class="recovery-link" href="recuperar.php">¿Olvidaste tu contraseña?</a>
            <div class="access-note"><span aria-hidden="true">●</span> Acceso exclusivo para personal autorizado</div>
            <p class="login-footer">SIGSM · Proyecto de Egreso 2026</p>
        </div>
    </section>
</main>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
