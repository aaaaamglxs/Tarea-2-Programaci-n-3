<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/conexion.php';

$cursoId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$db = obtenerConexion();

$stmt = $db->prepare('SELECT * FROM cursos WHERE id = ?');
$stmt->execute([$cursoId]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$curso) { header('Location: /index.php'); exit; }

$stmt = $db->prepare('SELECT * FROM modulos WHERE curso_id = ? ORDER BY numero');
$stmt->execute([$cursoId]);
$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario = usuarioLogueado();
$yaInscrito = false;
if ($usuario) {
    $stmt = $db->prepare('SELECT id, progreso FROM inscripciones WHERE usuario_id = ? AND curso_id = ?');
    $stmt->execute([$usuario['id'], $cursoId]);
    $inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);
    $yaInscrito = $inscripcion ? true : false;
    $progresoActual = $inscripcion ? $inscripcion['progreso'] : 0;
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario && !$yaInscrito) {
    $stmt = $db->prepare('INSERT OR IGNORE INTO inscripciones (usuario_id, curso_id) VALUES (?, ?)');
    $stmt->execute([$usuario['id'], $cursoId]);
    $yaInscrito = true;
    $mensaje = '¡Te has inscrito exitosamente al curso!';
    $tipoMensaje = 'mensaje-exito';
}

$precioTexto = $curso['precio'] > 0 ? '$' . number_format($curso['precio'], 2) . ' USD' : 'Gratis';
$tituloPagina = $curso['titulo'] . ' - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/curso.css">';
include __DIR__ . '/includes/header.php';
?>

<header class="course-hero">
    <div class="hero-decoration"></div>
    <div class="hero-content">
        <span class="hero-badge">Curso Certificado • <?php echo htmlspecialchars($curso['badge']); ?></span>
        <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
        <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
        <div class="hero-meta">
            <span>⏳ <?php echo $curso['duracion_semanas']; ?> Semanas</span>
            <span>📁 <?php echo $curso['num_modulos']; ?> Módulos</span>
            <span>💰 <?php echo $precioTexto; ?></span>
        </div>
    </div>
</header>

<main class="detalle-curso-container">
    <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipoMensaje; ?>"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <section class="descripcion-box">
        <h1>¿Qué aprenderás en este curso?</h1>
        <p><?php echo htmlspecialchars($curso['descripcion']); ?> Este curso intensivo de <?php echo $curso['duracion_semanas']; ?> semanas te llevará desde los conceptos básicos hasta proyectos prácticos reales.</p>
    </section>

    <section class="modulos-container">
        <h3>Contenido del curso:</h3>
        <ul id="lista-modulos" class="lista-modulos">
            <?php foreach ($modulos as $mod): ?>
            <li>
                <strong>Módulo <?php echo $mod['numero']; ?>:</strong> <?php echo htmlspecialchars($mod['titulo']); ?>
                <br><span style="color:var(--text-muted);font-size:0.9rem;"><?php echo htmlspecialchars($mod['descripcion']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <div class="vscode-window">
        <div class="title-bar">
            mini-vscode.html
            <div class="window-controls">
                <div class="close"></div><div class="minimize"></div><div class="maximize"></div>
            </div>
        </div>
        <div class="tabs"><label class="l">index.html</label></div>
        <div class="code-panel">
            <div id="html-panel" class="panel">
                &lt;<span class="keyword">!DOCTYPE</span> html&gt;<br />
                &lt;html lang=<span class="string">"es"</span>&gt;<br />
                &lt;head&gt;<br />
                &nbsp;&nbsp;&lt;<span class="keyword">meta</span> charset=<span class="string">"UTF-8"</span>&gt;<br />
                &nbsp;&nbsp;&lt;<span class="keyword">title</span>&gt;Mi primera página&lt;/<span class="keyword">title</span>&gt;<br />
                &lt;/head&gt;<br />
                &lt;body&gt;<br /><br />
                &nbsp;&nbsp;&lt;<span class="keyword">h1</span>&gt;Hola Mundo&lt;/<span class="keyword">h1</span>&gt;<br />
                &nbsp;&nbsp;&lt;<span class="keyword">p</span>&gt;Esta es mi primera página...&lt;/<span class="keyword">p</span>&gt;<br /><br />
                &lt;/body&gt;<br />&lt;/html&gt;
            </div>
        </div>
    </div>

    <div class="razones-container">
        <h3>¿Por qué elegir este curso?</h3>
        <p>Este curso de <?php echo $curso['duracion_semanas']; ?> semanas está diseñado para que domines <?php echo htmlspecialchars($curso['titulo']); ?> desde cero hasta un nivel profesional.</p>
        <ul class="razones-lista">
            <li><span class="icono">⏰</span><span class="texto">Flexibilidad Total</span></li>
            <li><span class="icono">📜</span><span class="texto">Certificación Oficial</span></li>
            <li><span class="icono">💻</span><span class="texto">Enfoque Práctico</span></li>
            <li><span class="icono">👥</span><span class="texto">Comunidad Exclusiva</span></li>
            <li><span class="icono">🔄</span><span class="texto">Actualización Constante</span></li>
        </ul>
    </div>

    <?php if (!$usuario): ?>
        <a href="/login.php" class="btn-aplicar">Inicia sesión para inscribirte</a>
    <?php elseif ($yaInscrito): ?>
        <div class="btn-aplicar btn-inscrito">✓ Ya estás inscrito</div>
    <?php else: ?>
        <form method="POST">
            <button type="submit" class="btn-aplicar">Inscribirme ahora</button>
        </form>
    <?php endif; ?>

    <a href="/index.php" class="back-link">← Volver al catálogo</a>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
