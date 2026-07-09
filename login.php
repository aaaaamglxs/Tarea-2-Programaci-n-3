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
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $errores = [];
    if (empty($correo)) $errores[] = 'El correo electrónico es obligatorio.';
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresa un correo electrónico válido.';
    if (empty($password)) $errores[] = 'La contraseña es obligatoria.';

    if (empty($errores)) {
        $db = obtenerConexion();
        $stmt = $db->prepare('SELECT * FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            iniciarSesion($usuario);
            header('Location: /index.php');
            exit;
        } else {
            $mensaje = 'Correo o contraseña incorrectos.';
            $tipoMensaje = 'mensaje-error';
        }
    } else {
        $mensaje = implode('<br>', $errores);
        $tipoMensaje = 'mensaje-error';
    }
}

$tituloPagina = 'Iniciar Sesión - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/login.css">';
$mostrarNavbar = false;
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="auth-info">
        <div class="info-content">
            <h1>¡Qué bueno verte de nuevo!</h1>
            <p>Retoma tus clases justo donde las dejaste y sigue avanzando en tus proyectos.</p>
            <div class="features">
                <div class="feature-item"><span class="icon">🎯</span><p>Enfócate en tu próxima meta</p></div>
            </div>
        </div>
    </div>
    <div class="auth-form-container">
        <div class="form-header">
            <h2>Iniciar Sesión</h2>
            <p>¿Aún no eres parte? <a href="/registro.php" class="link">Regístrate aquí</a></p>
        </div>
        <?php if ($mensaje): ?><div class="mensaje <?php echo $tipoMensaje; ?>"><?php echo $mensaje; ?></div><?php endif; ?>
        <form action="/login.php" method="POST" class="auth-form" id="form-login" novalidate>
            <div class="input-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required autocomplete="email" value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
            </div>
            <div class="input-group">
                <div class="password-header">
                    <label for="password">Contraseña</label>
                </div>
                <input type="password" id="password" name="password" placeholder="Tu contraseña" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
