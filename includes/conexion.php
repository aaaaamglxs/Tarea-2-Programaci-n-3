<?php
function obtenerConexion() {
    $rutaBD = __DIR__ . '/../atelier.db';
    try {
        $db = new PDO('sqlite:' . $rutaBD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA journal_mode=WAL');
        $db->exec('PRAGMA foreign_keys=ON');
        return $db;
    } catch (Exception $e) {
        die('Error al conectar con la base de datos: ' . $e->getMessage());
    }
}
