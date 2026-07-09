<?php
require_once __DIR__ . '/includes/sesion.php';
cerrarSesion();
header('Location: /index.php');
exit;
