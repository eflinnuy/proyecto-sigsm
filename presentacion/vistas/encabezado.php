<?php
require_once __DIR__.'/../../logica/funciones.php';
 ?> <!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= limpiar($titulo ?? 'SIGSM') ?>
        </title>
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
                <nav>
                    <a href="inicio.php"> Inicio</a> <a href="documentos.php"> Documentos</a> <a href="encuestas_admin.php"> Encuestas</a> <a href="ambulancias.php"> Traslados</a> <a href="../portal.php" target="_blank"> Portal paciente</a> <a href="../salir.php"> Salir</a>
                </nav>
                <?php
endif;
 ?>
            </div>
        </header>
        <main class="contenedor">
            <?php
mostrarMensaje();
 ?>
