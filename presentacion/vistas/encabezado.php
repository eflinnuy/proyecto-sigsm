<?php
require_once __DIR__.'/../../logica/funciones.php';
 ?> <!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= limpiar($titulo ?? 'SIGSM') ?>
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/estilo.css">
        <script src="js/validar.js" defer>
        </script>
    </head>
    <body>
        <header>
            <div class="barra">
                <a class="logo" href="inicio.php"> SIGSM</a> <?php
if (isset($_SESSION['usuario_id'])) :
 ?>
                <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-controls="menu-panel" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
                <nav class="nav-links" id="menu-panel">
                    <a class="btn btn-primary boton" href="inicio.php">Inicio</a>
                    <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'usuario', 'funcionario'], true)): ?>
                    <a class="btn btn-primary boton" href="documentos.php">Documentos</a>
                    <a class="btn btn-primary boton" href="encuestas_admin.php">Encuestas</a>
                    <?php endif; ?>
                    <a class="btn btn-primary boton" href="ambulancias.php">Traslados</a>
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?><a class="btn btn-primary boton" href="funcionarios.php">Funcionarios</a><?php endif; ?>
                    <a class="btn btn-primary boton" href="../portal.php" target="_blank">Abrir portal</a>
                    <form class="form-salir" action="../salir.php" method="post">
                        <?= campoCsrf() ?><button class="btn btn-primary boton" type="submit">Salir</button></form>
                </nav>
                <?php
endif;
 ?>
            </div>
        </header>
        <script>
        (function () {
            const toggle = document.querySelector('.menu-toggle');
            const menu = document.getElementById('menu-panel');
            if (!toggle || !menu) return;
            function cerrarMenu() {
                menu.classList.remove('abierto');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Abrir menú');
            }
            toggle.addEventListener('click', function () {
                const abierto = this.getAttribute('aria-expanded') !== 'true';
                menu.classList.toggle('abierto', abierto);
                this.setAttribute('aria-expanded', String(abierto));
                this.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
            });
            menu.querySelectorAll('a').forEach(function (enlace) {
                enlace.addEventListener('click', cerrarMenu);
            });
            document.addEventListener('keydown', function (evento) {
                if (evento.key === 'Escape') cerrarMenu();
            });
        })();
        </script>
        <main class="container contenedor py-4">
            <?php
mostrarMensaje();
 ?>
