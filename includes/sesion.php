<?php
session_start();

function iniciarSesion($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_correo'] = $usuario['correo'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
}

function cerrarSesion() {
    session_unset();
    session_destroy();
}

function usuarioLogueado() {
    if (isset($_SESSION['usuario_id'])) {
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'correo' => $_SESSION['usuario_correo'],
            'rol' => $_SESSION['usuario_rol'],
        ];
    }
    return null;
}

function requiereLogin() {
    if (!usuarioLogueado()) {
        header('Location: /login.php');
        exit;
    }
}

function esAdmin() {
    $u = usuarioLogueado();
    return $u && $u['rol'] === 'admin';
}

function requiereAdmin() {
    requiereLogin();
    if (!esAdmin()) {
        header('Location: /index.php');
        exit;
    }
}
