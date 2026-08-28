<?php
require __DIR__ . '/logica/autenticacion.php';
cerrarSesion(); header('Location: presentacion/index.php'); exit;
