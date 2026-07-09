<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/conexion.php';

if (usuarioLogueado()) {
    header('Location: /index.php');
    exit;
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $terminos = isset($_POST['terminos']);

    $errores = [];
    if (empty($nombre)) $errores[] = 'El nombre completo es obligatorio.';
    if (empty($correo)) $errores[] = 'El correo electrónico es obligatorio.';
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresa un correo electrónico válido.';
    if (strlen($password) < 8) $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    if (!$terminos) $errores[] = 'Debes aceptar los Términos de Servicio.';

    if (empty($errores)) {
        $db = obtenerConexion();
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            $mensaje = 'Este correo ya está registrado. <a href="/login.php" class="link">Inicia sesión</a>.';
            $tipoMensaje = 'mensaje-error';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nombre, $correo, $hash, 'estudiante']);

            $stmt = $db->prepare('SELECT * FROM usuarios WHERE correo = ?');
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                iniciarSesion($usuario);
                header('Location: /index.php');
                exit;
            }
            $mensaje = 'Cuenta creada. <a href="/login.php" class="link">Inicia sesión</a>.';
            $tipoMensaje = 'mensaje-exito';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipoMensaje = 'mensaje-error';
    }
}

$tituloPagina = 'Registro - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/registro.css">';
$mostrarNavbar = false;
include __DIR__ . '/includes/header.php';
?>

<div class="register-container">
    <div class="register-info">
        <div class="info-content">
            <h1>Comienza tu futuro hoy</h1>
            <p>Accede a cursos prácticos, proyectos reales y certificaciones diseñadas para impulsar tu carrera profesional.</p>
            <div class="features">
                <div class="feature-item"><span class="icon">✨</span><p>Aprende a tu propio ritmo</p></div>
                <div class="feature-item"><span class="icon">📱</span><p>Optimizado para cualquier dispositivo</p></div>
            </div>
        </div>
    </div>
    <div class="register-form-container">
        <div class="form-header">
            <h2>Crear cuenta</h2>
            <p>¿Ya tienes una cuenta? <a href="/login.php" class="link">Inicia sesión</a></p>
        </div>
        <?php if ($mensaje): ?><div class="mensaje <?php echo $tipoMensaje; ?>"><?php echo $mensaje; ?></div><?php endif; ?>
        <form action="/registro.php" method="POST" class="register-form" id="form-registro" novalidate>
            <div class="input-group">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
            </div>
            <div class="input-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="terminos" name="terminos" required>
                <label for="terminos">Acepto los <a href="#" class="link">Términos de Servicio</a> y la <a href="#" class="link">Política de Privacidad</a></label>
            </div>
            <button type="submit" class="btn-submit">Crear mi cuenta</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
